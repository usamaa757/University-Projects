import sqlite3
import datetime

class Database:
    def __init__(self):
        self.conn = sqlite3.connect('clothes_management.db')
        self.cursor = self.conn.cursor()
        self.create_tables()
    
    def create_tables(self):
        # Users table
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                user_type TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Clothing items table
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS clothing_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                target_group TEXT CHECK(target_group IN ('Men', 'Women', 'Kids')),
                season TEXT CHECK(season IN ('Summer', 'Winter', 'All')),
                purchase_price REAL NOT NULL,
                sale_price REAL NOT NULL,
                stock INTEGER DEFAULT 0,
                min_stock INTEGER DEFAULT 10,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        # Sales table
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS sales (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                sale_price REAL NOT NULL,
                total_amount REAL NOT NULL,
                sale_date DATE NOT NULL,
                sold_by TEXT,
                FOREIGN KEY (item_id) REFERENCES clothing_items (id)
            )
        ''')
        
        # Purchases table
        self.cursor.execute('''
            CREATE TABLE IF NOT EXISTS purchases (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                purchase_price REAL NOT NULL,
                total_amount REAL NOT NULL,
                purchase_date DATE NOT NULL,
                purchased_by TEXT,
                FOREIGN KEY (item_id) REFERENCES clothing_items (id)
            )
        ''')
        
        # Insert default admin user if not exists
        self.cursor.execute('SELECT COUNT(*) FROM users WHERE user_type = "admin"')
        if self.cursor.fetchone()[0] == 0:
            self.cursor.execute('''
                INSERT INTO users (username, password, user_type) 
                VALUES (?, ?, ?)
            ''', ('admin', 'admin123', 'admin'))
        
        self.conn.commit()
    
    def execute_query(self, query, params=()):
        try:
            self.cursor.execute(query, params)
            self.conn.commit()
            return True
        except Exception as e:
            print(f"Database error: {e}")
            return False
    
    def fetch_all(self, query, params=()):
        self.cursor.execute(query, params)
        return self.cursor.fetchall()
    
    def fetch_one(self, query, params=()):
        self.cursor.execute(query, params)
        return self.cursor.fetchone()
    
    def close(self):
        self.conn.close()