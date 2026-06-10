import os
import uuid
import cv2
import json
import csv
from datetime import datetime
from flask import Flask, render_template, request, redirect, url_for, flash, send_file
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager, UserMixin, login_user, login_required, logout_user, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from werkzeug.utils import secure_filename
from detector import ImageProcessor 
from config import Config


# --- Flask app and config ---
app = Flask(__name__)
app.config.from_object(Config)

db = SQLAlchemy(app)
login_manager = LoginManager(app)
login_manager.login_view = 'login'

# --- Models (keep your original models) ---
class User(UserMixin, db.Model):
    id = db.Column(db.Integer, primary_key=True)
    username = db.Column(db.String(80), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password_hash = db.Column(db.String(120), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

class ImageAnalysis(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)
    filename = db.Column(db.String(255), nullable=False)
    original_path = db.Column(db.String(255), nullable=False)
    processed_path = db.Column(db.String(255))
    defect_count = db.Column(db.Integer, default=0)
    defect_types = db.Column(db.Text)  # JSON string of defect types
    analysis_date = db.Column(db.DateTime, default=datetime.utcnow)
    report_data = db.Column(db.Text)  # JSON string of detailed analysis

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

# --- Detector instance ---
# If you have model files, place them in model/ and Detector will attempt to load them.
MODEL_DIR = app.config.get('MODEL_DIR', 'model')
Y_CFG = os.path.join(MODEL_DIR, app.config.get('YOLO_CFG', 'yolov4-tiny-custom.cfg'))
Y_WEIGHTS = os.path.join(MODEL_DIR, app.config.get('YOLO_WEIGHTS', 'yolov4-tiny-custom_best.weights'))
Y_NAMES = os.path.join(MODEL_DIR, app.config.get('YOLO_NAMES', 'obj.names'))

# instantiate detector (will gracefully fallback to classical if YOLO not loaded)
image_processor = ImageProcessor(
    yolo_cfg=Y_CFG if os.path.exists(Y_CFG) else None,
    yolo_weights=Y_WEIGHTS if os.path.exists(Y_WEIGHTS) else None,
    yolo_names=Y_NAMES if os.path.exists(Y_NAMES) else None,
    yolo_input_size=app.config.get('YOLO_INPUT_SIZE', 640),
    use_cuda=app.config.get('USE_CUDA', False)
)
import json
@app.template_filter('parse_json')
def parse_json_filter(s):
    return json.loads(s)

# --- Helpers ---
def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in app.config.get('ALLOWED_EXTENSIONS', {'png','jpg','jpeg','bmp','tif','tiff'})

def serialize_defect(defect):
    """
    Convert a defect dictionary to a JSON-serializable dict.
    Expected keys in defect: 'type', 'confidence', 'bbox' (tuple), 'area', 'length', 'solidity', 'contour'
    We'll omit raw contour arrays (too large) and keep bbox + properties.
    """
    out = {}
    out['type'] = str(defect.get('type', 'unknown'))
    out['confidence'] = float(defect.get('confidence', 0.0))
    # bbox -> ensure ints
    if 'bbox' in defect and defect['bbox'] is not None:
        bx = defect['bbox']
        try:
            x, y, w, h = int(bx[0]), int(bx[1]), int(bx[2]), int(bx[3])
        except Exception:
            # fallback if bbox stored differently
            x = int(bx[0]); y = int(bx[1]); w = int(bx[2]); h = int(bx[3])
        out['bbox'] = {'x': x, 'y': y, 'w': w, 'h': h}
        out['area'] = int(defect.get('area', w*h))
    else:
        out['bbox'] = None
        out['area'] = float(defect.get('area', 0.0))
    # additional numeric fields
    if 'length' in defect:
        out['length'] = int(defect.get('length', 0))
    if 'solidity' in defect:
        out['solidity'] = float(defect.get('solidity', 0.0))
    return out

# --- Routes ---
@app.route('/')
def index():
    if current_user.is_authenticated:
        return redirect(url_for('dashboard'))
    return redirect(url_for('login'))

@app.route('/login', methods=['GET','POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username','').strip()
        password = request.form.get('password','').strip()
        user = User.query.filter_by(username=username).first()
        if user and check_password_hash(user.password_hash, password):
            login_user(user)
            return redirect(url_for('dashboard'))
        flash('Invalid username or password', 'danger')
    return render_template('login.html')

@app.route('/register', methods=['GET','POST'])
def register():
    if request.method == 'POST':
        username = request.form.get('username','').strip()
        email = request.form.get('email','').strip()
        password = request.form.get('password','')
        if User.query.filter_by(username=username).first():
            flash('Username already exists', 'warning')
            return render_template('register.html')
        user = User(username=username, email=email, password_hash=generate_password_hash(password))
        db.session.add(user)
        db.session.commit()
        flash('Registration successful. Please login.', 'success')
        return redirect(url_for('login'))
    return render_template('register.html')

from collections import Counter

@app.route('/dashboard')
@login_required
def dashboard():
    analyses = ImageAnalysis.query.filter_by(user_id=current_user.id)\
                .order_by(ImageAnalysis.analysis_date.desc()).limit(20).all()

    # ensure defect_types is dict
    for a in analyses:
        if a.defect_types:
            try:
                a.defect_types = json.loads(a.defect_types)
            except Exception:
                a.defect_types = {}
        else:
            a.defect_types = {}

    # --- DEFECT STATS ---
    total_counts = Counter()
    method_counts = Counter()

    for a in analyses:
        # sum defect types
        for k, v in a.defect_types.items():
            total_counts[k] += v

        # count detection method (example, assuming stored in analysis.method)
        if hasattr(a, 'method'):
            method_counts[a.method] += a.defect_count

    # fill missing keys for template
    defect_stats = {
        'cracks': total_counts.get('crack', 0),
        'dents': total_counts.get('dent', 0),
        'corrosion': total_counts.get('corrosion', 0),
        'spalling': total_counts.get('spalling', 0),
        'leaks': total_counts.get('leak', 0),
        'other': total_counts.get('other', 0),
    }

    method_stats = {
        'classical': method_counts.get('classical', 0),
        'yolo': method_counts.get('yolo', 0),
        'custom': method_counts.get('custom', 0)
    }

    return render_template(
        'dashboard.html',
        analyses=analyses,
        defect_stats=defect_stats,
        method_stats=method_stats
    )


@app.route('/upload', methods=['GET','POST'])
@login_required
def upload_image():
    if request.method == 'POST':
        if 'file' not in request.files:
            flash('No file selected', 'warning')
            return redirect(request.url)
        file = request.files['file']
        if file.filename == '':
            flash('No file selected', 'warning')
            return redirect(request.url)
        if file and allowed_file(file.filename):
            filename = secure_filename(file.filename)
            uid = uuid.uuid4().hex[:8]
            unique_filename = f"{current_user.id}_{datetime.now().strftime('%Y%m%d_%H%M%S')}_{uid}_{filename}"
            upload_folder = app.config.get('UPLOAD_FOLDER', 'static/uploads')
            os.makedirs(upload_folder, exist_ok=True)
            filepath = os.path.join(upload_folder, unique_filename)
            file.save(filepath)

            # Run detector (image_processor.detect returns annotated_image np.array + defects list + counts)
            try:
                result = image_processor.detect(filepath)
            except Exception as e:
                flash(f"Processing failed: {e}", 'danger')
                return redirect(request.url)

            # Annotated image array -> save to file
            annotated = result.get('annotated_image')
            processed_filename = f"processed_{unique_filename}"
            processed_path = os.path.join(upload_folder, processed_filename)
            if annotated is not None:
                # ensure BGR uint8 image
                try:
                    cv2.imwrite(processed_path, annotated)
                except Exception as e:
                    flash(f"Failed to save annotated image: {e}", 'danger')

            # Prepare JSON serializable report
            defects_raw = result.get('defects', [])
            defects_serial = [serialize_defect(d) for d in defects_raw]
            counts = result.get('counts', {})
            report = {
                'defect_count': len(defects_serial),
                'defect_types': counts,
                'defects': defects_serial,
                'detection_breakdown': {
                    'classical': result.get('detection_breakdown', {}).get('classical', None) if isinstance(result.get('detection_breakdown', {}), dict) else None,
                    'yolo': result.get('detection_breakdown', {}).get('yolo', None) if isinstance(result.get('detection_breakdown', {}), dict) else None,
                    'custom': result.get('detection_breakdown', {}).get('custom', None) if isinstance(result.get('detection_breakdown', {}), dict) else None
                }
            }

            # Save analysis entry to DB
            analysis = ImageAnalysis(
                user_id=current_user.id,
                filename=filename,
                original_path=unique_filename,
                processed_path=processed_filename,
                defect_count=report['defect_count'],
                defect_types=json.dumps(report['defect_types']),
                report_data=json.dumps(report)
            )
            db.session.add(analysis)
            db.session.commit()

            return redirect(url_for('show_results', analysis_id=analysis.id))
        else:
            flash('File type not allowed', 'warning')
            return redirect(request.url)
    return render_template('upload.html')

@app.route('/results/<int:analysis_id>')
@login_required
def show_results(analysis_id):
    analysis = ImageAnalysis.query.get_or_404(analysis_id)
    if analysis.user_id != current_user.id:
        flash('Access denied', 'danger')
        return redirect(url_for('dashboard'))

    report = json.loads(analysis.report_data)

    upload_folder = os.path.join('static', 'uploads')
    result_folder = os.path.join('static', 'results')

    os.makedirs(result_folder, exist_ok=True)  # make sure results folder exists

    # --- CREATE DETECTOR INSTANCE ---
    detector = ImageProcessor()  # or pass YOLO paths if needed

    # --- RUN DETECTION ---
    image_path = os.path.join(upload_folder, analysis.original_path)
    result = detector.detect(image_path)

    # save annotated image in results folder
    annotated_filename = f"annotated_{os.path.basename(analysis.original_path)}"
    annotated_path = os.path.join(result_folder, annotated_filename)  # result_folder = static/results
    cv2.imwrite(annotated_path, result['annotated_image'])

    # store path relative to static, using forward slashes
    result['annotated_image_path'] = f"results/{annotated_filename}"
    # relative to static

    return render_template(
        'results.html',
        analysis=analysis,
        report=report,
        result=result
    )

@app.route('/report/<int:analysis_id>')
@login_required
def generate_report(analysis_id):
    analysis = ImageAnalysis.query.get_or_404(analysis_id)
    if analysis.user_id != current_user.id:
        flash('Access denied', 'danger')
        return redirect(url_for('dashboard'))
    
    report = json.loads(analysis.report_data)
    current_time = datetime.now()  # <-- add this
    
    return render_template(
        'report.html',
        analysis=analysis,
        report=report,
        current_time=current_time  # <-- pass it to the template
    )
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter

@app.route('/download_report/<int:analysis_id>')
@login_required
def download_report(analysis_id):
    analysis = ImageAnalysis.query.get_or_404(analysis_id)
    if analysis.user_id != current_user.id:
        flash('Access denied', 'danger')
        return redirect(url_for('dashboard'))
    
    report = json.loads(analysis.report_data)

    # File paths
    pdf_filename = f"report_{analysis_id}.pdf"
    pdf_path = os.path.join("static", "results", pdf_filename)
    os.makedirs(os.path.join("static", "results"), exist_ok=True)

    # Create PDF
    c = canvas.Canvas(pdf_path, pagesize=letter)
    width, height = letter

    # Title
    c.setFont("Helvetica-Bold", 16)
    c.drawString(50, height - 50, "Defect Detection Report")

    # Add data
    c.setFont("Helvetica", 12)
    y = height - 90
    c.drawString(50, y, f"Image: {analysis.filename}")
    y -= 20
    c.drawString(50, y, f"Total Defects: {report['defect_count']}")
    y -= 20

    # defect types
    for defect_type, count in report["defect_types"].items():
        c.drawString(50, y, f"{defect_type}: {count}")
        y -= 18

    # Save & return file
    c.showPage()
    c.save()

    return send_file(pdf_path, as_attachment=True)

@app.route('/delete/<int:analysis_id>', methods=['POST'])
@login_required
def delete_analysis(analysis_id):
    analysis = ImageAnalysis.query.get_or_404(analysis_id)

    # ensure user owns the analysis
    if analysis.user_id != current_user.id:
        flash('Access denied', 'danger')
        return redirect(url_for('dashboard'))

    # --- delete stored images ---
    upload_folder = app.config.get('UPLOAD_FOLDER', 'static/uploads')

    # delete original image file
    original_file = os.path.join(upload_folder, analysis.original_path)
    if os.path.exists(original_file):
        os.remove(original_file)

    # delete processed image file
    processed_file = os.path.join(upload_folder, analysis.processed_path)
    if analysis.processed_path and os.path.exists(processed_file):
        os.remove(processed_file)

    # remove from database
    db.session.delete(analysis)
    db.session.commit()

    flash('Analysis deleted successfully', 'success')
    return redirect(url_for('dashboard'))



@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('login'))

# --- Run ---
if __name__ == '__main__':
    with app.app_context():
        db.create_all()
        os.makedirs(app.config.get('UPLOAD_FOLDER', 'static/uploads'), exist_ok=True)
    app.run(debug=True)
