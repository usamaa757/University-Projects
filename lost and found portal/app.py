from flask import Flask, render_template, request, redirect, url_for, session, flash, g
import sqlite3
import os
from werkzeug.utils import secure_filename
from datetime import datetime
import hashlib

app = Flask(__name__)
app.secret_key = 'your_secret_key_here_change_in_production'
app.config['UPLOAD_FOLDER'] = 'static/uploads'
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size

# Ensure upload directory exists
os.makedirs(app.config['UPLOAD_FOLDER'], exist_ok=True)

# Database setup
DATABASE = 'lost_and_found.db'

def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DATABASE)
        db.row_factory = sqlite3.Row
    return db

@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()

def init_db():
    with app.app_context():
        db = get_db()
        cursor = db.cursor()
        
        # Create users table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                full_name TEXT NOT NULL,
                phone TEXT,
                is_admin INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Create posts table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                title TEXT NOT NULL,
                category TEXT NOT NULL,
                description TEXT NOT NULL,
                location TEXT NOT NULL,
                date_lost_found DATE NOT NULL,
                image_path TEXT,
                status TEXT DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id)
            )
        ''')
        
        # Create messages table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sender_id INTEGER NOT NULL,
                receiver_id INTEGER NOT NULL,
                post_id INTEGER NOT NULL,
                message TEXT NOT NULL,
                is_read INTEGER DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (sender_id) REFERENCES users (id),
                FOREIGN KEY (receiver_id) REFERENCES users (id),
                FOREIGN KEY (post_id) REFERENCES posts (id)
            )
        ''')
        
        # Create default admin if not exists
        cursor.execute("SELECT * FROM users WHERE username = 'admin'")
        if not cursor.fetchone():
            admin_password = hashlib.sha256('admin123'.encode()).hexdigest()
            cursor.execute('''
                INSERT INTO users (username, password, email, full_name, is_admin)
                VALUES (?, ?, ?, ?, ?)
            ''', ('admin', admin_password, 'admin@lostfound.com', 'System Administrator', 1))
        
        db.commit()

# Initialize database
init_db()

# Helper functions
def hash_password(password):
    return hashlib.sha256(password.encode()).hexdigest()

def login_required(f):
    def decorated_function(*args, **kwargs):
        if 'user_id' not in session:
            flash('Please login to access this page')
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    decorated_function.__name__ = f.__name__
    return decorated_function

def admin_required(f):
    def decorated_function(*args, **kwargs):
        if 'user_id' not in session or session.get('is_admin') != 1:
            flash('Access denied')
            return redirect(url_for('index'))
        return f(*args, **kwargs)
    decorated_function.__name__ = f.__name__
    return decorated_function

# Routes
@app.route('/')
def index():
    db = get_db()
    recent_lost = db.execute('''
        SELECT posts.*, users.username, users.full_name 
        FROM posts JOIN users ON posts.user_id = users.id 
        WHERE type = 'lost' AND status = 'active' 
        ORDER BY created_at DESC LIMIT 5
    ''').fetchall()
    
    recent_found = db.execute('''
        SELECT posts.*, users.username, users.full_name 
        FROM posts JOIN users ON posts.user_id = users.id 
        WHERE type = 'found' AND status = 'active' 
        ORDER BY created_at DESC LIMIT 5
    ''').fetchall()
    
    return render_template('index.html', recent_lost=recent_lost, recent_found=recent_found)

@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        confirm_password = request.form['confirm_password']
        email = request.form['email']
        full_name = request.form['full_name']
        phone = request.form['phone']
        
        if password != confirm_password:
            flash('Passwords do not match')
            return render_template('register.html')
        
        db = get_db()
        
        # Check if username exists
        existing_user = db.execute('SELECT * FROM users WHERE username = ?', (username,)).fetchone()
        if existing_user:
            flash('Username already exists')
            return render_template('register.html')
        
        # Check if email exists
        existing_email = db.execute('SELECT * FROM users WHERE email = ?', (email,)).fetchone()
        if existing_email:
            flash('Email already registered')
            return render_template('register.html')
        
        # Create new user
        hashed_password = hash_password(password)
        db.execute('''
            INSERT INTO users (username, password, email, full_name, phone)
            VALUES (?, ?, ?, ?, ?)
        ''', (username, hashed_password, email, full_name, phone))
        db.commit()
        
        flash('Registration successful! Please login.')
        return redirect(url_for('login'))
    
    return render_template('register.html')

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form['username']
        password = request.form['password']
        
        db = get_db()
        user = db.execute('SELECT * FROM users WHERE username = ?', (username,)).fetchone()
        
        if user and user['password'] == hash_password(password):
            session['user_id'] = user['id']
            session['username'] = user['username']
            session['is_admin'] = user['is_admin']
            flash('Login successful!')
            
            if user['is_admin'] == 1:
                return redirect(url_for('admin_dashboard'))
            return redirect(url_for('dashboard'))
        else:
            flash('Invalid username or password')
    
    return render_template('login.html')

@app.route('/logout')
def logout():
    session.clear()
    flash('You have been logged out')
    return redirect(url_for('index'))

@app.route('/dashboard')
@login_required
def dashboard():
    db = get_db()
    user_posts = db.execute('''
        SELECT * FROM posts 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ''', (session['user_id'],)).fetchall()
    
    return render_template('dashboard.html', posts=user_posts)

@app.route('/post-lost', methods=['GET', 'POST'])
@login_required
def post_lost():
    if request.method == 'POST':
        title = request.form['title']
        category = request.form['category']
        description = request.form['description']
        location = request.form['location']
        date_lost = request.form['date_lost']
        
        # Handle file upload
        image_path = None
        if 'image' in request.files:
            file = request.files['image']
            if file and file.filename:
                filename = secure_filename(file.filename)
                # Add timestamp to filename to avoid duplicates
                name, ext = os.path.splitext(filename)
                filename = f"{name}_{datetime.now().strftime('%Y%m%d_%H%M%S')}{ext}"
                file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
                image_path = filename
        
        db = get_db()
        db.execute('''
            INSERT INTO posts (user_id, type, title, category, description, location, date_lost_found, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (session['user_id'], 'lost', title, category, description, location, date_lost, image_path))
        db.commit()
        
        flash('Lost item posted successfully!')
        return redirect(url_for('dashboard'))
    
    return render_template('post_lost.html')

@app.route('/post-found', methods=['GET', 'POST'])
@login_required
def post_found():
    if request.method == 'POST':
        title = request.form['title']
        category = request.form['category']
        description = request.form['description']
        location = request.form['location']
        date_found = request.form['date_found']
        
        # Handle file upload
        image_path = None
        if 'image' in request.files:
            file = request.files['image']
            if file and file.filename:
                filename = secure_filename(file.filename)
                name, ext = os.path.splitext(filename)
                filename = f"{name}_{datetime.now().strftime('%Y%m%d_%H%M%S')}{ext}"
                file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
                image_path = filename
        
        db = get_db()
        db.execute('''
            INSERT INTO posts (user_id, type, title, category, description, location, date_lost_found, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ''', (session['user_id'], 'found', title, category, description, location, date_found, image_path))
        db.commit()
        
        flash('Found item posted successfully!')
        return redirect(url_for('dashboard'))
    
    return render_template('post_found.html')

@app.route('/search', methods=['GET', 'POST'])
def search():
    if request.method == 'POST':
        keyword = request.form.get('keyword', '')
        category = request.form.get('category', '')
        location = request.form.get('location', '')
        date_from = request.form.get('date_from', '')
        item_type = request.form.get('item_type', '')
        
        query = '''
            SELECT posts.*, users.username, users.full_name 
            FROM posts JOIN users ON posts.user_id = users.id 
            WHERE status = 'active'
        '''
        params = []
        
        if keyword:
            query += ' AND (title LIKE ? OR description LIKE ?)'
            params.extend([f'%{keyword}%', f'%{keyword}%'])
        
        if category:
            query += ' AND category = ?'
            params.append(category)
        
        if location:
            query += ' AND location LIKE ?'
            params.append(f'%{location}%')
        
        if date_from:
            query += ' AND date_lost_found >= ?'
            params.append(date_from)
        
        if item_type:
            query += ' AND type = ?'
            params.append(item_type)
        
        query += ' ORDER BY created_at DESC'
        
        db = get_db()
        results = db.execute(query, params).fetchall()
        
        return render_template('search.html', results=results, search_performed=True)
    
    return render_template('search.html', results=None, search_performed=False)

@app.route('/item/<int:item_id>')
def item_detail(item_id):
    db = get_db()
    item = db.execute('''
        SELECT posts.*, users.username, users.full_name, users.id as user_id, users.phone, users.email
        FROM posts JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?
    ''', (item_id,)).fetchone()
    
    if not item:
        flash('Item not found')
        return redirect(url_for('index'))
    
    return render_template('item_detail.html', item=item)

@app.route('/contact/<int:post_id>', methods=['GET', 'POST'])
@login_required
def contact(post_id):
    db = get_db()
    post = db.execute('''
        SELECT posts.*, users.username, users.full_name, users.id as user_id
        FROM posts JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?
    ''', (post_id,)).fetchone()
    
    if not post:
        flash('Post not found')
        return redirect(url_for('index'))
    
    if request.method == 'POST':
        message = request.form['message']
        
        db.execute('''
            INSERT INTO messages (sender_id, receiver_id, post_id, message)
            VALUES (?, ?, ?, ?)
        ''', (session['user_id'], post['user_id'], post_id, message))
        db.commit()
        
        flash('Message sent successfully!')
        return redirect(url_for('item_detail', item_id=post_id))
    
    return render_template('contact.html', post=post)

@app.route('/messages')
@login_required
def messages():
    db = get_db()
    
    # Messages received
    received = db.execute('''
        SELECT messages.*, users.username as sender_name, posts.title as post_title
        FROM messages 
        JOIN users ON messages.sender_id = users.id
        JOIN posts ON messages.post_id = posts.id
        WHERE messages.receiver_id = ?
        ORDER BY messages.created_at DESC
    ''', (session['user_id'],)).fetchall()
    
    # Messages sent
    sent = db.execute('''
        SELECT messages.*, users.username as receiver_name, posts.title as post_title
        FROM messages 
        JOIN users ON messages.receiver_id = users.id
        JOIN posts ON messages.post_id = posts.id
        WHERE messages.sender_id = ?
        ORDER BY messages.created_at DESC
    ''', (session['user_id'],)).fetchall()
    
    return render_template('messages.html', received=received, sent=sent)

@app.route('/mark-read/<int:message_id>')
@login_required
def mark_read(message_id):
    db = get_db()
    db.execute('UPDATE messages SET is_read = 1 WHERE id = ?', (message_id,))
    db.commit()
    return redirect(url_for('messages'))

@app.route('/delete-post/<int:post_id>')
@login_required
def delete_post(post_id):
    db = get_db()
    post = db.execute('SELECT * FROM posts WHERE id = ?', (post_id,)).fetchone()
    
    if post and (post['user_id'] == session['user_id'] or session.get('is_admin') == 1):
        db.execute('UPDATE posts SET status = "deleted" WHERE id = ?', (post_id,))
        db.commit()
        flash('Post deleted successfully')
    else:
        flash('You do not have permission to delete this post')
    
    return redirect(url_for('dashboard'))

# Admin routes
@app.route('/admin')
@admin_required
def admin_dashboard():
    db = get_db()
    
    total_users = db.execute('SELECT COUNT(*) as count FROM users').fetchone()['count']
    total_posts = db.execute('SELECT COUNT(*) as count FROM posts').fetchone()['count']
    active_posts = db.execute('SELECT COUNT(*) as count FROM posts WHERE status = "active"').fetchone()['count']
    total_messages = db.execute('SELECT COUNT(*) as count FROM messages').fetchone()['count']
    
    recent_users = db.execute('SELECT * FROM users ORDER BY created_at DESC LIMIT 5').fetchall()
    recent_posts = db.execute('''
        SELECT posts.*, users.username 
        FROM posts JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC LIMIT 5
    ''').fetchall()
    
    return render_template('admin_dashboard.html', 
                         total_users=total_users,
                         total_posts=total_posts,
                         active_posts=active_posts,
                         total_messages=total_messages,
                         recent_users=recent_users,
                         recent_posts=recent_posts)

@app.route('/admin/users')
@admin_required
def admin_users():
    db = get_db()
    users = db.execute('SELECT * FROM users ORDER BY created_at DESC').fetchall()
    return render_template('admin_users.html', users=users)

@app.route('/admin/posts')
@admin_required
def admin_posts():
    db = get_db()
    posts = db.execute('''
        SELECT posts.*, users.username 
        FROM posts JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC
    ''').fetchall()
    return render_template('admin_posts.html', posts=posts)

@app.route('/admin/toggle-user/<int:user_id>')
@admin_required
def toggle_user(user_id):
    if user_id == session['user_id']:
        flash('You cannot modify your own account')
        return redirect(url_for('admin_users'))
    
    db = get_db()
    user = db.execute('SELECT * FROM users WHERE id = ?', (user_id,)).fetchone()
    
    if user:
        new_admin_status = 0 if user['is_admin'] == 1 else 1
        db.execute('UPDATE users SET is_admin = ? WHERE id = ?', (new_admin_status, user_id))
        db.commit()
        flash(f'User {"promoted to" if new_admin_status else "demoted from"} admin')
    
    return redirect(url_for('admin_users'))

@app.route('/admin/delete-user/<int:user_id>')
@admin_required
def delete_user(user_id):
    if user_id == session['user_id']:
        flash('You cannot delete your own account')
        return redirect(url_for('admin_users'))
    
    db = get_db()
    db.execute('DELETE FROM users WHERE id = ?', (user_id,))
    db.commit()
    flash('User deleted successfully')
    
    return redirect(url_for('admin_users'))

@app.route('/admin/moderate-post/<int:post_id>')
@admin_required
def moderate_post(post_id):
    db = get_db()
    post = db.execute('SELECT * FROM posts WHERE id = ?', (post_id,)).fetchone()
    
    if post:
        new_status = 'active' if post['status'] == 'deleted' else 'deleted'
        db.execute('UPDATE posts SET status = ? WHERE id = ?', (new_status, post_id))
        db.commit()
        flash(f'Post {"activated" if new_status == "active" else "deleted"}')
    
    return redirect(url_for('admin_posts'))

if __name__ == '__main__':
    app.run(debug=True)