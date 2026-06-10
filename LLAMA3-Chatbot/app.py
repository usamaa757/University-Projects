from flask import Flask, render_template, request, jsonify, session, redirect, url_for
from flask_cors import CORS
from datetime import datetime
import uuid
import os
from dotenv import load_dotenv

from groq_handler import GroqHandler
from database import Database
from analytics import Analytics
import auth

load_dotenv()
app = Flask(__name__)
app.secret_key = os.getenv('FLASK_SECRET_KEY', 'dev-secret-key-change-me')
CORS(app)

# Initialize components
db = Database()
groq_handler = GroqHandler()
analytics = Analytics()
auth.setup_auth(app, db)

@app.route('/')
def landing():
    """Landing page"""
    return render_template('landing.html')

@app.route('/chat')
def chat_interface():
    """Main chat interface"""
    user = auth.get_user()
    if not user:
        return redirect(url_for('login'))
    return render_template('index.html', user=user)

@app.route('/api/chat/send', methods=['POST'])
def send_message():
    """Send message to chatbot"""
    user = auth.get_user()
    if not user:
        return jsonify({'error': 'Not authenticated'}), 401
    
    data = request.json
    message = data.get('message')
    session_id = data.get('session_id')
    
    if not message:
        return jsonify({'error': 'No message provided'}), 400
    
    # Create new session if needed
    if not session_id:
        session_id = str(uuid.uuid4())
        db.create_session(session_id, user['email'])
    
    # Save user message
    db.save_message(session_id, 'user', message, 0)
    
    # Generate bot response using Groq
    bot_response, response_time = groq_handler.generate_response(message, session_id)
    
    # Save bot response
    message_id = db.save_message(session_id, 'assistant', bot_response, response_time)
    
    return jsonify({
        'response': bot_response,
        'session_id': session_id,
        'message_id': message_id,
        'response_time': round(response_time, 2)
    })

@app.route('/api/chat/feedback', methods=['POST'])
def submit_feedback():
    """Submit feedback"""
    user = auth.get_user()
    if not user:
        return jsonify({'error': 'Not authenticated'}), 401
    
    data = request.json
    message_id = data.get('message_id')
    rating = data.get('rating')
    correctness = data.get('correctness')
    response_length = data.get('response_length')
    comment = data.get('comment', '')
    
    db.save_feedback(message_id, rating, correctness, response_length, comment)
    
    return jsonify({'success': True})


@app.route('/api/chat/session/<session_id>', methods=['GET'])
def get_session_messages(session_id):
    """Get all messages for a specific session"""
    user = auth.get_user()
    if not user:
        return jsonify({'error': 'Not authenticated'}), 401
    
    # Verify session belongs to user
    conn = db.get_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT user_email FROM sessions WHERE session_id = ?", (session_id,))
    result = cursor.fetchone()
    conn.close()
    
    if not result or result[0] != user['email']:
        return jsonify({'error': 'Unauthorized'}), 403
    
    messages = db.get_session_messages(session_id)
    
    # Format messages for frontend
    formatted_messages = []
    for msg in messages:
        formatted_messages.append({
            'role': msg[0],
            'content': msg[1],
            'timestamp': msg[2],
            'id': msg[3]
        })
    
    return jsonify({
        'session_id': session_id,
        'messages': formatted_messages
    })
@app.route('/api/chat/history', methods=['GET'])
def get_chat_history():
    """Get user's chat history"""
    user = auth.get_user()
    if not user:
        return jsonify({'error': 'Not authenticated'}), 401
    
    sessions = db.get_user_sessions(user['email'])
    return jsonify({'sessions': sessions})

@app.route('/api/analytics/dashboard')
def analytics_dashboard():
    """Get analytics data"""
    user = auth.get_user()
    if not user:
        return jsonify({'error': 'Not authenticated'}), 401
    
    stats = analytics.generate_all_analytics()
    return jsonify(stats)

@app.route('/analytics')
def view_analytics():
    """Analytics page"""
    user = auth.get_user()
    if not user:
        return redirect(url_for('login'))
    return render_template('analytics.html', user=user)

@app.route('/api/system/status', methods=['GET'])
def system_status():
    """Check system status"""
    return jsonify({
        'groq_api': groq_handler.is_available(),
        'database': True,
        'status': 'ready' if groq_handler.is_available() else 'api_key_missing'
    })

@app.route('/api/session/end', methods=['POST'])
def end_session():
    """End current session"""
    data = request.json
    session_id = data.get('session_id')
    if session_id:
        db.end_session(session_id)
    return jsonify({'success': True})

if __name__ == '__main__':
    print("\n" + "="*50)
    print("🤖 LLaMA 3 Chatbot with Groq API")
    print("="*50)
    
    if groq_handler.is_available():
        print("✅ Groq API: Connected")
        print("✅ Database: Ready")
        print("\n🚀 Server starting at: http://localhost:5000")
        print("📊 Analytics dashboard: http://localhost:5000/analytics")
    else:
        print("⚠️  WARNING: GROQ_API_KEY not found!")
        print("Please create a .env file with your API key:")
        print("GROQ_API_KEY=your_key_here")
        print("\nGet your free API key from: https://console.groq.com")
    
    print("="*50 + "\n")
    app.run(debug=True, host='127.0.0.1', port=5000)