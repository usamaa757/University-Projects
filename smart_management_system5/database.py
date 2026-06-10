# database.py - Updated with proper staff roles
import sqlite3
import hashlib
from datetime import datetime, timedelta
import random

class Database:
    def __init__(self):
        self.conn = sqlite3.connect('gym_management.db', check_same_thread=False)
        self.create_tables()
        self.insert_sample_data()
    
    def create_tables(self):
        cursor = self.conn.cursor()
        
        # Cities table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS cities (
                city_id INTEGER PRIMARY KEY AUTOINCREMENT,
                city_name TEXT NOT NULL UNIQUE
            )
        ''')
        
        # Gym branches table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS gym_branches (
                branch_id INTEGER PRIMARY KEY AUTOINCREMENT,
                branch_name TEXT NOT NULL,
                city_id INTEGER,
                address TEXT,
                manager_id INTEGER,
                contact_number TEXT,
                FOREIGN KEY (city_id) REFERENCES cities (city_id)
            )
        ''')
        
        # Staff table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS staff (
                staff_id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT UNIQUE,
                phone TEXT,
                role TEXT NOT NULL CHECK(role IN ('admin', 'manager', 'attendant', 'trainer')),
                branch_id INTEGER,
                username TEXT UNIQUE,
                password_hash TEXT,
                hire_date DATE,
                salary REAL,
                status TEXT DEFAULT 'Active',
                FOREIGN KEY (branch_id) REFERENCES gym_branches (branch_id)
            )
        ''')
        
        # Update gym_branches to add foreign key constraint after staff table exists
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS gym_branches_new (
                branch_id INTEGER PRIMARY KEY AUTOINCREMENT,
                branch_name TEXT NOT NULL,
                city_id INTEGER,
                address TEXT,
                manager_id INTEGER,
                contact_number TEXT,
                FOREIGN KEY (city_id) REFERENCES cities (city_id),
                FOREIGN KEY (manager_id) REFERENCES staff (staff_id)
            )
        ''')
        
        # Workout zones table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS workout_zones (
                zone_id INTEGER PRIMARY KEY AUTOINCREMENT,
                zone_name TEXT NOT NULL,
                branch_id INTEGER,
                zone_type TEXT,
                attendant_id INTEGER,
                description TEXT,
                FOREIGN KEY (branch_id) REFERENCES gym_branches (branch_id),
                FOREIGN KEY (attendant_id) REFERENCES staff (staff_id)
            )
        ''')
        
        # Members table (Regular gym members, not staff)
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS members (
                member_id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT UNIQUE,
                phone TEXT,
                date_of_birth DATE,
                address TEXT,
                emergency_contact TEXT,
                health_conditions TEXT,
                membership_type TEXT,
                join_date DATE,
                expiry_date DATE,
                status TEXT DEFAULT 'Active',
                branch_id INTEGER,
                FOREIGN KEY (branch_id) REFERENCES gym_branches (branch_id)
            )
        ''')
        
        # Trainers table (linked to staff)
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS trainers (
                trainer_id INTEGER PRIMARY KEY AUTOINCREMENT,
                staff_id INTEGER,
                specialization TEXT,
                certification TEXT,
                hourly_rate REAL,
                experience_years INTEGER,
                FOREIGN KEY (staff_id) REFERENCES staff (staff_id)
            )
        ''')
        
        # Appointments table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS appointments (
                appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                trainer_id INTEGER,
                appointment_type TEXT,
                appointment_date DATE,
                start_time TIME,
                end_time TIME,
                status TEXT DEFAULT 'Scheduled',
                notes TEXT,
                FOREIGN KEY (member_id) REFERENCES members (member_id),
                FOREIGN KEY (trainer_id) REFERENCES trainers (trainer_id)
            )
        ''')
        
        # Payments table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS payments (
                payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                amount REAL,
                payment_date DATE,
                payment_method TEXT,
                payment_type TEXT,
                status TEXT,
                stripe_payment_id TEXT,
                subscription_period TEXT,
                FOREIGN KEY (member_id) REFERENCES members (member_id)
            )
        ''')
        
        # Attendance table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS attendance (
                attendance_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                check_in DATETIME,
                check_out DATETIME,
                branch_id INTEGER,
                zone_id INTEGER,
                FOREIGN KEY (member_id) REFERENCES members (member_id),
                FOREIGN KEY (branch_id) REFERENCES gym_branches (branch_id),
                FOREIGN KEY (zone_id) REFERENCES workout_zones (zone_id)
            )
        ''')
        
        # Subscription plans table
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS subscription_plans (
                plan_id INTEGER PRIMARY KEY AUTOINCREMENT,
                plan_name TEXT,
                duration TEXT,
                price REAL,
                features TEXT
            )
        ''')
        
        self.conn.commit()
    
    def insert_sample_data(self):
        cursor = self.conn.cursor()
        
        # Check if data already exists
        cursor.execute("SELECT COUNT(*) FROM staff")
        if cursor.fetchone()[0] > 0:
            return
        
        # Insert cities
        cities = ['Lahore', 'Islamabad', 'Rawalpindi', 'Karachi', 'Peshawar']
        for city in cities:
            cursor.execute('INSERT OR IGNORE INTO cities (city_name) VALUES (?)', (city,))
        
        # Insert subscription plans
        plans = [
            ('Basic Monthly', 'Monthly', 3000, 'Access to basic equipment'),
            ('Premium Monthly', 'Monthly', 5000, 'All equipment + 2 personal training sessions'),
            ('Basic Quarterly', 'Quarterly', 8000, 'Access to basic equipment'),
            ('Premium Quarterly', 'Quarterly', 13000, 'All equipment + 6 personal training sessions'),
            ('Basic Annual', 'Annual', 25000, 'Access to basic equipment'),
            ('Premium Annual', 'Annual', 40000, 'All equipment + 24 personal training sessions')
        ]
        
        for plan in plans:
            cursor.execute('''
                INSERT OR IGNORE INTO subscription_plans (plan_name, duration, price, features)
                VALUES (?, ?, ?, ?)
            ''', plan)
        
        # Insert staff with proper roles and hashed passwords
        staff_data = [
            # Admin users
            ('Ali', 'Khan', 'admin@m9fitness.com', '0300-1111111', 'admin', 1, 'admin', 'admin123', 80000),
            ('Sara', 'Ahmed', 'sara.admin@m9fitness.com', '0300-1111112', 'admin', 2, 'saraadmin', 'admin123', 75000),
            
            # Managers
            ('Ahmed', 'Raza', 'manager.lahore@m9fitness.com', '0300-2222222', 'manager', 1, 'manager', 'manager123', 60000),
            ('Fatima', 'Shah', 'manager.isb@m9fitness.com', '0300-2222223', 'manager', 2, 'fatimamanager', 'manager123', 58000),
            
            # Attendants
            ('Bilal', 'Hussain', 'attendant1@m9fitness.com', '0300-3333333', 'attendant', 1, 'attendant', 'attendant123', 35000),
            ('Ayesha', 'Malik', 'attendant2@m9fitness.com', '0300-3333334', 'attendant', 1, 'ayeshaattendant', 'attendant123', 32000),
            ('Usman', 'Ali', 'attendant3@m9fitness.com', '0300-3333335', 'attendant', 2, 'usmanattendant', 'attendant123', 33000),
            
            # Trainers
            ('John', 'Smith', 'trainer1@m9fitness.com', '0300-4444444', 'trainer', 1, 'trainer', 'trainer123', 45000),
            ('Emma', 'Wilson', 'trainer2@m9fitness.com', '0300-4444445', 'trainer', 1, 'emmatrainer', 'trainer123', 42000),
            ('Mike', 'Johnson', 'trainer3@m9fitness.com', '0300-4444446', 'trainer', 2, 'miketrainer', 'trainer123', 43000),
        ]
        
        for first_name, last_name, email, phone, role, branch_id, username, password, salary in staff_data:
            password_hash = self.hash_password(password)
            cursor.execute('''
                INSERT INTO staff (first_name, last_name, email, phone, role, branch_id, username, password_hash, hire_date, salary)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ''', (first_name, last_name, email, phone, role, branch_id, username, password_hash, '2024-01-01', salary))
        
        # Insert gym branches
        branches = [
            ('M9 Fitness Lahore Main', 1, 'Main Boulevard Gulberg, Lahore', 3, '042-1111111'),
            ('M9 Fitness Islamabad', 2, 'Sector F-10 Markaz, Islamabad', 4, '051-2222222'),
            ('M9 Fitness Karachi DHA', 4, 'DHA Phase 5, Karachi', 4, '021-3333333')
        ]
        
        for branch_name, city_id, address, manager_id, contact in branches:
            cursor.execute('''
                INSERT INTO gym_branches (branch_name, city_id, address, manager_id, contact_number)
                VALUES (?, ?, ?, ?, ?)
            ''', (branch_name, city_id, address, manager_id, contact))
        
        # Insert trainers specialization
        trainer_specializations = [
            (7, 'Weight Training & Bodybuilding', 'ACE Certified', 2000, 5),
            (8, 'Yoga & Pilates', 'RYT 500 Certified', 1800, 4),
            (9, 'Cardio & Functional Training', 'NASM Certified', 1900, 6)
        ]
        
        for staff_id, specialization, certification, hourly_rate, experience in trainer_specializations:
            cursor.execute('''
                INSERT INTO trainers (staff_id, specialization, certification, hourly_rate, experience_years)
                VALUES (?, ?, ?, ?, ?)
            ''', (staff_id, specialization, certification, hourly_rate, experience))
        
        # Insert workout zones
        zones = [
            ('Cardio Zone', 1, 'Cardio', 5, 'Treadmills, Ellipticals, Stationary Bikes, Rowing Machines'),
            ('Weight Training Area', 1, 'Strength', 5, 'Free weights, Machines, Benches, Power Racks'),
            ('Yoga Studio', 1, 'Mind-Body', 6, 'Yoga mats, Meditation area, Pilates equipment'),
            ('Swimming Pool', 1, 'Aquatic', 5, 'Olympic size pool, Swimming lanes'),
            ('Functional Training', 2, 'Functional', 7, 'TRX, Kettlebells, Battle Ropes, Plyometric boxes')
        ]
        
        for zone_name, branch_id, zone_type, attendant_id, description in zones:
            cursor.execute('''
                INSERT INTO workout_zones (zone_name, branch_id, zone_type, attendant_id, description)
                VALUES (?, ?, ?, ?, ?)
            ''', (zone_name, branch_id, zone_type, attendant_id, description))
        
        # Insert sample members (regular gym members)
        members = [
            ('John', 'Doe', 'john.doe@email.com', '0300-1234567', '1990-05-15', '123 Main St Lahore', '0300-7654321', 'None', 'Premium Monthly', '2024-01-15', '2024-02-15', 'Active', 1),
            ('Jane', 'Smith', 'jane.smith@email.com', '0300-7654321', '1985-08-22', '456 Gulberg Lahore', '0300-1112233', 'Asthma', 'Basic Quarterly', '2024-02-01', '2024-05-01', 'Active', 1),
            ('Mike', 'Johnson', 'mike.johnson@email.com', '0300-1112233', '1992-12-10', '789 Model Town', '0300-4455667', 'None', 'Premium Annual', '2024-01-20', '2025-01-20', 'Active', 1),
            ('Sarah', 'Williams', 'sarah.williams@email.com', '0300-9988776', '1988-03-30', '321 Cantt Islamabad', '0300-5544332', 'Back Pain', 'Basic Monthly', '2024-03-01', '2024-04-01', 'Active', 2)
        ]
        
        for member in members:
            cursor.execute('''
                INSERT INTO members (first_name, last_name, email, phone, date_of_birth, address, emergency_contact, health_conditions, membership_type, join_date, expiry_date, status, branch_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ''', member)
        
        # Insert sample appointments
        appointments = [
            (1, 1, 'Personal Training', '2024-03-20', '14:00', '15:00', 'Scheduled', 'Focus on weight training'),
            (2, 2, 'Yoga Session', '2024-03-21', '10:00', '11:00', 'Scheduled', 'Beginner yoga introduction'),
            (3, 1, 'Personal Training', '2024-03-22', '16:00', '17:00', 'Scheduled', 'Cardio workout'),
            (4, 3, 'Functional Training', '2024-03-23', '09:00', '10:00', 'Scheduled', 'TRX and functional movements')
        ]
        
        for appointment in appointments:
            cursor.execute('''
                INSERT INTO appointments (member_id, trainer_id, appointment_type, appointment_date, start_time, end_time, status, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ''', appointment)
        
        # Insert sample payments
        payments = [
            (1, 5000, '2024-03-15', 'Stripe', 'Monthly Subscription', 'Completed', 'pi_123456', 'Monthly'),
            (2, 8000, '2024-03-16', 'Cash', 'Quarterly Subscription', 'Completed', None, 'Quarterly'),
            (3, 40000, '2024-03-17', 'Stripe', 'Annual Subscription', 'Completed', 'pi_789012', 'Annual'),
            (4, 3000, '2024-03-18', 'Bank Transfer', 'Monthly Subscription', 'Completed', None, 'Monthly')
        ]
        
        for payment in payments:
            cursor.execute('''
                INSERT INTO payments (member_id, amount, payment_date, payment_method, payment_type, status, stripe_payment_id, subscription_period)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ''', payment)
        
        self.conn.commit()
    
    def hash_password(self, password):
        return hashlib.sha256(password.encode()).hexdigest()
    
    def verify_user(self, username, password, role):
        cursor = self.conn.cursor()
        password_hash = self.hash_password(password)
        
        cursor.execute('''
            SELECT staff_id, first_name, last_name, role, branch_id 
            FROM staff 
            WHERE username = ? AND password_hash = ? AND role = ? AND status = 'Active'
        ''', (username, password_hash, role))
        
        return cursor.fetchone()
    
    # Add these methods to get staff information
    def get_staff_by_role(self, role):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT staff_id, first_name || ' ' || last_name, role, branch_name
            FROM staff s
            LEFT JOIN gym_branches gb ON s.branch_id = gb.branch_id
            WHERE s.role = ? AND s.status = 'Active'
            ORDER BY first_name
        ''', (role,))
        return cursor.fetchall()
    
    def get_all_staff(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT staff_id, first_name || ' ' || last_name, role, branch_name, email, phone, hire_date
            FROM staff s
            LEFT JOIN gym_branches gb ON s.branch_id = gb.branch_id
            WHERE s.status = 'Active'
            ORDER BY role, first_name
        ''')
        return cursor.fetchall()
    
    def get_trainers_with_specialization(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT s.staff_id, s.first_name || ' ' || s.last_name, t.specialization, t.hourly_rate
            FROM staff s
            JOIN trainers t ON s.staff_id = t.staff_id
            WHERE s.role = 'trainer' AND s.status = 'Active'
            ORDER BY s.first_name
        ''')
        return cursor.fetchall()
    
    
    # Dynamic data methods
    def get_total_members(self):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM members WHERE status='Active'")
        return cursor.fetchone()[0]
    
    def get_total_staff(self):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM staff")
        return cursor.fetchone()[0]
    
    def get_recent_payments(self):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM payments WHERE payment_date >= date('now', '-30 days')")
        return cursor.fetchone()[0]
    
    def get_upcoming_appointments(self):
        cursor = self.conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM appointments WHERE appointment_date >= date('now')")
        return cursor.fetchone()[0]
    
    def get_membership_distribution(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT membership_type, COUNT(*) 
            FROM members 
            WHERE status='Active' 
            GROUP BY membership_type
        ''')
        return cursor.fetchall()
    
    def get_city_member_distribution(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT c.city_name, COUNT(m.member_id)
            FROM cities c
            LEFT JOIN gym_branches gb ON c.city_id = gb.city_id
            LEFT JOIN members m ON gb.branch_id = m.branch_id AND m.status='Active'
            GROUP BY c.city_name
        ''')
        return cursor.fetchall()
    
    def get_all_members(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT m.member_id, m.first_name || ' ' || m.last_name, m.email, m.phone, 
                   m.membership_type, m.join_date, m.status, gb.branch_name
            FROM members m
            LEFT JOIN gym_branches gb ON m.branch_id = gb.branch_id
        ''')
        return cursor.fetchall()
    
    def get_all_appointments(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT a.appointment_id, m.first_name || ' ' || m.last_name, 
                   s.first_name || ' ' || s.last_name, a.appointment_type, 
                   a.appointment_date, a.start_time || '-' || a.end_time, a.status
            FROM appointments a
            JOIN members m ON a.member_id = m.member_id
            JOIN trainers t ON a.trainer_id = t.trainer_id
            JOIN staff s ON t.staff_id = s.staff_id
        ''')
        return cursor.fetchall()
    
    def get_all_payments(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT p.payment_id, m.first_name || ' ' || m.last_name, p.amount, 
                   p.payment_date, p.payment_method, p.payment_type, p.status
            FROM payments p
            JOIN members m ON p.member_id = m.member_id
            ORDER BY p.payment_date DESC
        ''')
        return cursor.fetchall()
    
    def get_membership_growth(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT strftime('%Y-%m', join_date) as month, COUNT(*) as new_members,
                   (SELECT COUNT(*) FROM members m2 WHERE strftime('%Y-%m', m2.join_date) <= month) as total_members
            FROM members
            GROUP BY month
            ORDER BY month
            LIMIT 6
        ''')
        return cursor.fetchall()
    
    def get_revenue_analysis(self):
        cursor = self.conn.cursor()
        # Monthly revenue
        cursor.execute('''
            SELECT strftime('%Y-%m', payment_date) as month, SUM(amount) as revenue
            FROM payments
            WHERE status = 'Completed'
            GROUP BY month
            ORDER BY month
            LIMIT 6
        ''')
        monthly_revenue = cursor.fetchall()
        
        # Revenue by subscription type
        cursor.execute('''
            SELECT 
                CASE 
                    WHEN membership_type LIKE '%Basic%Monthly%' THEN 'Basic Monthly'
                    WHEN membership_type LIKE '%Premium%Monthly%' THEN 'Premium Monthly'
                    WHEN membership_type LIKE '%Basic%Quarterly%' THEN 'Basic Quarterly'
                    WHEN membership_type LIKE '%Premium%Quarterly%' THEN 'Premium Quarterly'
                    WHEN membership_type LIKE '%Annual%' THEN 'Annual'
                    ELSE 'Other'
                END as subscription_type,
                SUM(p.amount) as revenue
            FROM payments p
            JOIN members m ON p.member_id = m.member_id
            WHERE p.status = 'Completed'
            GROUP BY subscription_type
        ''')
        revenue_by_type = cursor.fetchall()
        
        return monthly_revenue, revenue_by_type
    
    def get_attendance_pattern(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT 
                CASE strftime('%w', check_in)
                    WHEN '0' THEN 'Sun'
                    WHEN '1' THEN 'Mon'
                    WHEN '2' THEN 'Tue'
                    WHEN '3' THEN 'Wed'
                    WHEN '4' THEN 'Thu'
                    WHEN '5' THEN 'Fri'
                    WHEN '6' THEN 'Sat'
                END as day,
                COUNT(*) as visits
            FROM attendance
            GROUP BY day
            ORDER BY 
                CASE strftime('%w', check_in)
                    WHEN '1' THEN 1
                    WHEN '2' THEN 2
                    WHEN '3' THEN 3
                    WHEN '4' THEN 4
                    WHEN '5' THEN 5
                    WHEN '6' THEN 6
                    WHEN '0' THEN 7
                END
        ''')
        return cursor.fetchall()
    
    def get_peak_hours(self):
        cursor = self.conn.cursor()
        cursor.execute('''
            SELECT strftime('%H', check_in) as hour, COUNT(*) as visits
            FROM attendance
            GROUP BY hour
            ORDER BY hour
        ''')
        return cursor.fetchall()
    
    def close(self):
        self.conn.close()
    

# Add these methods to your Database class in database.py

def get_all_branches_for_staff(self):
    """Get all branches for staff assignment"""
    cursor = self.conn.cursor()
    cursor.execute('''
        SELECT branch_id, branch_name, city_name 
        FROM gym_branches gb
        JOIN cities c ON gb.city_id = c.city_id
        ORDER BY branch_name
    ''')
    return cursor.fetchall()

def insert_staff(self, staff_data):
    """Insert new staff member with hashed password"""
    cursor = self.conn.cursor()
    try:
        # Hash the password before storing
        password_hash = self.hash_password(staff_data[8])  # password is at index 8
        
        # Replace plain password with hashed password
        staff_data_list = list(staff_data)
        staff_data_list[8] = password_hash
        staff_data = tuple(staff_data_list)
        
        cursor.execute('''
            INSERT INTO staff 
            (first_name, last_name, email, phone, role, branch_id, username, password_hash, hire_date, salary, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ''', staff_data)
        
        staff_id = cursor.lastrowid
        
        # If the staff member is a trainer, insert into trainers table
        if staff_data[4] == 'trainer':  # role is at index 4
            cursor.execute('''
                INSERT INTO trainers (staff_id, specialization, certification, hourly_rate, experience_years)
                VALUES (?, ?, ?, ?, ?)
            ''', (staff_id, 'General Fitness', 'Pending Certification', 1500, 1))
        
        self.conn.commit()
        return staff_id
    except sqlite3.IntegrityError as e:
        self.conn.rollback()
        raise e

def check_username_exists(self, username):
    """Check if username already exists"""
    cursor = self.conn.cursor()
    cursor.execute('SELECT staff_id FROM staff WHERE username = ?', (username,))
    return cursor.fetchone() is not None

def check_email_exists(self, email):
    """Check if email already exists"""
    cursor = self.conn.cursor()
    cursor.execute('SELECT staff_id FROM staff WHERE email = ?', (email,))
    return cursor.fetchone() is not None

def get_staff_roles(self):
    """Get all available staff roles"""
    return ['admin', 'manager', 'attendant', 'trainer']

def get_default_salary_by_role(self, role):
    """Get default salary based on role"""
    salary_ranges = {
        'admin': 80000,
        'manager': 60000,
        'trainer': 45000,
        'attendant': 35000
    }
    return salary_ranges.get(role, 35000)