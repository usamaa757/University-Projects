# Smart Gym Management System - Structure Skeleton
# Technologies: Tkinter, HTML/CSS-like styling, SQLite, Matplotlib

import tkinter as tk
from tkinter import ttk
import sqlite3
import matplotlib.pyplot as plt
from tkinter import messagebox
import random
import hashlib
import stripe
import webbrowser
import threading
import time
import datetime
import matplotlib.pyplot as plt
from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
import pandas as pd
stripe.api_key = "sk_test_51Rp9hg2KpCM2h0VE9FCSymAk4DjmDput8L2Uhlue0tILfKK6gq40OGtPKVOFGqGg82arqXmOhjU1ryJWwqUs6GiU008Pfz3Z5t"
# ----------------------------------------
# Colors & Style Config
# ----------------------------------------
COLORS = {
    'primary': '#2c3e50',
    'secondary': '#34495e',
    'accent': '#3498db',
    'success': '#2ecc71',
    'warning': '#f39c12',
    'danger': '#e74c3c',
    'light': '#ecf0f1',
    'dark': '#2c3e50',
    'text': '#2c3e50',
    'text_light': '#7f8c8d',
    'navbar': '#1a252f'
}

# ----------------------------------------
# Database Structure Skeleton
# ----------------------------------------
class Database:
    def __init__(self):
        self.conn = sqlite3.connect("gym_system.db")
        self.conn.row_factory = sqlite3.Row
        self.cursor = self.conn.cursor()
        self.create_tables()

    def create_tables(self):
        queries = [
            """
            CREATE TABLE IF NOT EXISTS admin (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE,
                password TEXT
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE,
                name TEXT,
                email TEXT,
                phone TEXT,
                password TEXT,
                branch_id INTEGER,
                zone_id INTEGER,
                membership_type TEXT,
                subscription_period TEXT,
                health_info TEXT,
                status TEXT DEFAULT 'inactive',
                join_date TEXT DEFAULT CURRENT_TIMESTAMP
            );

            """,
            """
            CREATE TABLE IF NOT EXISTS attendance (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                attendant_id INTEGER,
                check_in DATETIME,
                check_out DATETIME,
                FOREIGN KEY(member_id) REFERENCES members(id),
                FOREIGN KEY(attendant_id) REFERENCES staff(id)
            );


            """,
            """
            
            CREATE TABLE IF NOT EXISTS staff (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                username TEXT,
                role TEXT,
                password TEXT,
                branch_id INTEGER,
                zone_id INTEGER
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS branches (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                city TEXT,
                name TEXT,
                manager TEXT
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS zones (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                branch_id INTEGER,
                name TEXT,
                info TEXT
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS appointments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER NOT NULL,
                branch_id INTEGER NOT NULL,
                zone_id INTEGER NOT NULL,
                trainer_id INTEGER NOT NULL,
                date TEXT NOT NULL,      -- format: 'YYYY-MM-DD'
                time TEXT NOT NULL,      -- format: 'HH:MM'
                status TEXT DEFAULT 'scheduled',  -- scheduled, completed, cancelled
                FOREIGN KEY(member_id) REFERENCES members(id),
                FOREIGN KEY(branch_id) REFERENCES branches(id),
                FOREIGN KEY(zone_id) REFERENCES zones(id),
                FOREIGN KEY(trainer_id) REFERENCES staff(id)
            );

            """,
            """
           CREATE TABLE IF NOT EXISTS classes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                branch_id INTEGER NOT NULL,
                zone_id INTEGER NOT NULL,
                trainer_id INTEGER NOT NULL,
                date TEXT NOT NULL,          -- YYYY-MM-DD
                time TEXT NOT NULL,          -- HH:MM
                status TEXT NOT NULL DEFAULT 'active',  -- active / inactive
                FOREIGN KEY (branch_id) REFERENCES branches(id),
                FOREIGN KEY (zone_id) REFERENCES zones(id),
                FOREIGN KEY (trainer_id) REFERENCES staff(id)
            );

            """,
            """
            CREATE TABLE IF NOT EXISTS class_enrollments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                class_id INTEGER,
                UNIQUE(member_id, class_id),
                FOREIGN KEY(member_id) REFERENCES members(id),
                FOREIGN KEY(class_id) REFERENCES classes(id)
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS membership_plans (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                type TEXT UNIQUE,
                features TEXT,
                price REAL
            )
            """,
            """
            CREATE TABLE IF NOT EXISTS payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                member_id INTEGER,
                membership_id INTEGER,
                amount REAL,
                date TEXT,
                period TEXT,
                FOREIGN KEY(member_id) REFERENCES members(id),
                FOREIGN KEY(membership_id) REFERENCES membership_plans(id)
            );

            
            """,
            """
            CREATE TABLE IF NOT EXISTS equipment (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                last_service TEXT,
                next_service TEXT,
                status TEXT
            );


            """
        ]

        # Execute all SQL queries
        for q in queries:
            self.cursor.execute(q)

        # Insert default membership plans separately (Python code)
        plans = [
            ("Regular", "Access to gym equipment, 1 personal training session per month", 5000),
            ("Premium", "Access to gym equipment, 4 personal training sessions, Sauna access", 10000),
            ("Trial", "Access to gym equipment for 7 days", 0.0)
        ]

        for plan in plans:
            self.cursor.execute(
                "INSERT OR IGNORE INTO membership_plans (type, features, price) VALUES (?, ?, ?)", plan
            )

        # Insert static default admin
        default_password = "123"
        hashed_password = hashlib.sha256(default_password.encode()).hexdigest()

        self.cursor.execute(
            "INSERT OR IGNORE INTO admin (id, username, password) VALUES (1, 'admin', ?)",
            (hashed_password,)
        )
        
        self.conn.commit()

# ----------------------------------------
# Main Application Structure
# ----------------------------------------
class ProfessionalGymManagementSystem:
    def __init__(self, root):
        self.root = root
        self.root.title("M9's Fitness - Smart Gym Management System")
        self.root.geometry("1400x800")
        self.root.configure(bg=COLORS['light'])
        self.db = Database()
        self.current_user = None
        self.current_role = None

        self.show_login_screen()
        
    
    def clear_window(self):
        for widget in self.root.winfo_children():
            widget.destroy()
    
    def verify_password(self, plain_password, hashed_password):
        """Compare plain text password with stored SHA-256 hash"""
        return hashlib.sha256(plain_password.encode()).hexdigest() == hashed_password
    
    def login_user(self, username, password):
        # ----------------- Check Admin -----------------
        self.db.cursor.execute("SELECT * FROM admin WHERE username=?", (username,))
        admin = self.db.cursor.fetchone()
        if admin:
            # admin schema: (id, username, password)
            if self.verify_password(password, admin[2]):
                self.current_user = username
                self.current_role = "admin"
                self.show_dashboard()
                return
            else:
                messagebox.showerror("Login Failed", "Incorrect password!")
                return

        # ----------------- Check Staff -----------------
        self.db.cursor.execute("SELECT * FROM staff WHERE username=?", (username,))
        staff = self.db.cursor.fetchone()
        if staff:
            # staff schema: (id, name, username, role, password, branch_id, zone_id)
            stored_hash = staff[4]
            if self.verify_password(password, stored_hash):
                self.current_user = username
                self.current_role = staff['role']
                self.staff_id = staff['id']
                self.current_zone_id = staff['zone_id']
                self.show_dashboard()
                return
            else:
                messagebox.showerror("Login Failed", "Incorrect password!")
                return

        # ----------------- Check Members -----------------
        self.db.cursor.execute(
            "SELECT * FROM members WHERE username=? AND status != ?",
            (username, "blocked")
        )

        member = self.db.cursor.fetchone()
        if member:
            # members schema: (id, name, username, email, phone, password, membership_type, health_info, status)
            stored_hash = member[5]  # password column
            if self.verify_password(password, stored_hash):
                self.current_user = username
                self.current_role = "member"
                self.current_status = member['status']
                self.show_dashboard()
                return
            else:
                messagebox.showerror("Login Failed", "Incorrect password!")
                return

        # ----------------- Not Found -----------------
        messagebox.showerror("Login Failed", "Username not found!")




    def setup_styles(self):
        self.style = ttk.Style()
        self.style.configure('TFrame', background=COLORS['light'])
        self.style.configure('Nav.TFrame', background=COLORS['navbar'])
        self.style.configure('Title.TLabel', font=('Segoe UI', 20, 'bold'), foreground=COLORS['primary'])

    # ------------------------------ LOGIN ------------------------------
    def show_login_screen(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        frame = ttk.Frame(self.root)
        frame.pack(expand=True)

        ttk.Label(frame, text="Login", style='Title.TLabel').pack(pady=20)

        ttk.Label(frame, text="Username:").pack()
        username_entry = ttk.Entry(frame, width=30)
        username_entry.pack(pady=5)

        ttk.Label(frame, text="Password:").pack()
        password_entry = ttk.Entry(frame, show="*", width=30)
        password_entry.pack(pady=5)

        login_btn = ttk.Button(frame, text="Login", style='Primary.TButton', command=lambda: self.login_user(username_entry.get(), password_entry.get()))
        login_btn.pack(pady=20)

        # Registration link
        register_btn = ttk.Button(frame, text="New Member? Register Here", command=self.show_member_registration)
        register_btn.pack(pady=10)
    
    

    # ------------------------------ NAVBAR ------------------------------

    def create_navbar(self):
        navbar = ttk.Frame(self.root)
        navbar.pack(fill='x')

        role = (self.current_role or "").lower()
        status = None
        if role == "member":
            status = self.current_status
        # Always initialize buttons list
        buttons = []

        # -----------------------------------------
        # ROLE-BASED COMMON BUTTONS
        # -----------------------------------------
        if role == "admin":
            buttons = [
                ("Dashboard", self.show_dashboard),
                ("Staff", self.show_staff_management),
                ("Zones", self.show_zone_management),
                ("Branches", self.show_branch_management),
                ("Classes", self.show_classes),
                ("Reports", self.show_reports)
            ]

        elif role == "manager":
            buttons = [
                ("Dashboard", self.show_dashboard),
                ("Staff", self.show_staff_management),
                ("Branches", self.show_branch_management),
                ("Zones", self.show_zone_management),
                ("Members", self.show_member_list),
                ("Reports", self.show_manager_reports) 
            ]


        elif role == "trainer":
            buttons = [
                ("Dashboard", self.show_dashboard),
                ("My Classes", self.show_trainer_classes),
                ("My Appointments", self.show_trainer_appointments)

            ]

        elif role == "attendant":
            buttons = [
                ("Dashboard", self.show_dashboard),
                ("Attendance", self.show_attendance)
            ]

        
        else:

            if status == 'active':
                buttons = [
                    ("Dashboard", self.show_dashboard),
                    ("My Profile", self.show_member_profile),
                    ("Appointments", self.show_appointments),
                    ("My Classes", self.show_member_classes)
                ]
            else:
                # fallback minimal menu
                buttons = [
                    ("Dashboard", self.show_dashboard)
                ]

        # -----------------------------------------
        # Always add Logout button
        # -----------------------------------------
        buttons.append(("Logout", self.show_login_screen))

        # -----------------------------------------
        # CREATE BUTTONS
        # -----------------------------------------
        for text, command in buttons:
            ttk.Button(navbar, text=text, command=command).pack(side='left', padx=10, pady=5)


    # ------------------------------ PAGES ------------------------------
    def show_dashboard(self):
        # Clear previous content
        for widget in self.root.winfo_children():
            widget.destroy()

        self.create_navbar()

        # Main dashboard frame
        dash = ttk.Frame(self.root, padding=20)
        dash.pack(fill="both", expand=True)

        role = (self.current_role or "").lower()

        # ---------- Title & Subtitle ----------
        if role == "admin":
            title = "Admin Dashboard"
            subtitle = "Welcome, Super Admin!"
        elif role == "manager":
            title = "Manager Dashboard"
            subtitle = "Welcome to your dashboard!"
        elif role == "trainer":
            title = "Trainer Dashboard"
            subtitle = "Welcome to your dashboard!"
        elif role == "attendant":
            title = "Attendant Dashboard"
            subtitle = "Welcome to your dashboard!"
        elif role == "member":
            title = "Member Dashboard"
            subtitle = "Welcome to your dashboard!"
        else:
            title = "Dashboard"
            subtitle = ""

        ttk.Label(dash, text=title, font=("Segoe UI", 20, "bold")).pack(pady=(20, 5))
        ttk.Label(dash, text=subtitle, font=("Segoe UI", 12)).pack(pady=(0, 20))

        # ---------- Stats Section ----------
        stats_frame = ttk.Frame(dash)
        stats_frame.pack(fill="x", pady=(0, 20))

        if role == "admin" or role == "manager":
            # Example: Count total members and staff
            self.db.cursor.execute("SELECT COUNT(*) FROM members")
            total_members = self.db.cursor.fetchone()[0]

            self.db.cursor.execute("SELECT COUNT(*) FROM staff")
            total_staff = self.db.cursor.fetchone()[0]

            ttk.Label(stats_frame, text=f"Total Members: {total_members}", font=("Segoe UI", 12)).pack(anchor="w", pady=2)
            ttk.Label(stats_frame, text=f"Total Staff: {total_staff}", font=("Segoe UI", 12)).pack(anchor="w", pady=2)

        elif role == "trainer":
            # Example: show number of assigned members
            self.db.cursor.execute("SELECT COUNT(*) FROM members WHERE trainer_id=?", (self.current_user_id,))
            assigned_members = self.db.cursor.fetchone()[0]
            ttk.Label(stats_frame, text=f"Assigned Members: {assigned_members}", font=("Segoe UI", 12)).pack(anchor="w", pady=2)

        elif role == "attendant":
            # Example: show number of members in assigned zone
            self.db.cursor.execute("SELECT COUNT(*) FROM members WHERE zone_id=?", (self.current_zone_id,))
            zone_members = self.db.cursor.fetchone()[0]
            ttk.Label(stats_frame, text=f"Members in Your Zone: {zone_members}", font=("Segoe UI", 12)).pack(anchor="w", pady=2)
            # ---------- Role-specific content ----------
            content_frame = ttk.Frame(dash)
            content_frame.pack(fill="both", expand=True)

        elif role == "member":
            title = "Member Dashboard"
            subtitle = "Welcome to your dashboard!"
            title_label = ttk.Label(self.root, text=title, font=("Segoe UI", 20, "bold"))
            title_label.pack(pady=(20, 5))

            subtitle_label = ttk.Label(self.root, text=subtitle, font=("Segoe UI", 12))
            subtitle_label.pack(pady=(0, 20))

            # Fetch member full profile
            self.db.cursor.execute("""
                SELECT m.*, b.city, b.name AS branch_name, 
                    z.name AS zone_name
                FROM members m
                LEFT JOIN branches b ON m.branch_id = b.id
                LEFT JOIN zones z ON m.zone_id = z.id
                WHERE m.username=?
            """, (self.current_user,))
            member = self.db.cursor.fetchone()

            if not member:
                messagebox.showerror("Error", "Member not found!")
                return

            # Fetch plan details
            self.db.cursor.execute(
                "SELECT id, features, price FROM membership_plans WHERE type=?",
                (member["membership_type"],)
            )
            plan = self.db.cursor.fetchone()

            dash = ttk.Frame(self.root, padding=20)
            dash.pack(fill="both", expand=True)

            # ---------- CONTAINER ----------
            container = ttk.Frame(dash)
            container.pack(fill="both", expand=True, padx=10, pady=10)

            left_frame = ttk.Frame(container)
            right_frame = ttk.Frame(container)
            left_frame.pack(side="left", fill="both", expand=True, padx=10)
            right_frame.pack(side="right", fill="both", expand=True, padx=10)

            # ---------- PROFILE CARD ----------
            profile_card = ttk.Frame(left_frame, style="Card.TFrame", padding=20)
            profile_card.pack(fill="x", pady=10)

            ttk.Label(profile_card, text="Profile Information", style="Header.TLabel").pack(anchor="w", pady=(0, 10))

            profile_items = [
                ("Name", member["name"]),
                ("Status", member["status"]),
                ("Health Info", member["health_info"]),
                ("Branch", f"{member['city']} - {member['branch_name']}"),
                ("Zone", member["zone_name"])
            ]

            for label, value in profile_items:
                row = ttk.Frame(profile_card)
                row.pack(fill="x", pady=3)
                ttk.Label(row, text=f"{label}:", style="Subheader.TLabel", width=15, anchor="w").pack(side="left")
                ttk.Label(row, text=value, style="TLabel").pack(side="left")

            # ---------- MEMBERSHIP CARD ----------
            membership_card = ttk.Frame(right_frame, style="Card.TFrame", padding=20)
            membership_card.pack(fill="x", pady=10)

            ttk.Label(membership_card, text="Membership Details", style="Header.TLabel").pack(anchor="w", pady=(0, 10))

            if plan:
                ttk.Label(membership_card, text=f"Plan: {member['membership_type']}", style="Subheader.TLabel").pack(anchor="w", pady=5)
                ttk.Label(membership_card, text=f"Features:\n{plan['features']}", style="TLabel", wraplength=350, justify="left").pack(anchor="w", pady=5)

            # ---------- Helper: Calculate Remaining Days ----------
            def calculate_remaining_days(join_date, subscription_period):
                if not join_date or not subscription_period:
                    return 0
                period_days = {"Monthly": 30, "Quarterly": 90, "Annual": 365}
                start_date = datetime.datetime.strptime(join_date, "%Y-%m-%d").date()
                total_days = period_days.get(subscription_period, 30)
                end_date = start_date + datetime.timedelta(days=total_days)
                remaining = (end_date - datetime.date.today()).days
                return max(remaining, 0)

            remaining_days = calculate_remaining_days(member["join_date"], member["subscription_period"])

            # If membership expired, set status to inactive in DB
            if member["status"].lower() == "active" and remaining_days == 0:
                self.db.cursor.execute(
                    "UPDATE members SET status='inactive' WHERE id=?",
                    (member["id"],)
                )
                self.db.conn.commit()
                display_status = "inactive"
            else:
                display_status = member["status"].lower()


            # If remaining days > 0, show active membership
            display_status = member["status"].lower()
            if display_status == "active" and remaining_days > 0:
                ttk.Label(membership_card, text=f"Subscription Period: {member['subscription_period']}", style="TLabel").pack(anchor="w", pady=5)
                ttk.Label(membership_card, text=f"Membership Remaining: {remaining_days} days", style="TLabel").pack(anchor="w", pady=5)
            else:
                display_status = "inactive"  # use local variable
                ttk.Label(membership_card, text="Your membership has expired. Renew now:", style="Subheader.TLabel").pack(anchor="w", pady=5)

                period_var = tk.StringVar(value="Monthly")
                period_dropdown = ttk.Combobox(
                    membership_card, textvariable=period_var,
                    values=["Monthly", "Quarterly", "Annual"],
                    state="readonly", width=20
                )
                period_dropdown.pack(anchor="w", pady=5)

                def calculate_total(price, period):
                    if period == "Monthly": return price
                    if period == "Quarterly": return price * 3
                    if period == "Annual": return price * 12

                total_price_var = tk.StringVar()
                total_price_var.set(f"Total Price: Pkr{calculate_total(plan['price'], period_var.get()):.2f}")
                price_label = ttk.Label(membership_card, textvariable=total_price_var, style="TLabel")
                price_label.pack(anchor="w", pady=5)

                period_var.trace("w", lambda *args: total_price_var.set(f"Total Price: Pkr{calculate_total(plan['price'], period_var.get()):.2f}"))

                # Pay Now button remains the same


                def pay_now():
                    period = period_var.get()
                    total_price = calculate_total(plan['price'], period)  # in PKR

                    # Convert to USD
                    PKR_TO_USD = 0.0036
                    usd_amount = total_price * PKR_TO_USD

                    try:
                        session = stripe.checkout.Session.create(
                            payment_method_types=['card'],
                            line_items=[{
                                'price_data': {
                                    'currency': 'usd',
                                    'product_data': {'name': f"{member['membership_type']} Plan - {period}"},
                                    'unit_amount': int(usd_amount * 100),  # Stripe amount in cents
                                },
                                'quantity': 1,
                            }],
                            mode='payment',
                            success_url='https://example.com/success',
                            cancel_url='https://example.com/cancel',
                            metadata={'username': self.current_user, 'period': period}
                        )
                        webbrowser.open(session.url)
                        ...


                        def check_payment():
                            while True:
                                s = stripe.checkout.Session.retrieve(session.id)
                                if s.payment_status == 'paid':
                                    def update_member_and_log():
                                        # Extend join_date if already active, else set today
                                        current_join = member["join_date"]
                                        if member["status"].lower() == "active" and current_join:
                                            join_date = datetime.datetime.strptime(current_join, "%Y-%m-%d").date()
                                        else:
                                            join_date = datetime.date.today()

                                        # Update member
                                        self.db.cursor.execute(
                                            "UPDATE members SET status='active', subscription_period=?, join_date=? WHERE username=?",
                                            (period, join_date.isoformat(), self.current_user)
                                        )

                                        # Insert payment record
                                        self.db.cursor.execute(
                                            "INSERT INTO payments (member_id, membership_id, amount, date, period) VALUES (?, ?, ?, ?, ?)",
                                            (member["id"], plan["id"], total_price, datetime.date.today().isoformat(), period)
                                        )
                                        self.db.conn.commit()
                                        messagebox.showinfo("Success", "Membership Activated & Payment Logged!")
                                        self.show_dashboard()
                                    self.root.after(0, update_member_and_log)
                                    break
                                time.sleep(4)

                        threading.Thread(target=check_payment, daemon=True).start()
                    except Exception as e:
                        messagebox.showerror("Payment Error", str(e))

                ttk.Button(membership_card, text="Pay Now", style="Accent.TButton", command=pay_now).pack(pady=20)


    def show_member_profile(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        self.create_navbar()

        frame = ttk.Frame(self.root)
        frame.pack(expand=True, padx=20, pady=20)

        # Fetch member data
        self.db.cursor.execute(
            "SELECT * FROM members WHERE username=?",
            (self.current_user,)
        )
        member = self.db.cursor.fetchone()

        if member:
            ttk.Label(frame, text="My Profile", style='Title.TLabel').pack(pady=20)

            # Editable fields
            ttk.Label(frame, text="Full Name:").pack(pady=2)
            name_entry = ttk.Entry(frame)
            name_entry.insert(0, member['name'])
            name_entry.pack(pady=5)

            ttk.Label(frame, text="Email:").pack(pady=2)
            email_entry = ttk.Entry(frame)
            email_entry.insert(0, member['email'])
            email_entry.pack(pady=5)

            ttk.Label(frame, text="Phone:").pack(pady=2)
            phone_entry = ttk.Entry(frame)
            phone_entry.insert(0, member['phone'])
            phone_entry.pack(pady=5)

            ttk.Label(frame, text="Health Info:").pack(pady=2)
            health_entry = ttk.Entry(frame)
            health_entry.insert(0, member['health_info'])
            health_entry.pack(pady=5)

            # Optional password field
            ttk.Label(frame, text="New Password (optional):").pack(pady=2)
            password_entry = ttk.Entry(frame, show="*")
            password_entry.pack(pady=5)

            # Update button
            def update_profile():
                name = name_entry.get().strip()
                email = email_entry.get().strip()
                phone = phone_entry.get().strip()
                health = health_entry.get().strip()
                new_password = password_entry.get().strip()

                if not all([name, email, phone]):
                    messagebox.showwarning("Validation Error", "Name, Email, and Phone are required!")
                    return

                try:
                    if new_password:
                        # Hash the new password
                        hashed_password = self.hash_password(new_password)
                        self.db.cursor.execute(
                            "UPDATE members SET name=?, email=?, phone=?, health_info=?, password=? WHERE username=?",
                            (name, email, phone, health, hashed_password, self.current_user)
                        )
                    else:
                        self.db.cursor.execute(
                            "UPDATE members SET name=?, email=?, phone=?, health_info=? WHERE username=?",
                            (name, email, phone, health, self.current_user)
                        )

                    self.db.conn.commit()
                    messagebox.showinfo("Success", "Profile updated successfully!")
                    self.show_member_profile()  # refresh

                except Exception as e:
                    messagebox.showerror("Error", f"Failed to update profile: {str(e)}")

            ttk.Button(frame, text="Update Profile", command=update_profile, style='Success.TButton').pack(pady=20)

        else:
            ttk.Label(frame, text="Member profile not found!", font=('Segoe UI', 12)).pack(pady=20)

    
    def show_member_registration(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        frame = ttk.Frame(self.root)
        frame.pack(expand=True)

        ttk.Label(frame, text="Member Registration", style='Title.TLabel').pack(pady=20)

        # Name
        ttk.Label(frame, text="Full Name:").pack()
        name_entry = ttk.Entry(frame, width=40)
        name_entry.pack(pady=5)

        # Email
        ttk.Label(frame, text="Email:").pack()
        email_entry = ttk.Entry(frame, width=40)
        email_entry.pack(pady=5)

        # Phone
        ttk.Label(frame, text="Phone Number:").pack()
        phone_entry = ttk.Entry(frame, width=40)
        phone_entry.pack(pady=5)
        
                # --- Branch Dropdown ---
        ttk.Label(frame, text="Select Branch:").pack(pady=5)
        branch_var = tk.StringVar()

        # Fetch branches
        self.db.cursor.execute("SELECT id, name, city FROM branches")
        branches = self.db.cursor.fetchall()

        branch_dropdown = ttk.Combobox(
            frame, textvariable=branch_var,
            values=[f"{b['id']} - {b['city']} ({b['name']})" for b in branches],
            state="readonly", width=40
        )
        branch_dropdown.pack()

        # --- Zone Dropdown ---
        ttk.Label(frame, text="Select Zone:").pack(pady=5)
        zone_var = tk.StringVar()
        zone_dropdown = ttk.Combobox(frame, textvariable=zone_var, state="readonly", width=40)
        zone_dropdown.pack(pady=5)

        # ---------------------------
        #  FIX: Load Zones Function
        # ---------------------------
        def load_zones(event=None):
            selected = branch_var.get()
            if not selected:
                return

            branch_id = int(selected.split(" - ")[0])

            self.db.cursor.execute("SELECT id, name FROM zones WHERE branch_id=?", (branch_id,))
            zones = self.db.cursor.fetchall()

            zone_dropdown['values'] = [f"{z['id']} - {z['name']}" for z in zones]
            zone_dropdown.set("")
        
        # Bind AFTER function definition
        branch_dropdown.bind("<<ComboboxSelected>>", load_zones)



        # Membership Type
        ttk.Label(frame, text="Membership Type:").pack()
        membership_type = ttk.Combobox(frame, values=["Regular", "Premium", "Trial"], width=37)
        membership_type.pack(pady=5)

        # Health Info
        ttk.Label(frame, text="Health Information:").pack()
        health_entry = ttk.Entry(frame, width=40)
        health_entry.pack(pady=5)

        # Password
        ttk.Label(frame, text="Password:").pack()
        password_entry = ttk.Entry(frame, width=40, show="*")
        password_entry.pack(pady=5)

        # Register button
        def register_member():
            name = name_entry.get().strip()
            email = email_entry.get().strip()
            phone = phone_entry.get().strip()
            membership = membership_type.get().strip()
            health = health_entry.get().strip()
            password = password_entry.get().strip()
            branch_selected = branch_var.get().strip()
            zone_selected = zone_var.get().strip()

            # Required validation
            if not all([name, email, phone, membership, password, branch_selected, zone_selected]):
                messagebox.showwarning("Validation Error", "Please fill all required fields!")
                return

            # Extract IDs
            branch_id = int(branch_selected.split(" - ")[0])
            zone_id = int(zone_selected.split(" - ")[0])

            # Generate username
            first_name = name.lower().split()[0] if name else "user"
            rand_num = random.randint(1000, 9999)
            username = f"{first_name}{rand_num}"

            # Hash password
            hashed_password = hashlib.sha256(password.encode()).hexdigest()

            # Insert into DB
            try:
                self.db.cursor.execute(
                    """
                    INSERT INTO members 
                    (name, username, email, phone, membership_type, health_info, password, branch_id, zone_id, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'inactive')
                    """,
                    (name, username, email, phone, membership, health, hashed_password, branch_id, zone_id)
                )
                self.db.conn.commit()

                messagebox.showinfo(
                    "Success",
                    f"Member {name} registered!\n\nUsername: {username}"
                )
                self.show_login_screen()

            except Exception as e:
                messagebox.showerror("Error", f"Failed to register member:\n{str(e)}")


        ttk.Button(frame, text="Register", style='Success.TButton', command=register_member).pack(pady=20)
        
                # Registration link
        login_btn = ttk.Button(frame, text="Aleardy have account? Login Here", command=self.show_login_screen)
        login_btn.pack(pady=10)
    
    def show_member_list(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        title = ttk.Label(frame, text="Members List", font=("Segoe UI", 18, "bold"))
        title.pack(pady=10)

        # ------------------ Fetch Members ------------------
        self.db.cursor.execute("""
            SELECT id, name, username, email, phone, status, membership_type
            FROM members
            ORDER BY name
        """)
        members = self.db.cursor.fetchall()

        columns = ("ID", "Name", "Username", "Email", "Phone", "Status", "Membership")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=15)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=120)

        for m in members:
            table.insert("", "end", values=(
                m["id"], m["name"], m["username"], m["email"], m["phone"], m["status"], m["membership_type"]
            ))

        table.pack(pady=10, fill="x")

        # ------------------ Update Member Frame ------------------
        update_frame = ttk.LabelFrame(frame, text="Update Member", padding=10)
        update_frame.pack(fill="x", pady=20)

        ttk.Label(update_frame, text="Select Member ID:").grid(row=0, column=0, padx=5, pady=5)
        member_id_var = tk.StringVar()
        member_cb = ttk.Combobox(update_frame, textvariable=member_id_var,
                                values=[str(m["id"]) for m in members], state="readonly", width=20)
        member_cb.grid(row=0, column=1, padx=5, pady=5)

        ttk.Label(update_frame, text="Set Status:").grid(row=1, column=0, padx=5, pady=5)
        status_var = tk.StringVar()
        status_cb = ttk.Combobox(update_frame, textvariable=status_var, values=["active", "blocked"], state="readonly", width=20)
        status_cb.grid(row=1, column=1, padx=5, pady=5)

        ttk.Label(update_frame, text="Set Password (optional):").grid(row=2, column=0, padx=5, pady=5)
        password_entry = ttk.Entry(update_frame, width=22, show="*")
        password_entry.grid(row=2, column=1, padx=5, pady=5)

        def update_member():
            member_id = member_id_var.get()
            status = status_var.get()
            password = password_entry.get().strip()

            if not member_id or not status:
                messagebox.showerror("Error", "Select member ID and status!")
                return

            try:
                # Update status
                self.db.cursor.execute(
                    "UPDATE members SET status=? WHERE id=?",
                    (status, member_id)
                )

                # Update password if entered
                if password:
                    import hashlib
                    hashed = hashlib.sha256(password.encode()).hexdigest()
                    self.db.cursor.execute(
                        "UPDATE members SET password=? WHERE id=?",
                        (hashed, member_id)
                    )

                self.db.conn.commit()
                messagebox.showinfo("Success", "Member updated successfully!")
                self.show_member_list()  # refresh list

            except Exception as e:
                messagebox.showerror("Error", f"Failed to update member:\n{str(e)}")

        ttk.Button(update_frame, text="Update Member", style="Accent.TButton", command=update_member).grid(row=3, column=0, columnspan=2, pady=10)

        
    def show_staff_management(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        self.create_navbar()

        frame = ttk.Frame(self.root)
        frame.pack(fill='both', expand=True, padx=20, pady=20)

        ttk.Label(frame, text="Staff Management", style='Title.TLabel').pack(pady=10)

        # Table
        columns = ("Username", "Name", "Role", "Branch", "Zone")
        table = ttk.Treeview(frame, columns=columns, show='headings', height=15)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=200)

        table.pack(pady=20)

        # Load staff data
        self.db.cursor.execute("""
            SELECT 
                staff.id,
                staff.username,
                staff.name,
                staff.role,
                branches.city || ' - ' || branches.name AS branch_name,
                zones.name AS zone_name
            FROM staff
            LEFT JOIN branches ON staff.branch_id = branches.id
            LEFT JOIN zones ON staff.zone_id = zones.id
        """)
        rows = self.db.cursor.fetchall()


        for r in rows:
            table.insert('', 'end', values=tuple(r))

        # Buttons area
        btn_frame = ttk.Frame(frame)
        btn_frame.pack(pady=10)

        # ---------------- ADD STAFF POPUP ----------------
        

        def add_staff():
            win = tk.Toplevel(self.root)
            win.title("Add Staff")
            win.geometry("400x400")

            ttk.Label(win, text="Full Name:").pack(pady=5)
            name_e = ttk.Entry(win)
            name_e.pack(pady=5)

            ttk.Label(win, text="Role:").pack(pady=5)
            role_e = ttk.Combobox(win, values=["Manager", "Trainer", "Attendant"])
            role_e.pack(pady=5)

            # Branch dropdown
            ttk.Label(win, text="Branch:").pack(pady=5)
            self.db.cursor.execute("SELECT id, city, name FROM branches")
            branches = self.db.cursor.fetchall()
            branch_dict = {f"{b[1]} - {b[2]}": b[0] for b in branches}  # Display text: ID
            branch_dropdown = ttk.Combobox(win, values=list(branch_dict.keys()))
            branch_dropdown.pack(pady=5)

            # Zone dropdown, updated dynamically based on branch
            ttk.Label(win, text="Zone:").pack(pady=5)
            zone_dropdown = ttk.Combobox(win)
            zone_dropdown.pack(pady=5)
            

            def update_zones(event):
                branch_text = branch_dropdown.get()
                branch_id = branch_dict.get(branch_text)
                self.db.cursor.execute("SELECT id, name FROM zones WHERE branch_id=?", (branch_id,))
                zones = self.db.cursor.fetchall()

                zone_dict = {z[1]: z[0] for z in zones}
                zone_dropdown['values'] = list(zone_dict.keys())

                if zones:
                    zone_dropdown.current(0)
                else:
                    zone_dropdown.set('')

            branch_dropdown.bind("<<ComboboxSelected>>", update_zones)
            ttk.Label(win, text="Password").pack(pady=5)
            password_e = ttk.Entry(win, show="*")
            password_e.pack(pady=5)
            
            def save():
                full_name = name_e.get()
                role = role_e.get()
                branch_text = branch_dropdown.get()
                zone_text = zone_dropdown.get()  # <-- Get selected zone text

                branch_id = branch_dict.get(branch_text)
                
                # Fetch zone_id from zones selected
                self.db.cursor.execute("SELECT id FROM zones WHERE name=? AND branch_id=?", (zone_text, branch_id))
                zone_row = self.db.cursor.fetchone()
                if zone_row:
                    zone_id = zone_row[0]
                else:
                    zone_id = None  # optional: handle no zone selected

                password = password_e.get()
                # Generate username 
                first_name = full_name.split()[0] if full_name else "user"
                rand_num = random.randint(1000, 9999)
                username = f"{first_name}{rand_num}"
                hashed_password = hashlib.sha256(password.encode()).hexdigest()

                # Insert staff with zone_id
                self.db.cursor.execute(
                    "INSERT INTO staff (name, role, branch_id, zone_id, username, password) VALUES (?, ?, ?, ?, ?, ?)",
                    (full_name, role, branch_id, zone_id, username, hashed_password)
                )
                self.db.conn.commit()

                messagebox.showinfo("Staff Added", f"Username: {username}")
                win.destroy()
                self.show_staff_management()


            ttk.Button(win, text="Save", command=save).pack(pady=20)


        # ---------------- DELETE STAFF ----------------
        def delete_staff():
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Warning", "Select a staff record to delete!")
                return

            staff_id = table.item(selected[0])['values'][0]  # ID is first column now
            confirm = messagebox.askyesno("Confirm Delete", "Are you sure you want to delete this staff?")
            if not confirm:
                return

            self.db.cursor.execute("DELETE FROM staff WHERE id=?", (staff_id,))
            self.db.conn.commit()
            messagebox.showinfo("Deleted", "Staff record deleted successfully!")
            self.show_staff_management()



        ttk.Button(btn_frame, text="Add Staff", command=add_staff).pack(side='left', padx=10)
        ttk.Button(btn_frame, text="Delete Staff", command=delete_staff).pack(side='left', padx=10)

    def show_zone_management(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        self.create_navbar()  # Keep navbar

        frame = ttk.Frame(self.root)
        frame.pack(fill='both', expand=True, padx=20, pady=20)

        ttk.Label(frame, text="Workout Zone Management", style='Title.TLabel').pack(pady=10)

        # Table
        columns = ("ID", "Branch", "Zone Name", "Info")
        table = ttk.Treeview(frame, columns=columns, show='headings', height=15)
        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=200)
        table.pack(pady=20, fill='x')

        # Load zones data with branch name
        self.db.cursor.execute("""
            SELECT zones.id, branches.name || ' - ' || branches.city, zones.name, zones.info
            FROM zones
            JOIN branches ON zones.branch_id = branches.id
        """)
        rows = self.db.cursor.fetchall()
        for r in rows:
            table.insert('', 'end', values=tuple(r))

        # Buttons frame
        btn_frame = ttk.Frame(frame)
        btn_frame.pack(pady=10)

        # ---------------- ADD ZONE POPUP ----------------
        def add_zone():
            win = tk.Toplevel(self.root)
            win.title("Add Zone")
            win.geometry("400x350")

            # Branch dropdown
            ttk.Label(win, text="Select Branch:").pack(pady=5)
            self.db.cursor.execute("SELECT id, name, city FROM branches")
            branches = self.db.cursor.fetchall()
            branch_dict = {f"{b[1]} - {b[2]}": b[0] for b in branches}  # Display text: ID
            branch_dropdown = ttk.Combobox(win, values=list(branch_dict.keys()))
            branch_dropdown.pack(pady=5)

            # Zone Name
            ttk.Label(win, text="Zone Name:").pack(pady=5)
            zone_name_e = ttk.Entry(win)
            zone_name_e.pack(pady=5)

            # Info
            ttk.Label(win, text="Info / Updates / Schedule / Promotions:").pack(pady=5)
            info_e = ttk.Entry(win)
            info_e.pack(pady=5)

            def save():
                branch_text = branch_dropdown.get()
                branch_id = branch_dict.get(branch_text)
                zone_name = zone_name_e.get()
                info = info_e.get()

                if not branch_id or not zone_name:
                    messagebox.showwarning("Warning", "Branch and Zone Name are required!")
                    return

                self.db.cursor.execute(
                    "INSERT INTO zones (branch_id, name, info) VALUES (?, ?, ?)",
                    (branch_id, zone_name, info)
                )
                self.db.conn.commit()
                win.destroy()
                self.show_zone_management()

            ttk.Button(win, text="Save", command=save).pack(pady=20)

        # ---------------- DELETE ZONE ----------------
        def delete_zone():
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Warning", "Select a zone to delete!")
                return

            zone_id = table.item(selected[0])['values'][0]
            confirm = messagebox.askyesno("Confirm Delete", "Are you sure you want to delete this zone?")
            if confirm:
                self.db.cursor.execute("DELETE FROM zones WHERE id=?", (zone_id,))
                self.db.conn.commit()
                self.show_zone_management()

        ttk.Button(btn_frame, text="Add Zone", command=add_zone).pack(side='left', padx=10)
        ttk.Button(btn_frame, text="Delete Zone", command=delete_zone).pack(side='left', padx=10)


    def show_branch_management(self):
        for widget in self.root.winfo_children():
            widget.destroy()

        self.create_navbar()  # Keep navbar

        frame = ttk.Frame(self.root)
        frame.pack(fill='both', expand=True, padx=20, pady=20)

        ttk.Label(frame, text="Branch Management", style='Title.TLabel').pack(pady=10)

        # Table
        columns = ("ID", "Branch Name", "City")
        table = ttk.Treeview(frame, columns=columns, show='headings', height=15)
        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=250)
        table.pack(pady=20, fill='x')

        # Load branches data
        self.db.cursor.execute("SELECT id, name, city FROM branches")
        rows = self.db.cursor.fetchall()
        for r in rows:
            table.insert('', 'end', values=(r[0], r[1], r[2]))

        # Buttons frame
        btn_frame = ttk.Frame(frame)
        btn_frame.pack(pady=10)

        # ---------------- ADD BRANCH POPUP ----------------
        def add_branch():
            win = tk.Toplevel(self.root)
            win.title("Add Branch")
            win.geometry("400x250")

            ttk.Label(win, text="Branch Name:").pack(pady=5)
            branch_name_e = ttk.Entry(win)
            branch_name_e.pack(pady=5)

            ttk.Label(win, text="City:").pack(pady=5)
            city_dropdown = ttk.Combobox(win, values=["Lahore", "Islamabad", "Rawalpindi", "Karachi", "Peshawar"])
            city_dropdown.pack(pady=5)

            def save():
                branch_name = branch_name_e.get()
                city = city_dropdown.get()

                if not branch_name or not city:
                    messagebox.showwarning("Warning", "All fields are required!")
                    return

                self.db.cursor.execute(
                    "INSERT INTO branches (name, city) VALUES (?, ?)",
                    (branch_name, city)
                )
                self.db.conn.commit()
                win.destroy()
                self.show_branch_management()

            ttk.Button(win, text="Save", command=save).pack(pady=20)

        # ---------------- DELETE BRANCH ----------------
        def delete_branch():
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Warning", "Select a branch to delete!")
                return

            branch_id = table.item(selected[0])['values'][0]
            confirm = messagebox.askyesno("Confirm Delete", "Are you sure you want to delete this branch?")
            if confirm:
                self.db.cursor.execute("DELETE FROM branches WHERE id=?", (branch_id,))
                self.db.conn.commit()
                self.show_branch_management()

        ttk.Button(btn_frame, text="Add Branch", command=add_branch).pack(side='left', padx=10)
        ttk.Button(btn_frame, text="Delete Branch", command=delete_branch).pack(side='left', padx=10)
        
    def show_attendance(self):
        for widget in self.root.winfo_children():
            widget.destroy()
        self.create_navbar()

        dash = ttk.Frame(self.root)
        dash.pack(expand=True, fill='both', padx=20, pady=20)

        ttk.Label(dash, text="Attendance Dashboard", style='Title.TLabel').pack(pady=10)

        # ---------------------------------------------------------
        # FETCH THE STAFF ZONE
        # ---------------------------------------------------------
        self.db.cursor.execute("SELECT zone_id FROM staff WHERE id=?", (self.staff_id,))
        staff_zone = self.db.cursor.fetchone()['zone_id']

        # ---------------------------------------------------------
        # FETCH ACTIVE MEMBERS ONLY FROM THIS ZONE
        # ---------------------------------------------------------
        self.db.cursor.execute("""
            SELECT id, username, name 
            FROM members 
            WHERE status='active' AND zone_id=?
        """, (staff_zone,))
        members = self.db.cursor.fetchall()

        member_dict = {m['id']: {'username': m['username'], 'name': m['name']} for m in members}

        # ---------------------------------------------------------
        # FETCH MEMBERS CURRENTLY CHECKED-IN FROM THIS ZONE
        # ---------------------------------------------------------
        self.db.cursor.execute("""
            SELECT member_id, check_in
            FROM attendance 
            WHERE check_out IS NULL
            AND member_id IN (SELECT id FROM members WHERE zone_id=?)
        """, (staff_zone,))
        checked_in_records = self.db.cursor.fetchall()
        checked_in_ids = [r['member_id'] for r in checked_in_records]

        # Members not checked-in
        not_checked_in = [m for m in members if m['id'] not in checked_in_ids]

        # ---------------------------------------------------------
        # FETCH CHECKED-OUT HISTORY FOR THIS ZONE
        # ---------------------------------------------------------
        self.db.cursor.execute("""
            SELECT member_id, check_in, check_out
            FROM attendance
            WHERE check_out IS NOT NULL
            AND member_id IN (SELECT id FROM members WHERE zone_id=?)
            ORDER BY check_in DESC
        """, (staff_zone,))
        checked_out_records = self.db.cursor.fetchall()

        # ---------------------------------------------------------
        # LEFT & RIGHT PANELS
        # ---------------------------------------------------------
        table_frame = ttk.Frame(dash)
        table_frame.pack(fill='both', expand=True)

        # ---------------------------------------------------------
        # LEFT = CHECKED-IN MEMBERS
        # ---------------------------------------------------------
        left_frame = ttk.Frame(table_frame)
        left_frame.pack(side="left", fill="both", expand=True, padx=10)

        ttk.Label(left_frame, text="Checked-In Members", font=('Segoe UI', 12, 'bold')).pack(pady=5)

        in_container = ttk.Frame(left_frame)
        in_container.pack(fill="both", expand=True)

        in_canvas = tk.Canvas(in_container)
        in_scroll = ttk.Scrollbar(in_container, orient="vertical", command=in_canvas.yview)
        in_canvas.configure(yscrollcommand=in_scroll.set)

        in_scroll.pack(side="right", fill="y")
        in_canvas.pack(side="left", fill="both", expand=True)

        in_frame = ttk.Frame(in_canvas)
        in_canvas.create_window((0, 0), window=in_frame, anchor="nw")

        in_table = ttk.Treeview(
            in_frame,
            columns=("Username", "Name", "Check-In"),
            show="headings",
            height=10
        )
        for col in ("Username", "Name", "Check-In"):
            in_table.heading(col, text=col)
            in_table.column(col, width=180)

        in_table.pack(fill="both", expand=True)

        for r in checked_in_records:
            m = member_dict[r['member_id']]
            in_table.insert('', 'end', values=(m['username'], m['name'], r['check_in']))

        in_frame.update_idletasks()
        in_canvas.config(scrollregion=in_canvas.bbox("all"))

        # ---------------------------------------------------------
        # RIGHT = CHECKED-OUT MEMBERS
        # ---------------------------------------------------------
        right_frame = ttk.Frame(table_frame)
        right_frame.pack(side="right", fill="both", expand=True, padx=10)

        ttk.Label(right_frame, text="Checked-Out Members", font=('Segoe UI', 12, 'bold')).pack(pady=5)

        out_container = ttk.Frame(right_frame)
        out_container.pack(fill="both", expand=True)

        out_canvas = tk.Canvas(out_container)
        out_scroll = ttk.Scrollbar(out_container, orient="vertical", command=out_canvas.yview)
        out_canvas.configure(yscrollcommand=out_scroll.set)

        out_scroll.pack(side="right", fill="y")
        out_canvas.pack(side="left", fill="both", expand=True)

        out_frame = ttk.Frame(out_canvas)
        out_canvas.create_window((0, 0), window=out_frame, anchor="nw")

        out_table = ttk.Treeview(
            out_frame,
            columns=("Username", "Name", "Check-In", "Check-Out"),
            show="headings",
            height=10
        )
        for col in ("Username", "Name", "Check-In", "Check-Out"):
            out_table.heading(col, text=col)
            out_table.column(col, width=160)

        out_table.pack(fill="both", expand=True)

        for r in checked_out_records:
            m = member_dict[r['member_id']]
            out_table.insert('', 'end', values=(m['username'], m['name'], r['check_in'], r['check_out']))

        out_frame.update_idletasks()
        out_canvas.config(scrollregion=out_canvas.bbox("all"))

        # ---------------------------------------------------------
        # BOTTOM = NOT CHECKED-IN MEMBERS
        # ---------------------------------------------------------
        ttk.Label(dash, text="Members Not Checked-In Yet",
                font=('Segoe UI', 12, 'bold')).pack(pady=10)

        not_in_table = ttk.Treeview(
            dash,
            columns=("Username", "Name"),
            show="headings",
            height=5
        )
        not_in_table.heading("Username", text="Username")
        not_in_table.heading("Name", text="Name")

        for col in ("Username", "Name"):
            not_in_table.column(col, width=250)

        not_in_table.pack(fill="x")

        for m in not_checked_in:
            not_in_table.insert('', 'end', values=(m['username'], m['name']))

        # ---------------------------------------------------------
        # ACTION AREA (IN / OUT)
        # ---------------------------------------------------------
        ttk.Label(dash, text="Select Member:", font=('Segoe UI', 12)).pack(pady=10)
        member_var = tk.StringVar()

        member_dropdown = ttk.Combobox(
            dash, textvariable=member_var,
            values=[f"{m['id']} - {m['username']} ({m['name']})" for m in members],
            state="readonly", width=50
        )
        member_dropdown.pack()

        def mark_in():
            selected = member_var.get()
            if not selected:
                messagebox.showwarning("Error", "Select a member!")
                return

            member_id = int(selected.split(" - ")[0])
            now = datetime.datetime.now()

            self.db.cursor.execute(
                "INSERT INTO attendance (member_id, attendant_id, check_in) VALUES (?, ?, ?)",
                (member_id, self.staff_id, now)
            )
            self.db.conn.commit()
            self.show_attendance()

        def mark_out():
            selected = member_var.get()
            if not selected:
                messagebox.showwarning("Error", "Select a member!")
                return

            member_id = int(selected.split(" - ")[0])
            now = datetime.datetime.now()

            self.db.cursor.execute(
                "SELECT id FROM attendance WHERE member_id=? AND check_out IS NULL ORDER BY check_in DESC LIMIT 1",
                (member_id,)
            )
            r = self.db.cursor.fetchone()
            if not r:
                messagebox.showwarning("Error", "No active check-in for this member.")
                return

            self.db.cursor.execute(
                "UPDATE attendance SET check_out=? WHERE id=?",
                (now, r['id'])
            )
            self.db.conn.commit()
            self.show_attendance()

        ttk.Button(dash, text="Check-In", style="Success.TButton", command=mark_in).pack(pady=5)
        ttk.Button(dash, text="Check-Out", style="Danger.TButton", command=mark_out).pack(pady=5)
        
     # ----------------- Show Classes -----------------
    def show_classes(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        title = ttk.Label(frame, text="Manage Gym Classes", font=("Segoe UI", 18, "bold"))
        title.pack(pady=10)

        # --------------------------------------------------
        # Fetch classes for table
        # --------------------------------------------------
        self.db.cursor.execute("""
            SELECT classes.id, classes.name, branches.name AS branch_name,
                zones.name AS zone_name, staff.name AS trainer_name,
                classes.date, classes.time, classes.status
            FROM classes
            JOIN branches ON classes.branch_id = branches.id
            JOIN zones ON classes.zone_id = zones.id
            JOIN staff ON classes.trainer_id = staff.id
            ORDER BY classes.date, classes.time
        """)
        classes = self.db.cursor.fetchall()

        # --------------------------------------------------
        # Table
        # --------------------------------------------------
        columns = ("ID","Name","Branch","Zone","Trainer","Date","Time","Status")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=12)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=130)

        for cls in classes:
            table.insert("", "end", values=(
                cls["id"], cls["name"], cls["branch_name"], cls["zone_name"],
                cls["trainer_name"], cls["date"], cls["time"], cls["status"]
            ))

        table.pack(pady=10, fill="x")

        # --------------------------------------------------
        # Form to Add Class
        # --------------------------------------------------
        form = ttk.Frame(frame)
        form.pack(pady=20)

        # Get dropdown data
        self.db.cursor.execute("SELECT id, name FROM branches")
        branches = self.db.cursor.fetchall()

        self.db.cursor.execute("SELECT id, name FROM zones")
        zones = self.db.cursor.fetchall()

        self.db.cursor.execute("SELECT id, name FROM staff WHERE role='Trainer'")
        trainers = self.db.cursor.fetchall()

        ttk.Label(form, text="Class Name:").grid(row=0, column=0, padx=5, pady=5)
        class_name = ttk.Entry(form, width=25)
        class_name.grid(row=0, column=1, padx=5, pady=5)

                # --------------------------------------------------
        # Dynamic Zone & Trainer Filtering
        # --------------------------------------------------

        def update_zones(event=None):
            """Refresh zone dropdown based on selected branch"""
            try:
                if not branch_cb.get():
                    zone_cb['values'] = []
                    trainer_cb['values'] = []
                    zone_cb.set("")
                    trainer_cb.set("")
                    return

                branch_id = int(branch_cb.get().split(" - ")[0])

                # Fetch zones for selected branch
                self.db.cursor.execute("""
                    SELECT id, name FROM zones WHERE branch_id=?
                """, (branch_id,))
                filtered_zones = self.db.cursor.fetchall()

                zone_cb['values'] = [f"{z['id']} - {z['name']}" for z in filtered_zones]
                zone_cb.set("")
                trainer_cb['values'] = []   # Reset trainer when branch changes
                trainer_cb.set("")

            except Exception as e:
                print("Zone filter error:", e)

        def update_trainers(event=None):
            """Refresh trainers based on selected branch and zone"""
            try:
                if not branch_cb.get() or not zone_cb.get():
                    trainer_cb['values'] = []
                    trainer_cb.set("")
                    return

                branch_id = int(branch_cb.get().split(" - ")[0])
                zone_id = int(zone_cb.get().split(" - ")[0])

                # Fetch trainers in selected branch + zone
                self.db.cursor.execute("""
                    SELECT id, name FROM staff
                    WHERE role='Trainer' AND branch_id=? AND zone_id=?
                """, (branch_id, zone_id))
                filtered_trainers = self.db.cursor.fetchall()

                trainer_cb['values'] = [f"{t['id']} - {t['name']}" for t in filtered_trainers]
                trainer_cb.set("")

            except Exception as e:
                print("Trainer filter error:", e)
        
        # --------------------------------------------------
        # Static Class Type Dropdown
        # --------------------------------------------------
        ttk.Label(form, text="Class Type:").grid(row=1, column=0, padx=5, pady=5)

        class_types = [
            "Yoga",
            "Zumba",
            "HIIT",
            "CrossFit",
            "Strength Training",
            "Cardio",
            "Pilates",
            "Aerobics"
        ]

        class_type_cb = ttk.Combobox(form, values=class_types, state="readonly", width=25)
        class_type_cb.grid(row=1, column=1, padx=5, pady=5)
        class_type_cb.set("Yoga")   # Default




        # --------------------------------------------------
        # Branch Dropdown
        # --------------------------------------------------
        ttk.Label(form, text="Branch:").grid(row=1, column=0, padx=5, pady=5)
        branch_cb = ttk.Combobox(form, values=[f"{b['id']} - {b['name']}" for b in branches])
        branch_cb.grid(row=1, column=1, padx=5, pady=5)
        branch_cb.bind("<<ComboboxSelected>>", update_zones)

        # --------------------------------------------------
        # Zone Dropdown (updates after branch selection)
        # --------------------------------------------------
        ttk.Label(form, text="Zone:").grid(row=2, column=0, padx=5, pady=5)
        zone_cb = ttk.Combobox(form, values=[])
        zone_cb.grid(row=2, column=1, padx=5, pady=5)
        zone_cb.bind("<<ComboboxSelected>>", update_trainers)

        # --------------------------------------------------
        # Trainer Dropdown (updates after zone selection)
        # --------------------------------------------------
        ttk.Label(form, text="Trainer:").grid(row=3, column=0, padx=5, pady=5)
        trainer_cb = ttk.Combobox(form, values=[])
        trainer_cb.grid(row=3, column=1, padx=5, pady=5)


        ttk.Label(form, text="Date (YYYY-MM-DD):").grid(row=4, column=0, padx=5, pady=5)
        date_entry = ttk.Entry(form, width=25)
        date_entry.grid(row=4, column=1, padx=5, pady=5)

        ttk.Label(form, text="Time (HH:MM):").grid(row=5, column=0, padx=5, pady=5)
        time_entry = ttk.Entry(form, width=25)
        time_entry.grid(row=5, column=1, padx=5, pady=5)

        # --------------------------------------------------
        # Add Class Button
        # --------------------------------------------------
        def add_class():
            try:
                name = class_name.get()
                branch_id = int(branch_cb.get().split(" - ")[0])
                zone_id = int(zone_cb.get().split(" - ")[0])
                trainer_id = int(trainer_cb.get().split(" - ")[0])
                date = date_entry.get()
                time = time_entry.get()

                if not name or not date or not time:
                    messagebox.showerror("Error", "Fill all fields!")
                    return

                self.db.cursor.execute("""
                    INSERT INTO classes (name, branch_id, zone_id, trainer_id, date, time)
                    VALUES (?, ?, ?, ?, ?, ?)
                """, (name, branch_id, zone_id, trainer_id, date, time))

                self.db.conn.commit()
                messagebox.showinfo("Success", "Class added successfully!")
                self.show_classes()

            except Exception as e:
                messagebox.showerror("Error", f"Failed to add class\n{e}")

        add_button = ttk.Button(form, text="Add Class", command=add_class)
        add_button.grid(row=6, column=0, columnspan=2, pady=10)

    def show_member_classes(self):
        self.clear_window()
        self.create_navbar()

        # Get current member data
        self.db.cursor.execute("SELECT id, branch_id, zone_id FROM members WHERE username=?", (self.current_user,))
        member = self.db.cursor.fetchone()
        member_id = member["id"]
        branch_id = member["branch_id"]
        zone_id = member["zone_id"]

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        title = ttk.Label(frame, text="Available Classes for You", font=("Segoe UI", 18, "bold"))
        title.pack(pady=10)

        # --------------------------------------------------
        # FETCH ONLY RELATED CLASSES
        # --------------------------------------------------
        self.db.cursor.execute("""
            SELECT classes.id, classes.name, classes.class_type, classes.date, classes.time,
                staff.name AS trainer_name,
                (SELECT COUNT(*) FROM class_enrollments WHERE class_id=classes.id AND member_id=?) AS is_enrolled
            FROM classes
            JOIN staff ON classes.trainer_id = staff.id
            WHERE classes.branch_id = ? AND classes.zone_id = ? AND classes.status='active'
            ORDER BY classes.date, classes.time
        """, (member_id, branch_id, zone_id))

        classes = self.db.cursor.fetchall()

        columns = ("ID", "Class", "Type", "Trainer", "Date", "Time", "Status")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=12)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=130)

        for c in classes:
            status = "Enrolled" if c["is_enrolled"] > 0 else "Available"
            table.insert("", "end", values=(
                c["id"], c["name"], c["class_type"], c["trainer_name"],
                c["date"], c["time"], status
            ))

        table.pack(pady=10, fill="x")

        # --------------------------------------------------
        # ENROLL / UNENROLL BUTTONS
        # --------------------------------------------------
        def enroll():
            selected = table.selection()
            if not selected:
                messagebox.showerror("Error", "Please select a class.")
                return

            values = table.item(selected[0], "values")
            class_id = values[0]

            # Prevent double-enroll
            self.db.cursor.execute("""
                SELECT 1 FROM class_enrollments WHERE member_id=? AND class_id=?
            """, (member_id, class_id))
            exist = self.db.cursor.fetchone()

            if exist:
                messagebox.showerror("Error", "You already enrolled in this class!")
                return

            self.db.cursor.execute("""
                INSERT INTO class_enrollments (member_id, class_id)
                VALUES (?, ?)
            """, (member_id, class_id))

            self.db.conn.commit()
            messagebox.showinfo("Success", "You have joined the class!")
            self.show_member_classes()

        def unenroll():
            selected = table.selection()
            if not selected:
                messagebox.showerror("Error", "Please select a class.")
                return

            values = table.item(selected[0], "values")
            class_id = values[0]

            # Check if enrolled
            self.db.cursor.execute("""
                SELECT 1 FROM class_enrollments WHERE member_id=? AND class_id=?
            """, (member_id, class_id))
            is_enrolled = self.db.cursor.fetchone()

            if not is_enrolled:
                messagebox.showerror("Error", "You are not enrolled in this class!")
                return

            self.db.cursor.execute("""
                DELETE FROM class_enrollments WHERE member_id=? AND class_id=?
            """, (member_id, class_id))

            self.db.conn.commit()
            messagebox.showinfo("Success", "You have left the class.")
            self.show_member_classes()

        button_frame = ttk.Frame(frame)
        button_frame.pack(pady=10)

        ttk.Button(button_frame, text="Join Class", width=20, command=enroll).grid(row=0, column=0, padx=10)
        ttk.Button(button_frame, text="Unjoin Class", width=20, command=unenroll).grid(row=0, column=1, padx=10)

    def show_appointments(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root)
        frame.pack(fill="both", expand=True, padx=20, pady=20)

        ttk.Label(frame, text="Book an Appointment", font=('Segoe UI', 18, 'bold')).pack(pady=15)

        add_frame = ttk.Frame(frame)
        add_frame.pack(fill="x", pady=10)

        # ---------------------------
        # Fetch member's branch_id & zone_id dynamically from users table
        # ---------------------------
        self.db.cursor.execute("SELECT id, branch_id, zone_id FROM members WHERE username=?", (self.current_user,))
        member = self.db.cursor.fetchone()
        if not member:
            messagebox.showerror("Error", "Member not found!")
            return

        member_id = member['id']
        branch_id = member['branch_id']
        zone_id = member['zone_id']

        # Fetch branch and zone names
        self.db.cursor.execute("SELECT name FROM branches WHERE id=?", (branch_id,))
        branch_name = self.db.cursor.fetchone()['name']

        self.db.cursor.execute("SELECT name FROM zones WHERE id=?", (zone_id,))
        zone_name = self.db.cursor.fetchone()['name']

        ttk.Label(add_frame, text=f"Branch: {branch_name}", font=("Segoe UI", 12)).grid(row=0, column=0, sticky="w", pady=5)
        ttk.Label(add_frame, text=f"Zone: {zone_name}", font=("Segoe UI", 12)).grid(row=1, column=0, sticky="w", pady=5)

        # ---------------------------
        # Trainer Dropdown: only trainers in this branch + zone
        # ---------------------------
        ttk.Label(add_frame, text="Select Trainer:").grid(row=2, column=0, padx=5, pady=5, sticky="w")
        trainer_var = tk.StringVar()
        trainer_dropdown = ttk.Combobox(add_frame, textvariable=trainer_var, state="readonly", width=40)
        trainer_dropdown.grid(row=2, column=1, padx=5, pady=5)

        trainers = self.db.cursor.execute("""
            SELECT id, name FROM staff
            WHERE role='Trainer' AND branch_id=? AND zone_id=?
        """, (branch_id, zone_id)).fetchall()

        trainer_dropdown['values'] = [f"{t['id']} - {t['name']}" for t in trainers]

        # ---------------------------
        # Date & Time
        # ---------------------------
        ttk.Label(add_frame, text="Appointment Date (YYYY-MM-DD):").grid(row=3, column=0, sticky="w", pady=5)
        date_entry = ttk.Entry(add_frame, width=30)
        date_entry.grid(row=3, column=1, pady=5)

        ttk.Label(add_frame, text="Appointment Time (HH:MM):").grid(row=4, column=0, sticky="w", pady=5)
        time_entry = ttk.Entry(add_frame, width=30)
        time_entry.grid(row=4, column=1, pady=5)

        # ---------------------------
        # Book Appointment
        # ---------------------------
        def book_appointment():
            trainer_sel = trainer_var.get()
            app_date = date_entry.get().strip()
            app_time = time_entry.get().strip()

            if not all([trainer_sel, app_date, app_time]):
                messagebox.showwarning("Validation Error", "Please fill all fields!")
                return

            trainer_id = int(trainer_sel.split(" - ")[0])

            # Check trainer availability
            self.db.cursor.execute("""
                SELECT * FROM appointments
                WHERE trainer_id=? AND date=? AND time=?
            """, (trainer_id, app_date, app_time))
            conflict = self.db.cursor.fetchone()
            if conflict:
                messagebox.showerror("Unavailable", "Trainer is not available at this time!")
                return

            # Insert appointment
            self.db.cursor.execute("""
                INSERT INTO appointments (member_id, branch_id, zone_id, trainer_id, date, time)
                VALUES (?, ?, ?, ?, ?, ?)
            """, (member_id, branch_id, zone_id, trainer_id, app_date, app_time))
            self.db.conn.commit()
            messagebox.showinfo("Success", "Appointment booked successfully!")
            self.show_appointments()

        ttk.Button(frame, text="Book Appointment", command=book_appointment).pack(pady=20)
        
    def show_trainer_appointments(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        ttk.Label(frame, text="My Appointments", font=("Segoe UI", 18, "bold")).pack(pady=10)

        trainer_id = self.staff_id  # logged-in trainer

        # Fetch appointments assigned to this trainer
        self.db.cursor.execute("""
            SELECT appointments.id, members.name AS member_name,
                appointments.date, appointments.time,
                branches.name AS branch_name,
                zones.name AS zone_name,
                appointments.status
            FROM appointments
            JOIN members ON appointments.member_id = members.id
            JOIN branches ON appointments.branch_id = branches.id
            JOIN zones ON appointments.zone_id = zones.id
            WHERE appointments.trainer_id = ?
            ORDER BY appointments.date, appointments.time
        """, (trainer_id,))

        appointments = self.db.cursor.fetchall()

        columns = ("ID", "Member", "Date", "Time", "Branch", "Zone", "Status")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=12)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=160)

        for a in appointments:
            table.insert("", "end", values=(
                a["id"], a["member_name"], a["date"], a["time"],
                a["branch_name"], a["zone_name"], a["status"]
            ))

        table.pack(fill="x", pady=10)

        # -----------------------------
        # Attendance Update Controls
        # -----------------------------
        def update_status(new_status):
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Select Appointment", "Please select an appointment!")
                return

            appointment_id = table.item(selected[0])["values"][0]

            self.db.cursor.execute("""
                UPDATE appointments
                SET status = ?
                WHERE id = ?
            """, (new_status, appointment_id))

            self.db.conn.commit()
            messagebox.showinfo("Success", f"Status updated to {new_status}")
            self.show_trainer_appointments()  # refresh

        button_frame = ttk.Frame(frame)
        button_frame.pack(pady=20)

        ttk.Button(button_frame, text="Mark Completed",
                command=lambda: update_status("Completed")).grid(row=0, column=0, padx=10)

        ttk.Button(button_frame, text="Mark Missed",
                command=lambda: update_status("Missed")).grid(row=0, column=1, padx=10)

    def show_trainer_classes(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        ttk.Label(frame, text="My Assigned Classes", font=("Segoe UI", 18, "bold")).pack(pady=10)

        trainer_id = self.staff_id  # logged-in trainer id

        self.db.cursor.execute("""
            SELECT classes.id, classes.name, branches.name AS branch_name,
                zones.name AS zone_name, classes.date, classes.time
            FROM classes
            JOIN branches ON classes.branch_id = branches.id
            JOIN zones ON classes.zone_id = zones.id
            WHERE classes.trainer_id = ?
            ORDER BY classes.date, classes.time
        """, (trainer_id,))

        class_list = self.db.cursor.fetchall()

        columns = ("ID", "Name", "Branch", "Zone", "Date", "Time")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=12)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=150)

        for row in class_list:
            table.insert("", "end", values=(
                row["id"], row["name"], row["branch_name"],
                row["zone_name"], row["date"], row["time"]
            ))

        table.pack(pady=10, fill="x")

        def open_class():
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Select Class", "Please select a class.")
                return
            
            class_id = table.item(selected[0])["values"][0]
            self.show_class_members_for_trainer(class_id)

        ttk.Button(frame, text="View Enrolled Members", command=open_class).pack(pady=20)
    def show_class_members_for_trainer(self, class_id):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        ttk.Label(frame, text="Enrolled Members & Attendance", 
                font=("Segoe UI", 18, "bold")).pack(pady=10)

        self.db.cursor.execute("""
            SELECT members.id, members.name, members.phone, 
                class_enrollments.attendance
            FROM class_enrollments
            JOIN members ON class_enrollments.member_id = members.id
            WHERE class_enrollments.class_id = ?
        """, (class_id,))

        members = self.db.cursor.fetchall()

        columns = ("ID", "Name", "Phone", "Attendance")
        table = ttk.Treeview(frame, columns=columns, show="headings", height=12)

        for col in columns:
            table.heading(col, text=col)
            table.column(col, width=180)

        for m in members:
            table.insert("", "end", values=(m["id"], m["name"], m["phone"], m["attendance"]))

        table.pack(pady=10, fill="x")

        def mark_attendance(status):
            selected = table.selection()
            if not selected:
                messagebox.showwarning("Select Member", "Please select a member.")
                return

            member_id = table.item(selected[0])["values"][0]

            self.db.cursor.execute("""
                UPDATE class_enrollments
                SET attendance = ?
                WHERE member_id = ? AND class_id = ?
            """, (status, member_id, class_id))

            self.db.conn.commit()

            messagebox.showinfo("Success", f"Attendance marked as {status}")
            self.show_class_members_for_trainer(class_id)  # refresh UI

        button_frame = ttk.Frame(frame)
        button_frame.pack(pady=20)

        ttk.Button(button_frame, text="Mark Present",
                command=lambda: mark_attendance("Present")).grid(row=0, column=0, padx=10)

        ttk.Button(button_frame, text="Mark Absent",
                command=lambda: mark_attendance("Absent")).grid(row=0, column=1, padx=10)

    def show_manager_reports(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        title = ttk.Label(frame, text="Manager Reports & Analytics",
                        font=("Segoe UI", 20, "bold"))
        title.pack(pady=10)

        # ===============================================================
        # ================ MEMBERSHIP GROWTH =============================
        # ===============================================================
        ttk.Label(frame, text="📈 Membership Growth (Last 6 Months)",
                font=("Segoe UI", 14, "bold")).pack(anchor="w", pady=5)

        self.db.cursor.execute("""
            SELECT strftime('%Y-%m', join_date) AS month, COUNT(*) AS total
            FROM members
            GROUP BY month
            ORDER BY month DESC LIMIT 6
        """)
        membership_data = self.db.cursor.fetchall()

        mem_table = ttk.Treeview(frame, columns=("Month", "New Members"), show="headings", height=6)
        mem_table.heading("Month", text="Month")
        mem_table.heading("New Members", text="New Members")
        mem_table.pack(fill="x", pady=5)

        for row in membership_data:
            mem_table.insert("", "end", values=(row["month"], row["total"]))

        # ===============================================================
        # =================== REVENUE TRENDS ============================
        # ===============================================================
        ttk.Label(frame, text="💰 Revenue Trends (Last 6 Months)",
                font=("Segoe UI", 14, "bold")).pack(anchor="w", pady=10)

        self.db.cursor.execute("""
            SELECT strftime('%Y-%m', date) AS month, SUM(amount) AS revenue
            FROM payments
            GROUP BY month
            ORDER BY month DESC LIMIT 6
        """)
        revenue_data = self.db.cursor.fetchall()

        rev_table = ttk.Treeview(frame, columns=("Month", "Revenue"), show="headings", height=6)
        rev_table.heading("Month", text="Month")
        rev_table.heading("Revenue", text="Revenue (PKR)")
        rev_table.pack(fill="x", pady=5)

        for row in revenue_data:
            rev_table.insert("", "end", values=(row["month"], row["revenue"]))

        # ===============================================================
        # ================= TRAINER SCHEDULES ===========================
        # ===============================================================
        ttk.Label(frame, text="🧑‍🏫 Trainer Class Schedules",
                font=("Segoe UI", 14, "bold")).pack(anchor="w", pady=10)

        self.db.cursor.execute("""
            SELECT staff.name AS trainer_name, classes.name AS class_name,
                classes.date, classes.time
            FROM classes
            JOIN staff ON staff.id = classes.trainer_id
            ORDER BY classes.date, classes.time
        """)
        trainer_data = self.db.cursor.fetchall()

        trainer_table = ttk.Treeview(frame, columns=("Trainer", "Class", "Date", "Time"),
                                    show="headings", height=8)
        for col in ("Trainer", "Class", "Date", "Time"):
            trainer_table.heading(col, text=col)
        trainer_table.pack(fill="x", pady=5)

        for row in trainer_data:
            trainer_table.insert("", "end", values=(
                row["trainer_name"], row["class_name"], row["date"], row["time"]
            ))

        # ===============================================================
        # ================= EQUIPMENT MAINTENANCE =======================
        # ===============================================================
        ttk.Label(frame, text="🛠 Equipment Maintenance Status",
                font=("Segoe UI", 14, "bold")).pack(anchor="w", pady=10)

        self.db.cursor.execute("""
            SELECT name, last_service, next_service, status
            FROM equipment
            ORDER BY next_service
        """)
        eq_data = self.db.cursor.fetchall()

        eq_table = ttk.Treeview(frame, columns=("Name", "Last Service", "Next Service", "Status"),
                                show="headings", height=8)
        for col in ("Name", "Last Service", "Next Service", "Status"):
            eq_table.heading(col, text=col)
        eq_table.pack(fill="x", pady=5)

        for row in eq_data:
            eq_table.insert("", "end", values=(
                row["name"], row["last_service"], row["next_service"], row["status"]
            ))




    def show_reports(self):
        self.clear_window()
        self.create_navbar()

        frame = ttk.Frame(self.root, padding=20)
        frame.pack(fill="both", expand=True)

        ttk.Label(frame, text="Gym Reports & Analytics", font=("Segoe UI", 18, "bold")).pack(pady=10)

        # ------------------ Fetch Data ------------------
        # Members per branch
        self.db.cursor.execute("""
            SELECT b.name AS branch, COUNT(m.id) AS members_count
            FROM members m
            LEFT JOIN branches b ON m.branch_id = b.id
            GROUP BY b.name
        """)
        branch_data = self.db.cursor.fetchall()

        # Membership type distribution
        self.db.cursor.execute("""
            SELECT membership_type, COUNT(*) AS count
            FROM members
            GROUP BY membership_type
        """)
        membership_data = self.db.cursor.fetchall()

        # Class popularity (number of enrolled members per class)
        self.db.cursor.execute("""
            SELECT c.name AS class_name, COUNT(ce.id) AS enrollments
            FROM classes c
            LEFT JOIN class_enrollments ce ON c.id = ce.class_id
            GROUP BY c.name
        """)
        class_data = self.db.cursor.fetchall()

        # Revenue trends (sum of payments per month)
        self.db.cursor.execute("""
            SELECT strftime('%Y-%m', date) AS month, SUM(amount) AS revenue
            FROM payments
            GROUP BY month
            ORDER BY month
        """)
        revenue_data = self.db.cursor.fetchall()

        # ------------------ Convert to DataFrames ------------------
        df_branch = pd.DataFrame(branch_data, columns=['Branch', 'Members'])
        df_membership = pd.DataFrame(membership_data, columns=['Membership Type', 'Count'])
        df_class = pd.DataFrame(class_data, columns=['Class', 'Enrollments'])
        df_revenue = pd.DataFrame(revenue_data, columns=['Month', 'Revenue'])

        # ------------------ Plotting ------------------
        fig, axs = plt.subplots(2, 2, figsize=(10, 8))
        fig.tight_layout(pad=4)

        # Members per branch
        axs[0,0].bar(df_branch['Branch'], df_branch['Members'], color='skyblue')
        axs[0,0].set_title("Members per Branch")
        axs[0,0].set_xticklabels(df_branch['Branch'], rotation=45, ha='right')

        # Membership type distribution
        axs[0,1].pie(df_membership['Count'], labels=df_membership['Membership Type'], autopct='%1.1f%%', startangle=140)
        axs[0,1].set_title("Membership Type Distribution")

        # Class popularity
        axs[1,0].barh(df_class['Class'], df_class['Enrollments'], color='lightgreen')
        axs[1,0].set_title("Class Popularity (Enrollments)")

        # Revenue trends
        axs[1,1].plot(df_revenue['Month'], df_revenue['Revenue'], marker='o', linestyle='-', color='orange')
        axs[1,1].set_title("Revenue Trends")
        axs[1,1].set_xticklabels(df_revenue['Month'], rotation=45, ha='right')

        # ------------------ Embed in Tkinter ------------------
        canvas = FigureCanvasTkAgg(fig, master=frame)
        canvas.draw()
        canvas.get_tk_widget().pack(fill="both", expand=True)


# ----------------------------------------
# Run App
# ----------------------------------------
if __name__ == "__main__":
    root = tk.Tk()
    app = ProfessionalGymManagementSystem(root)
    root.mainloop()
