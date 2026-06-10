import sqlite3
import json
import os
import random
from datetime import datetime, timedelta
import hashlib


class Database:
    def __init__(self, db_name="gym_management.db"):
        self.db_name = db_name
        self.init_database()

    def get_connection(self):
        return sqlite3.connect(self.db_name)

    def init_database(self):
        conn = self.get_connection()
        cursor = conn.cursor()

        # Gym Branches Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS gym_branches (
                branch_id INTEGER PRIMARY KEY AUTOINCREMENT,
                branch_name TEXT NOT NULL,
                city TEXT NOT NULL,
                address TEXT,
                manager_id INTEGER,
                contact_number TEXT,
                email TEXT,
                established_date DATE,
                facilities TEXT,  -- JSON string of facilities
                FOREIGN KEY (manager_id) REFERENCES staff(staff_id)
            )
        """
        )

        # Workout Zones Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS workout_zones (
                zone_id INTEGER PRIMARY KEY AUTOINCREMENT,
                zone_name TEXT NOT NULL,
                branch_id INTEGER,
                zone_type TEXT,  -- Cardio, Strength, Yoga, etc.
                attendant_id INTEGER,
                equipment_list TEXT,  -- JSON string
                schedule TEXT,  -- JSON string
                status TEXT DEFAULT 'Active',
                last_maintenance DATE,
                FOREIGN KEY (branch_id) REFERENCES gym_branches(branch_id),
                FOREIGN KEY (attendant_id) REFERENCES staff(staff_id)
            )
        """
        )

        # Staff Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS staff (
                staff_id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                role TEXT NOT NULL,
                branch_id INTEGER,
                email TEXT UNIQUE,
                phone TEXT,
                hire_date DATE,
                salary REAL,
                specialization TEXT,
                schedule TEXT,
                status TEXT DEFAULT 'Active',
                zone_id INTEGER,
                password_hash TEXT,
                FOREIGN KEY (branch_id) REFERENCES gym_branches(branch_id)
            )

        """
        )

        # Members Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS members (
                member_id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT UNIQUE,
                phone TEXT,
                date_of_birth DATE,
                gender TEXT,
                address TEXT,
                city TEXT,
                emergency_contact TEXT,
                medical_conditions TEXT,
                fitness_goals TEXT,
                membership_type TEXT,  -- Regular, Premium, Trial
                join_date DATE,
                expiry_date DATE,
                status TEXT DEFAULT 'Active',
                branch_id INTEGER,
                zone_id INTEGER,
                subscription_plan TEXT,  -- Monthly, Quarterly, Annual
                payment_method TEXT,
                FOREIGN KEY (branch_id) REFERENCES gym_branches(branch_id)
                FOREIGN KEY (zone_id) REFERENCES workout_zones(zone_id)
            )
        """
        )

        # Classes Table - NEW
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS classes (
                class_id INTEGER PRIMARY KEY AUTOINCREMENT,
                class_name TEXT NOT NULL,
                class_type TEXT NOT NULL,
                description TEXT,
                class_date DATE NOT NULL,
                class_time TIME NOT NULL,
                duration_minutes INTEGER NOT NULL,
                capacity INTEGER NOT NULL,
                trainer_id INTEGER NOT NULL,
                branch_id INTEGER NOT NULL,
                zone_id INTEGER NOT NULL,
                status TEXT DEFAULT 'Scheduled',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (trainer_id) REFERENCES staff(staff_id),
                FOREIGN KEY (branch_id) REFERENCES gym_branches(branch_id),
                FOREIGN KEY (zone_id) REFERENCES workout_zones(zone_id)
            )
        """
        )

        # Class Registrations Table - NEW
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS class_registrations (
                registration_id INTEGER PRIMARY KEY AUTOINCREMENT,
                class_id INTEGER NOT NULL,
                member_id INTEGER NOT NULL,
                registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                attendance_status TEXT DEFAULT 'Registered',
                FOREIGN KEY (class_id) REFERENCES classes(class_id),
                FOREIGN KEY (member_id) REFERENCES members(member_id),
                UNIQUE(class_id, member_id)
            )
        """
        )

        # Appointments Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS appointments (
                appointment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                trainer_id INTEGER,
                appointment_type TEXT,  -- Personal Training, Group Class, Nutrition Consultation
                appointment_date DATE,
                start_time TIME,
                end_time TIME,
                status TEXT,  -- Scheduled, Completed, Cancelled, No-show
                notes TEXT,
                zone_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (member_id) REFERENCES members(member_id),
                FOREIGN KEY (trainer_id) REFERENCES staff(staff_id),
                FOREIGN KEY (zone_id) REFERENCES workout_zones(zone_id)
            )
        """
        )

        # Payments Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS payments (
                payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                amount REAL NOT NULL,
                payment_date DATE,
                payment_type TEXT,  -- Membership, Session, Personal Training
                payment_method TEXT,  -- Cash, Card, Online
                subscription_period TEXT,  -- Monthly, Quarterly, Annual
                discount_applied REAL DEFAULT 0,
                final_amount REAL,
                status TEXT DEFAULT 'Completed',
                invoice_number TEXT UNIQUE,
                FOREIGN KEY (member_id) REFERENCES members(member_id)
            )
        """
        )

        # Attendance Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS attendance (
                attendance_id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                check_in TIMESTAMP,
                check_out TIMESTAMP,
                zone_id INTEGER,
                duration_minutes INTEGER,
                FOREIGN KEY (member_id) REFERENCES members(member_id),
                FOREIGN KEY (zone_id) REFERENCES workout_zones(zone_id)
            )
        """
        )

        # Equipment Maintenance Table
        cursor.execute(
            """
            CREATE TABLE IF NOT EXISTS equipment_maintenance (
                maintenance_id INTEGER PRIMARY KEY AUTOINCREMENT,
                equipment_name TEXT,
                zone_id INTEGER,
                last_maintenance DATE,
                next_maintenance DATE,
                status TEXT,
                notes TEXT,
                FOREIGN KEY (zone_id) REFERENCES workout_zones(zone_id)
            )
        """
        )

        # Insert sample data if tables are empty
        self.insert_sample_data(cursor)

        conn.commit()
        conn.close()

    def hash_password(self, password):
        
        return hashlib.sha256(password.encode()).hexdigest()

    def insert_sample_data(self, cursor):
        # Check if branches exist
        cursor.execute("SELECT COUNT(*) FROM gym_branches")
        if cursor.fetchone()[0] == 0:
            # Insert sample gym branches
            branches = [
                (
                    "M9 Fitness Gulberg",
                    "Lahore",
                    "123 Main Street, Gulberg",
                    "0300-1234567",
                    "gulberg@m9fitness.com",
                    "2020-01-15",
                    '["Gym", "Swimming Pool", "Sauna", "Cafe"]',
                ),
                (
                    "M9 Fitness DHA",
                    "Lahore",
                    "456 Phase 5, DHA",
                    "0300-2345678",
                    "dha@m9fitness.com",
                    "2021-03-20",
                    '["Gym", "Yoga Studio", "Basketball Court"]',
                ),
                (
                    "M9 Fitness Islamabad",
                    "Islamabad",
                    "789 F-7 Markaz",
                    "051-1234567",
                    "islamabad@m9fitness.com",
                    "2019-11-10",
                    '["Gym", "Swimming Pool", "Tennis Court"]',
                ),
                (
                    "M9 Fitness Karachi",
                    "Karachi",
                    "101 Clifton Road",
                    "021-3456789",
                    "karachi@m9fitness.com",
                    "2022-05-30",
                    '["Gym", "Boxing Ring", "Martial Arts"]',
                ),
                (
                    "M9 Fitness Peshawar",
                    "Peshawar",
                    "234 University Road",
                    "091-4567890",
                    "peshawar@m9fitness.com",
                    "2023-02-15",
                    '["Gym", "Cardio Zone", "Weight Training"]',
                ),
            ]

            cursor.executemany(
                """
                INSERT INTO gym_branches (branch_name, city, address, contact_number, email, established_date, facilities)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            """,
                branches,
            )

            # Insert sample staff
            staff = [
                (
                    "Ahmed",
                    "Raza",
                    "Manager",
                    1,
                    "ahmed@m9fitness.com",
                    "0300-1111111",
                    "2020-01-20",
                    120000,
                    "General Management",
                    '{"Monday": "9AM-6PM", "Tuesday": "9AM-6PM", "Wednesday": "9AM-6PM", "Thursday": "9AM-6PM", "Friday": "9AM-4PM"}',
                    "Active",
                    1,
                    self.hash_password("manager123"),
                ),
                (
                    "Sara",
                    "Khan",
                    "Trainer",
                    1,
                    "sara@m9fitness.com",
                    "0300-2222222",
                    "2020-02-15",
                    80000,
                    "Personal Training",
                    '{"Monday": "6AM-2PM", "Tuesday": "6AM-2PM", "Wednesday": "6AM-2PM", "Thursday": "6AM-2PM", "Friday": "6AM-12PM", "Saturday": "8AM-12PM"}',
                    "Active",
                    2,
                    self.hash_password("trainer123"),
                ),
                (
                    "Bilal",
                    "Ahmed",
                    "Nutritionist",
                    1,
                    "bilal@m9fitness.com",
                    "0300-3333333",
                    "2020-03-10",
                    90000,
                    "Diet Planning",
                    '{"Monday": "10AM-6PM", "Tuesday": "10AM-6PM", "Thursday": "10AM-6PM", "Friday": "10AM-4PM"}',
                    "Active",
                    3,
                    self.hash_password("nutrition123"),
                ),
                (
                    "Fatima",
                    "Ali",
                    "Attendant",
                    1,
                    "fatima@m9fitness.com",
                    "0300-4444444",
                    "2021-01-15",
                    40000,
                    "Cardio Zone",
                    '{"Monday": "2PM-10PM", "Tuesday": "2PM-10PM", "Wednesday": "2PM-10PM", "Thursday": "2PM-10PM", "Friday": "12PM-8PM"}',
                    "Active",
                    1,
                    self.hash_password("attendant123"),
                ),
            ]

            cursor.executemany(
                """
                INSERT INTO staff (first_name, last_name, role, branch_id, email, phone, hire_date, salary, 
                specialization, schedule, status, zone_id, password_hash)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """,
                staff,
            )

            # Update branch managers
            cursor.execute("UPDATE gym_branches SET manager_id = 1 WHERE branch_id = 1")

            # Insert workout zones
            zones = [
                (
                    "Cardio Zone",
                    1,
                    "Cardio",
                    4,
                    '["Treadmill", "Elliptical", "Stationary Bike", "Rowing Machine"]',
                    '{"Monday": "6AM-10PM", "Tuesday": "6AM-10PM", "Wednesday": "6AM-10PM", "Thursday": "6AM-10PM", "Friday": "6AM-8PM", "Saturday": "8AM-8PM", "Sunday": "8AM-6PM"}',
                    "Active",
                    "2024-01-15",
                ),
                (
                    "Strength Zone",
                    1,
                    "Strength",
                    4,
                    '["Bench Press", "Dumbbells", "Barbells", "Leg Press", "Cable Machine"]',
                    '{"Monday": "6AM-10PM", "Tuesday": "6AM-10PM", "Wednesday": "6AM-10PM", "Thursday": "6AM-10PM", "Friday": "6AM-8PM", "Saturday": "8AM-8PM", "Sunday": "8AM-6PM"}',
                    "Active",
                    "2024-01-10",
                ),
                (
                    "Yoga Studio",
                    1,
                    "Yoga",
                    4,
                    '["Yoga Mats", "Blocks", "Straps", "Bolsters", "Blankets"]',
                    '{"Monday": "7AM-9PM", "Tuesday": "7AM-9PM", "Wednesday": "7AM-9PM", "Friday": "7AM-7PM", "Saturday": "9AM-7PM"}',
                    "Active",
                    "2024-01-05",
                ),
            ]

            cursor.executemany(
                """
                INSERT INTO workout_zones (zone_name, branch_id, zone_type, attendant_id, equipment_list, schedule, status, last_maintenance)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            """,
                zones,
            )

            # Insert sample members
            for i in range(20):
                join_date = datetime.now() - timedelta(days=random.randint(1, 365))
                expiry_date = join_date + timedelta(days=30)

                cursor.execute(
                    """
                    INSERT INTO members (first_name, last_name, email, phone, date_of_birth, gender, 
                    membership_type, join_date, expiry_date, branch_id, zone_id, subscription_plan, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                """,
                    (
                        f"Member{i+1}",
                        f"Lastname{i+1}",
                        f"member{i+1}@email.com",
                        f"0300-{1000000 + i}",
                        f"{1990 + random.randint(0, 30)}-{random.randint(1, 12):02d}-{random.randint(1, 28):02d}",
                        random.choice(["Male", "Female"]),
                        random.choice(["Regular", "Premium", "Trial"]),
                        join_date.strftime("%Y-%m-%d"),
                        expiry_date.strftime("%Y-%m-%d"),
                        1,
                        random.randint(1, 3),
                        random.choice(["Monthly", "Quarterly", "Annual"]),
                        "Active",
                    ),
                )

            # Insert sample classes
            sample_classes = [
                (
                    "Morning Yoga",
                    "Yoga",
                    "Start your day with peaceful yoga session",
                    (datetime.now() + timedelta(days=1)).strftime("%Y-%m-%d"),
                    "07:00",
                    60,
                    15,
                    2,  # Trainer Sara
                    1,  # Branch 1
                    3,  # Yoga Studio
                    "Scheduled",
                ),
                (
                    "HIIT Workout",
                    "HIIT",
                    "High intensity interval training for fat burning",
                    (datetime.now() + timedelta(days=1)).strftime("%Y-%m-%d"),
                    "18:00",
                    45,
                    20,
                    2,  # Trainer Sara
                    1,  # Branch 1
                    1,  # Cardio Zone
                    "Scheduled",
                ),
                (
                    "Zumba Dance",
                    "Zumba",
                    "Fun dance workout with Latin rhythms",
                    (datetime.now() + timedelta(days=2)).strftime("%Y-%m-%d"),
                    "17:00",
                    60,
                    25,
                    2,  # Trainer Sara
                    1,  # Branch 1
                    2,  # Strength Zone
                    "Scheduled",
                ),
                (
                    "Strength Training",
                    "Strength",
                    "Build muscle and strength with guided training",
                    (datetime.now() + timedelta(days=2)).strftime("%Y-%m-%d"),
                    "19:00",
                    90,
                    10,
                    2,  # Trainer Sara
                    1,  # Branch 1
                    2,  # Strength Zone
                    "Scheduled",
                ),
            ]

            cursor.executemany(
                """
                INSERT INTO classes (class_name, class_type, description, class_date, class_time, 
                                   duration_minutes, capacity, trainer_id, branch_id, zone_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """,
                sample_classes,
            )

            # Insert some class registrations
            for class_id in range(1, 5):  # For each sample class
                for member_id in random.sample(range(1, 21), random.randint(3, 8)):
                    cursor.execute(
                        """
                        INSERT OR IGNORE INTO class_registrations (class_id, member_id)
                        VALUES (?, ?)
                    """,
                        (class_id, member_id),
                    )

    # ---------- BRANCH METHODS ----------
    def get_branches(self):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT branch_id, branch_name, city, contact_number FROM gym_branches"
        )
        rows = cursor.fetchall()
        conn.close()
        return rows

    def get_branch_by_id(self, branch_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT branch_id, branch_name, city, contact_number, email FROM gym_branches WHERE branch_id=?",
            (branch_id,),
        )
        row = cursor.fetchone()
        conn.close()
        return row

    def add_branch(self, name, city, contact, email):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO gym_branches (branch_name, city, contact_number, email) VALUES (?, ?, ?, ?)",
            (name, city, contact, email),
        )
        conn.commit()
        conn.close()

    def update_branch(self, branch_id, name, city, contact, email):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE gym_branches SET branch_name=?, city=?, contact_number=?, email=? WHERE branch_id=?",
            (name, city, contact, email, branch_id),
        )
        conn.commit()
        conn.close()

    def delete_branch(self, branch_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        # Delete associated zones first
        cursor.execute("DELETE FROM workout_zones WHERE branch_id=?", (branch_id,))
        # Then delete the branch
        cursor.execute("DELETE FROM gym_branches WHERE branch_id=?", (branch_id,))
        conn.commit()
        conn.close()

    # ---------- ZONE METHODS ----------
    def get_zones_by_branch_id(self, branch_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT zone_id, zone_name, zone_type, status FROM workout_zones WHERE branch_id=?",
            (branch_id,),
        )
        rows = cursor.fetchall()
        conn.close()
        return rows

    def get_zone_by_id(self, zone_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT zone_id, zone_name, zone_type FROM workout_zones WHERE zone_id=?",
            (zone_id,),
        )
        row = cursor.fetchone()
        conn.close()
        return row

    def add_zone(self, branch_id, name, ztype):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "INSERT INTO workout_zones (branch_id, zone_name, zone_type) VALUES (?, ?, ?)",
            (branch_id, name, ztype),
        )
        conn.commit()
        conn.close()

    def update_zone(self, zone_id, name, ztype):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE workout_zones SET zone_name=?, zone_type=? WHERE zone_id=?",
            (name, ztype, zone_id),
        )
        conn.commit()
        conn.close()

    def delete_zone(self, zone_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute("DELETE FROM workout_zones WHERE zone_id=?", (zone_id,))
        conn.commit()
        conn.close()

    # ---------- STAFF METHODS ----------
    def get_staff_by_role(self, role):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT staff_id, first_name || ' ' || last_name as name FROM staff WHERE role = ? AND status = 'Active'",
            (role,),
        )
        rows = cursor.fetchall()
        conn.close()
        return rows

    def get_staff_by_id(self, staff_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT staff_id, first_name, last_name, role, branch_id, email, phone FROM staff WHERE staff_id = ?",
            (staff_id,),
        )
        row = cursor.fetchone()
        conn.close()
        return row

    # ---------- CLASS METHODS ----------
    def get_classes(self, trainer_id=None, branch_id=None, zone_id=None):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        query = """
            SELECT c.class_id, c.class_name, c.class_type, c.description, 
                   c.class_date, c.class_time, c.duration_minutes, c.capacity,
                   s.first_name || ' ' || s.last_name as trainer_name,
                   b.branch_name, z.zone_name, c.status,
                   (SELECT COUNT(*) FROM class_registrations cr WHERE cr.class_id = c.class_id) as enrolled_count
            FROM classes c
            LEFT JOIN staff s ON c.trainer_id = s.staff_id
            LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
            WHERE 1=1
        """
        
        params = []
        
        if trainer_id:
            query += " AND c.trainer_id = ?"
            params.append(trainer_id)
        
        if branch_id:
            query += " AND c.branch_id = ?"
            params.append(branch_id)
        
        if zone_id:
            query += " AND c.zone_id = ?"
            params.append(zone_id)
        
        query += " ORDER BY c.class_date, c.class_time"
        
        cursor.execute(query, params)
        rows = cursor.fetchall()
        conn.close()
        return rows

    def add_class(self, class_data):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            """
            INSERT INTO classes (class_name, class_type, description, class_date, class_time,
                               duration_minutes, capacity, trainer_id, branch_id, zone_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """,
            class_data
        )
        
        class_id = cursor.lastrowid
        conn.commit()
        conn.close()
        return class_id

    def update_class(self, class_id, class_data):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            """
            UPDATE classes 
            SET class_name=?, class_type=?, description=?, class_date=?, class_time=?,
                duration_minutes=?, capacity=?, trainer_id=?, branch_id=?, zone_id=?, status=?
            WHERE class_id=?
            """,
            (*class_data, class_id)
        )
        
        conn.commit()
        conn.close()

    def delete_class(self, class_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        # Delete registrations first
        cursor.execute("DELETE FROM class_registrations WHERE class_id = ?", (class_id,))
        # Then delete the class
        cursor.execute("DELETE FROM classes WHERE class_id = ?", (class_id,))
        
        conn.commit()
        conn.close()

    def get_class_by_id(self, class_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            """
            SELECT c.*, s.first_name || ' ' || s.last_name as trainer_name,
                   b.branch_name, z.zone_name
            FROM classes c
            LEFT JOIN staff s ON c.trainer_id = s.staff_id
            LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
            WHERE c.class_id = ?
            """,
            (class_id,)
        )
        
        row = cursor.fetchone()
        conn.close()
        return row

    # ---------- CLASS REGISTRATION METHODS ----------
    def register_member_for_class(self, class_id, member_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        # Check if class exists and has capacity
        cursor.execute("SELECT capacity FROM classes WHERE class_id = ?", (class_id,))
        class_info = cursor.fetchone()
        
        if not class_info:
            conn.close()
            return False, "Class not found"
        
        capacity = class_info[0]
        
        # Check current enrollment
        cursor.execute("SELECT COUNT(*) FROM class_registrations WHERE class_id = ?", (class_id,))
        current_enrollment = cursor.fetchone()[0]
        
        if current_enrollment >= capacity:
            conn.close()
            return False, "Class is full"
        
        # Check if already registered
        cursor.execute("SELECT * FROM class_registrations WHERE class_id = ? AND member_id = ?", 
                      (class_id, member_id))
        if cursor.fetchone():
            conn.close()
            return False, "Member already registered for this class"
        
        # Register member
        cursor.execute(
            "INSERT INTO class_registrations (class_id, member_id) VALUES (?, ?)",
            (class_id, member_id)
        )
        
        conn.commit()
        conn.close()
        return True, "Registration successful"

    def get_class_registrations(self, class_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            """
            SELECT m.member_id, m.first_name || ' ' || m.last_name as member_name,
                   m.email, m.phone, cr.registration_date
            FROM class_registrations cr
            JOIN members m ON cr.member_id = m.member_id
            WHERE cr.class_id = ?
            ORDER BY cr.registration_date DESC
            """,
            (class_id,)
        )
        
        rows = cursor.fetchall()
        conn.close()
        return rows

    def cancel_registration(self, class_id, member_id):
        conn = self.get_connection()
        cursor = conn.cursor()
        
        cursor.execute(
            "DELETE FROM class_registrations WHERE class_id = ? AND member_id = ?",
            (class_id, member_id)
        )
        
        conn.commit()
        conn.close()

    # ---------- UTILITY METHODS ----------
    def get_branch_names(self):
        """Return a list of all branch names"""
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT branch_name FROM gym_branches ORDER BY branch_name")
        rows = cursor.fetchall()
        conn.close()
        return [row[0] for row in rows]

    def get_branch_id_by_name(self, branch_name):
        """Return the branch_id for a given branch name"""
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT branch_id FROM gym_branches WHERE branch_name = ?", (branch_name,)
        )
        row = cursor.fetchone()
        conn.close()
        return row[0] if row else None

    def get_zones_by_branch(self, branch_id):
        """Return list of zones for a given branch_id"""
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT zone_id, zone_name, zone_type FROM workout_zones WHERE branch_id = ? ORDER BY zone_name",
            (branch_id,),
        )
        rows = cursor.fetchall()
        conn.close()
        return rows

    def get_zone_id_by_name_and_branch(self, zone_name, branch_id):
        """Return zone_id given zone name and branch_id"""
        conn = self.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT zone_id, zone_type FROM workout_zones WHERE branch_id = ? AND zone_name = ?",
            (branch_id, zone_name),
        )
        row = cursor.fetchone()
        conn.close()
        return row[0] if row else None

    def get_members_by_branch_zone(self, branch_id=None, zone_id=None):
        """Get members filtered by branch and/or zone"""
        conn = self.get_connection()
        cursor = conn.cursor()
        
        query = "SELECT member_id, first_name || ' ' || last_name as name FROM members WHERE status = 'Active'"
        params = []
        
        if branch_id:
            query += " AND branch_id = ?"
            params.append(branch_id)
        
        if zone_id:
            query += " AND zone_id = ?"
            params.append(zone_id)
        
        query += " ORDER BY first_name, last_name"
        
        cursor.execute(query, params)
        rows = cursor.fetchall()
        conn.close()
        return rows