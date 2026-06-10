import sqlite3
from datetime import datetime
import json

class Database:
    def __init__(self, db_path="chatbot.db"):
        self.db_path = db_path
        self.init_tables()
    
    def get_connection(self):
        return sqlite3.connect(self.db_path)
    
    def init_tables(self):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        # Users table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                name TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Sessions table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS sessions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id TEXT UNIQUE NOT NULL,
                user_email TEXT,
                start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                end_time TIMESTAMP,
                message_count INTEGER DEFAULT 0,
                FOREIGN KEY (user_email) REFERENCES users(email)
            )
        ''')
        
        # Messages table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id TEXT,
                role TEXT,
                content TEXT,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                response_time REAL,
                FOREIGN KEY (session_id) REFERENCES sessions(session_id)
            )
        ''')
        
        # Feedback table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS feedback (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                message_id INTEGER,
                rating INTEGER,
                correctness TEXT,
                response_length TEXT,
                comment TEXT,
                timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (message_id) REFERENCES messages(id)
            )
        ''')
        
        conn.commit()
        conn.close()
        print("Database initialized successfully")
    
    def create_user(self, email, name):
        conn = self.get_connection()
        cursor = conn.cursor()
        try:
            cursor.execute("INSERT INTO users (email, name) VALUES (?, ?)", (email, name))
            conn.commit()
            return True
        except sqlite3.IntegrityError:
            return False
        finally:
            conn.close()
    
    def get_user(self, email):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM users WHERE email = ?", (email,))
        user = cursor.fetchone()
        conn.close()
        return user
    
    def create_session(self, session_id, user_email):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO sessions (session_id, user_email) VALUES (?, ?)", 
            (session_id, user_email)
        )
        conn.commit()
        conn.close()
    
    def update_session_message_count(self, session_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE sessions SET message_count = message_count + 1 WHERE session_id = ?",
            (session_id,)
        )
        conn.commit()
        conn.close()
    
    def save_message(self, session_id, role, content, response_time=None):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO messages (session_id, role, content, response_time) VALUES (?, ?, ?, ?)",
            (session_id, role, content, response_time)
        )
        message_id = cursor.lastrowid
        conn.commit()
        conn.close()
        
        # Update message count
        self.update_session_message_count(session_id)
        
        return message_id
    
    def save_feedback(self, message_id, rating, correctness, response_length, comment=""):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            """INSERT INTO feedback 
               (message_id, rating, correctness, response_length, comment) 
               VALUES (?, ?, ?, ?, ?)""",
            (message_id, rating, correctness, response_length, comment)
        )
        conn.commit()
        conn.close()
    
    def end_session(self, session_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE sessions SET end_time = CURRENT_TIMESTAMP WHERE session_id = ?", 
            (session_id,)
        )
        conn.commit()
        conn.close()
    
    def get_user_sessions(self, user_email, limit=10):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT session_id, start_time, message_count
            FROM sessions
            WHERE user_email = ?
            ORDER BY start_time DESC
            LIMIT ?
        """, (user_email, limit))
        sessions = cursor.fetchall()
        conn.close()
        return sessions
    
    def get_session_messages(self, session_id):
        """Get all messages for a specific session"""
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT role, content, timestamp, id
            FROM messages
            WHERE session_id = ?
            ORDER BY timestamp ASC
        """, (session_id,))
        messages = cursor.fetchall()
        conn.close()
        return messages  # Returns list of (role, content, timestamp, id)
    