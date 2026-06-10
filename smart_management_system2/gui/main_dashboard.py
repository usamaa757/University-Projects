import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import matplotlib.pyplot as plt
from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
import hashlib
import database

# Import all GUI modules
from gui.member_management import MemberManagement
from gui.appointment_management import AppointmentManagement
from gui.payment_management import PaymentManagement
from gui.staff_dashboard import StaffDashboard
from gui.reports_window import ReportsWindow
from gui.branch_zone_management import BranchZoneManagement
from gui.class_management import ClassManagement

class MainDashboard:
    def __init__(self, root, user_data, on_logout=None):
        self.root = root
        self.root.title("M9 Fitness - Dashboard")
        self.root.state('zoomed')
        self.root.configure(bg='#ecf0f1')

        self.on_logout = on_logout
        self.db = database.Database()

        self.user_data = user_data
        self.user_role = user_data["role"]

        # Initialize frames dictionary
        self.frames = {}

        self.setup_ui()
        self.load_statistics()
    
    def setup_ui(self):
        # Create main container
        main_container = tk.Frame(self.root)
        main_container.pack(fill='both', expand=True)
        
        # Sidebar
        self.sidebar = tk.Frame(main_container, width=250, bg='#2c3e50')
        self.sidebar.pack(side='left', fill='y')
        self.sidebar.pack_propagate(False)
        
        # Main content area
        self.main_content = tk.Frame(main_container, bg='#ecf0f1')
        self.main_content.pack(side='left', fill='both', expand=True)
        
        self.create_sidebar()
        self.create_header()
        
        # Default dashboard view
        self.show_dashboard()
    
    def create_header(self):
        header = tk.Frame(self.main_content, bg='#3498db', height=60)
        header.pack(fill='x')
        header.pack_propagate(False)
        
        # Welcome message
        welcome_label = tk.Label(header, text=f"Welcome, {self.user_role}",
                                font=('Arial', 16, 'bold'), bg='#3498db', fg='white')
        welcome_label.pack(side='left', padx=20)

    def create_sidebar(self):
        # Logo/Title
        logo_frame = tk.Frame(self.sidebar, bg='#34495e', height=100)
        logo_frame.pack(fill='x')
        logo_frame.pack_propagate(False)
        
        tk.Label(logo_frame, text="M9 FITNESS", font=('Arial', 18, 'bold'),
                bg='#34495e', fg='white').pack(expand=True)
        
        tk.Label(logo_frame, text=f"({self.user_data['first_name']})", font=('Arial', 16),
                bg='#34495e', fg='white').pack(expand=True)
        
        # Navigation buttons
        nav_frame = tk.Frame(self.sidebar, bg='#2c3e50')
        nav_frame.pack(fill='both', expand=True, padx=10, pady=20)
        
        # Define all possible buttons with their commands
        all_buttons = [
            ("🏠 Dashboard", self.show_dashboard),
            ("👥 Members", self.show_member_management),
            ("📅 Appointments", self.show_appointment_management),
            ("💳 Payments", self.show_payment_management),
            ("📊 Reports", self.show_reports),
            ("👨‍💼 Staff", self.show_staff_dashboard),
            ("🏢 Branches & Zones", self.show_branch_zone_management),
            ("🏋️ Classes", self.show_class_management),
            ("⚙️ My Profile", self.show_member_profile),
            ("📝 My Classes", self.show_my_classes),
            ("📅 My Schedule", self.show_my_schedule),
            ("💳 My Payments", self.show_my_payments),
            ("🚪 Logout", self.logout),
        ]
        
        # Define role-based button visibility
        role_buttons = {
            "member": [
                "🏠 Dashboard", 
                "⚙️ My Profile", 
                "📝 My Classes", 
                "📅 My Schedule", 
                "💳 My Payments", 
                "🚪 Logout"
            ],
            "Trainer": [
                "🏠 Dashboard", 
                "👥 Members", 
                "📅 Appointments", 
                "🏋️ Classes", 
                "🚪 Logout"
            ],
            "Manager": [
                "🏠 Dashboard", 
                "👥 Members", 
                "📅 Appointments", 
                "💳 Payments", 
                "📊 Reports", 
                "👨‍💼 Staff", 
                "🏢 Branches & Zones", 
                "🏋️ Classes", 
                "🚪 Logout"
            ],
            "Admin": [
                "🏠 Dashboard", 
                "👥 Members", 
                "📅 Appointments", 
                "💳 Payments", 
                "📊 Reports", 
                "👨‍💼 Staff", 
                "🏢 Branches & Zones", 
                "🏋️ Classes", 
                "🚪 Logout"
            ],
         
            "Attendant": [
                "🏠 Dashboard", 
                "👥 Members", 
                "🚪 Logout"
            ]
        }
        
        # Get allowed buttons for this role
        allowed_button_texts = role_buttons.get(self.user_role, role_buttons.get(self.user_role.lower(), []))
        
        # Filter buttons based on role
        buttons_to_show = []
        for text, command in all_buttons:
            if text in allowed_button_texts:
                buttons_to_show.append((text, command))
        
        # Create buttons
        for text, command in buttons_to_show:
            btn = tk.Button(nav_frame, text=text, font=('Arial', 11),
                           bg='#34495e', fg='white', anchor='w',
                           padx=20, pady=12, cursor='hand2',
                           command=command)
            btn.pack(fill='x', pady=2)
            btn.bind("<Enter>", lambda e, b=btn: b.config(bg='#3498db'))
            btn.bind("<Leave>", lambda e, b=btn: b.config(bg='#34495e'))
        
        # Status bar
        status_frame = tk.Frame(self.sidebar, bg='#34495e', height=40)
        status_frame.pack(fill='x', side='bottom')
        status_frame.pack_propagate(False)
        
        # Get member status if logged in as member
        if self.user_role.lower() == "member":
            conn = self.db.get_connection()
            cursor = conn.cursor()
            cursor.execute("SELECT membership_type, expiry_date FROM members WHERE member_id = ?", 
                          (self.user_data["id"],))
            member_info = cursor.fetchone()
            conn.close()
            
            if member_info:
                membership, expiry = member_info
                days_left = (datetime.strptime(expiry, '%Y-%m-%d') - datetime.now()).days
                status_text = f"{membership} | {days_left} days left"
            else:
                status_text = "Member"
        else:
            conn = self.db.get_connection()
            cursor = conn.cursor()
            cursor.execute("SELECT COUNT(*) FROM members WHERE status='Active'")
            active_members = cursor.fetchone()[0]
            conn.close()
            status_text = f"Active Members: {active_members}"
        
        tk.Label(status_frame, text=status_text,
                font=('Arial', 9), bg='#34495e', fg='#bdc3c7').pack(expand=True)

    def clear_main_content(self):
        for widget in self.main_content.winfo_children():
            if widget != self.main_content.winfo_children()[0]:  # Keep header
                widget.destroy()
                
    def show_dashboard(self):
        self.clear_main_content()

        if self.user_role.lower() == "member":
            self.show_member_dashboard()
        elif self.user_role in ("Manager", "Admin"):
            self.show_admin_dashboard()
        elif self.user_role == "Trainer":
            self.show_trainer_dashboard()
    
    # ==================== MEMBER DASHBOARD ====================
    def show_member_dashboard(self):
        member_id = self.user_data["id"]
        container = tk.Frame(self.main_content, bg='#ecf0f1')
        container.pack(fill='both', expand=True, padx=20, pady=20)

        # Welcome Header
        header_frame = tk.Frame(container, bg='#ecf0f1')
        header_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(header_frame, text=f"Welcome back, {self.user_data['first_name']}!",
                font=('Arial', 24, 'bold'), bg='#ecf0f1', fg='#2c3e50').pack(side='left')
        
        # Get member data
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get member details
        cursor.execute("""
            SELECT m.*, b.branch_name, z.zone_name 
            FROM members m
            LEFT JOIN gym_branches b ON m.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON m.zone_id = z.zone_id
            WHERE m.member_id = ?
        """, (member_id,))
        member = cursor.fetchone()
        
        if member:
            # Get column names
            cursor.execute("PRAGMA table_info(members)")
            columns = [col[1] for col in cursor.fetchall()]
            member_dict = dict(zip(columns, member[:len(columns)]))
            
            # Calculate days until expiry
            expiry_date = datetime.strptime(member_dict['expiry_date'], '%Y-%m-%d')
            days_left = (expiry_date - datetime.now()).days
            status_color = '#2ecc71' if days_left > 7 else '#f39c12' if days_left > 0 else '#e74c3c'
            
            # Stats cards
            stats_frame = tk.Frame(container, bg='#ecf0f1')
            stats_frame.pack(fill='x', pady=10)
            
            # Membership Card
            membership_card = tk.Frame(stats_frame, bg=status_color, relief='raised', bd=1)
            membership_card.grid(row=0, column=0, padx=10, pady=10, sticky='nsew')
            
            tk.Label(membership_card, text="Membership Status", font=('Arial', 12),
                    bg=status_color, fg='white').pack(pady=(15, 5))
            tk.Label(membership_card, text=member_dict['membership_type'], 
                    font=('Arial', 20, 'bold'), bg=status_color, fg='white').pack()
            tk.Label(membership_card, text=f"{days_left} days remaining", 
                    font=('Arial', 11), bg=status_color, fg='white').pack(pady=(5, 15))
            
            # Branch Card
            branch_card = tk.Frame(stats_frame, bg='#3498db', relief='raised', bd=1)
            branch_card.grid(row=0, column=1, padx=10, pady=10, sticky='nsew')
            
            tk.Label(branch_card, text="Your Branch", font=('Arial', 12),
                    bg='#3498db', fg='white').pack(pady=(15, 5))
            tk.Label(branch_card, text=member_dict.get('branch_name', 'N/A'), 
                    font=('Arial', 16, 'bold'), bg='#3498db', fg='white').pack()
            tk.Label(branch_card, text=f"Zone: {member_dict.get('zone_name', 'N/A')}", 
                    font=('Arial', 11), bg='#3498db', fg='white').pack(pady=(5, 15))
            
            # Quick Stats Card
            stats_card = tk.Frame(stats_frame, bg='#9b59b6', relief='raised', bd=1)
            stats_card.grid(row=0, column=2, padx=10, pady=10, sticky='nsew')
            
            # Get attendance count
            cursor.execute("""
                SELECT COUNT(*) FROM attendance 
                WHERE member_id = ? AND DATE(check_in) = DATE('now')
            """, (member_id,))
            today_visit = cursor.fetchone()[0]
            
            # Get upcoming appointments
            cursor.execute("""
                SELECT COUNT(*) FROM appointments 
                WHERE member_id = ? AND appointment_date >= DATE('now') 
                AND status = 'Scheduled'
            """, (member_id,))
            upcoming = cursor.fetchone()[0]
            
            tk.Label(stats_card, text="Quick Stats", font=('Arial', 12),
                    bg='#9b59b6', fg='white').pack(pady=(15, 5))
            tk.Label(stats_card, text=f"📅 Today: {'Visited' if today_visit else 'Not Visited'}", 
                    font=('Arial', 11), bg='#9b59b6', fg='white').pack()
            tk.Label(stats_card, text=f"📋 Upcoming: {upcoming} sessions", 
                    font=('Arial', 11), bg='#9b59b6', fg='white').pack(pady=5)
            
            stats_frame.grid_columnconfigure(0, weight=1)
            stats_frame.grid_columnconfigure(1, weight=1)
            stats_frame.grid_columnconfigure(2, weight=1)
            
            # Upcoming Classes Section
            classes_frame = tk.LabelFrame(container, text="📝 My Upcoming Classes", 
                                         font=('Arial', 14, 'bold'), bg='white', padx=15, pady=15)
            classes_frame.pack(fill='both', expand=True, pady=20)
            
            # Get member's registered classes
            cursor.execute("""
                SELECT c.class_id, c.class_name, c.class_type, c.class_date, 
                       c.class_time, c.duration_minutes, c.zone_id, z.zone_name,
                       s.first_name || ' ' || s.last_name as trainer_name
                FROM class_registrations cr
                JOIN classes c ON cr.class_id = c.class_id
                LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
                LEFT JOIN staff s ON c.trainer_id = s.staff_id
                WHERE cr.member_id = ? AND c.class_date >= DATE('now')
                ORDER BY c.class_date, c.class_time
                LIMIT 5
            """, (member_id,))
            
            upcoming_classes = cursor.fetchall()
            
            if upcoming_classes:
                # Create treeview for classes
                columns = ('Class Name', 'Type', 'Date', 'Time', 'Duration', 'Trainer', 'Zone')
                tree = ttk.Treeview(classes_frame, columns=columns, show='headings', height=5)
                
                for col in columns:
                    tree.heading(col, text=col)
                    tree.column(col, width=100)
                
                for cls in upcoming_classes:
                    tree.insert('', 'end', values=(
                        cls[1], cls[2], cls[3], cls[4], f"{cls[5]} min", cls[8], cls[7]
                    ))
                
                tree.pack(fill='both', expand=True)
                
                # Button to view all classes
                btn_frame = tk.Frame(classes_frame, bg='white')
                btn_frame.pack(fill='x', pady=10)
                
                tk.Button(btn_frame, text="View All My Classes", 
                         command=self.show_my_classes,
                         bg='#3498db', fg='white', padx=20, pady=5).pack(side='right')
            else:
                tk.Label(classes_frame, text="No upcoming classes registered", 
                        font=('Arial', 12), bg='white', fg='#7f8c8d').pack(pady=20)
                
             
            # Recent Activity Section
            activity_frame = tk.LabelFrame(container, text="📊 Recent Activity", 
                                          font=('Arial', 14, 'bold'), bg='white', padx=15, pady=15)
            activity_frame.pack(fill='x', pady=10)
            
            # Get recent check-ins
            cursor.execute("""
                SELECT DATE(check_in) as date, TIME(check_in) as time_in, 
                       TIME(check_out) as time_out, duration_minutes, z.zone_name
                FROM attendance a
                LEFT JOIN workout_zones z ON a.zone_id = z.zone_id
                WHERE a.member_id = ?
                ORDER BY a.check_in DESC
                LIMIT 5
            """, (member_id,))
            
            recent_activity = cursor.fetchall()
            
            if recent_activity:
                for activity in recent_activity:
                    activity_item = tk.Frame(activity_frame, bg='white', relief='solid', bd=1)
                    activity_item.pack(fill='x', pady=2)
                    
                    date_str = f"📅 {activity[0]} | 🕐 {activity[1]} - {activity[2] if activity[2] else 'Present'}"
                    zone_str = f"📍 {activity[4] if activity[4] else 'Main Gym'}"
                    duration_str = f"⏱️ {activity[3]} min" if activity[3] else "⏱️ In progress"
                    
                    tk.Label(activity_item, text=date_str, bg='white', 
                            font=('Arial', 10)).pack(anchor='w', padx=10, pady=2)
                    tk.Label(activity_item, text=f"{zone_str} | {duration_str}", 
                            bg='white', font=('Arial', 10)).pack(anchor='w', padx=10, pady=2)
            else:
                tk.Label(activity_frame, text="No recent activity", 
                        font=('Arial', 12), bg='white', fg='#7f8c8d').pack(pady=10)
        
        conn.close()
    
    def show_member_profile(self):
        """Show member profile for editing"""
        self.clear_main_content()
        
        # Create container for profile
        container = tk.Frame(self.main_content, bg='#ecf0f1', padx=30, pady=30)
        container.pack(fill='both', expand=True)
        
        # Title
        tk.Label(container, text="My Profile", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        # Get member data
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT m.*, b.branch_name, z.zone_name 
            FROM members m
            LEFT JOIN gym_branches b ON m.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON m.zone_id = z.zone_id
            WHERE m.member_id = ?
        """, (self.user_data["id"],))
        
        member = cursor.fetchone()
        
        if not member:
            conn.close()
            messagebox.showerror("Error", "Member not found")
            return
        
        # Get column names
        cursor.execute("PRAGMA table_info(members)")
        columns = [col[1] for col in cursor.fetchall()]
        member_dict = dict(zip(columns, member[:len(columns)]))
        
        # Create scrollable frame
        canvas = tk.Canvas(container, bg='#ecf0f1', highlightthickness=0)
        scrollbar = ttk.Scrollbar(container, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='#ecf0f1')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        # Create notebook for tabs
        notebook = ttk.Notebook(scrollable_frame)
        notebook.pack(fill='both', expand=True, pady=10)
        
        # Tab 1: Personal Information
        personal_frame = tk.Frame(notebook, bg='white', padx=20, pady=20)
        notebook.add(personal_frame, text="Personal Information")
        
        # Personal info fields (editable)
        personal_fields = [
            ("First Name:", "first_name"),
            ("Last Name:", "last_name"),
            ("Email:", "email"),
            ("Phone:", "phone"),
            ("Date of Birth:", "date_of_birth"),
            ("Gender:", "gender"),
            ("Address:", "address"),
            ("City:", "city"),
            ("Emergency Contact:", "emergency_contact"),
            ("Medical Conditions:", "medical_conditions"),
            ("Fitness Goals:", "fitness_goals"),
        ]
        
        self.profile_vars = {}
        
        for i, (label, field) in enumerate(personal_fields):
            tk.Label(personal_frame, text=label, font=('Arial', 11),
                    bg='white', anchor='w').grid(row=i, column=0, sticky='w', pady=8, padx=(0, 20))
            
            var = tk.StringVar()
            var.set(member_dict.get(field, ''))
            entry = ttk.Entry(personal_frame, textvariable=var, width=40, state='readonly')
            entry.grid(row=i, column=1, pady=8, sticky='w')
            
            self.profile_vars[field] = var
        
        # Tab 2: Membership Details
        membership_frame = tk.Frame(notebook, bg='white', padx=20, pady=20)
        notebook.add(membership_frame, text="Membership Details")
        
        membership_fields = [
            ("Membership Type:", "membership_type"),
            ("Subscription Plan:", "subscription_plan"),
            ("Join Date:", "join_date"),
            ("Expiry Date:", "expiry_date"),
            ("Status:", "status"),
            ("Branch:", "branch_name"),
            ("Zone:", "zone_name"),
        ]
        
        for i, (label, field) in enumerate(membership_fields):
            tk.Label(membership_frame, text=label, font=('Arial', 11),
                    bg='white', anchor='w').grid(row=i, column=0, sticky='w', pady=8, padx=(0, 20))
            
            var = tk.StringVar()
            
            if field == 'branch_name':
                value = member[17] if len(member) > 17 else 'N/A'
            elif field == 'zone_name':
                value = member[18] if len(member) > 18 else 'N/A'
            else:
                value = member_dict.get(field, 'N/A')
            
            var.set(value)
            entry = ttk.Entry(membership_frame, textvariable=var, width=40, state='readonly')
            entry.grid(row=i, column=1, pady=8, sticky='w')
        
        # Tab 3: Change Password
        password_frame = tk.Frame(notebook, bg='white', padx=20, pady=20)
        notebook.add(password_frame, text="Change Password")
        
        tk.Label(password_frame, text="Current Password:", font=('Arial', 11),
                bg='white').grid(row=0, column=0, sticky='w', pady=10, padx=(0, 20))
        current_pwd = ttk.Entry(password_frame, width=40, show="*")
        current_pwd.grid(row=0, column=1, pady=10)
        
        tk.Label(password_frame, text="New Password:", font=('Arial', 11),
                bg='white').grid(row=1, column=0, sticky='w', pady=10, padx=(0, 20))
        new_pwd = ttk.Entry(password_frame, width=40, show="*")
        new_pwd.grid(row=1, column=1, pady=10)
        
        tk.Label(password_frame, text="Confirm New Password:", font=('Arial', 11),
                bg='white').grid(row=2, column=0, sticky='w', pady=10, padx=(0, 20))
        confirm_pwd = ttk.Entry(password_frame, width=40, show="*")
        confirm_pwd.grid(row=2, column=1, pady=10)
        
        def change_password():
            current = current_pwd.get().strip()
            new = new_pwd.get().strip()
            confirm = confirm_pwd.get().strip()
            
            if not current or not new or not confirm:
                messagebox.showerror("Error", "All fields are required")
                return
            
            if new != confirm:
                messagebox.showerror("Error", "New passwords do not match")
                return
            
            # Verify current password
            cursor.execute("SELECT password_hash FROM members WHERE member_id = ?", 
                          (self.user_data["id"],))
            stored_hash = cursor.fetchone()[0]
            
            # Hash current password to compare
            current_hash = hashlib.sha256(current.encode()).hexdigest()
            
            if current_hash != stored_hash:
                messagebox.showerror("Error", "Current password is incorrect")
                return
            
            # Update password
            new_hash = hashlib.sha256(new.encode()).hexdigest()
            cursor.execute("UPDATE members SET password_hash = ? WHERE member_id = ?",
                          (new_hash, self.user_data["id"]))
            conn.commit()
            
            messagebox.showinfo("Success", "Password changed successfully")
            current_pwd.delete(0, tk.END)
            new_pwd.delete(0, tk.END)
            confirm_pwd.delete(0, tk.END)
        
        tk.Button(password_frame, text="Change Password", 
                 command=change_password,
                 bg='#3498db', fg='white', font=('Arial', 11, 'bold'),
                 padx=30, pady=10).grid(row=3, column=0, columnspan=2, pady=20)
        
        # Buttons frame
        button_frame = tk.Frame(scrollable_frame, bg='#ecf0f1')
        button_frame.pack(fill='x', pady=20)
        
        def enable_edit():
            # Enable personal info fields for editing
            for widget in personal_frame.winfo_children():
                if isinstance(widget, ttk.Entry):
                    widget.config(state='normal')
            
            edit_btn.pack_forget()
            save_btn.pack(side='left', padx=5)
            cancel_btn.pack(side='left', padx=5)
        
        def save_changes():
            # Collect updated data
            updated_data = {}
            for field_name, var in self.profile_vars.items():
                updated_data[field_name] = var.get().strip()
            
            # Update database
            try:
                set_clause = ', '.join([f"{key} = ?" for key in updated_data.keys()])
                values = list(updated_data.values()) + [self.user_data["id"]]
                
                cursor.execute(f"UPDATE members SET {set_clause} WHERE member_id = ?", values)
                conn.commit()
                
                messagebox.showinfo("Success", "Profile updated successfully")
                
                # Disable fields again
                for widget in personal_frame.winfo_children():
                    if isinstance(widget, ttk.Entry):
                        widget.config(state='readonly')
                
                save_btn.pack_forget()
                cancel_btn.pack_forget()
                edit_btn.pack(side='left', padx=5)
                
            except Exception as e:
                conn.rollback()
                messagebox.showerror("Error", f"Failed to update profile: {str(e)}")
        
        def cancel_edit():
            # Reset values
            for field_name, var in self.profile_vars.items():
                var.set(member_dict.get(field_name, ''))
            
            # Disable fields
            for widget in personal_frame.winfo_children():
                if isinstance(widget, ttk.Entry):
                    widget.config(state='readonly')
            
            save_btn.pack_forget()
            cancel_btn.pack_forget()
            edit_btn.pack(side='left', padx=5)
        
        edit_btn = tk.Button(button_frame, text="Edit Profile", 
                            command=enable_edit,
                            bg='#3498db', fg='white', font=('Arial', 11),
                            padx=20, pady=5)
        edit_btn.pack(side='left', padx=5)
        
        save_btn = tk.Button(button_frame, text="Save Changes", 
                            command=save_changes,
                            bg='#2ecc71', fg='white', font=('Arial', 11),
                            padx=20, pady=5)
        
        cancel_btn = tk.Button(button_frame, text="Cancel", 
                              command=cancel_edit,
                              bg='#e74c3c', fg='white', font=('Arial', 11),
                              padx=20, pady=5)
        
        # Back button
        tk.Button(button_frame, text="Back to Dashboard", 
                 command=self.show_member_dashboard,
                 bg='#95a5a6', fg='white', font=('Arial', 11),
                 padx=20, pady=5).pack(side='right', padx=5)
        
        conn.close()
    
    def show_my_classes(self):
        """Show classes the member is registered for"""
        self.clear_main_content()
        
        container = tk.Frame(self.main_content, bg='#ecf0f1', padx=20, pady=20)
        container.pack(fill='both', expand=True)
        
        tk.Label(container, text="My Registered Classes", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        # Create notebook for upcoming and past classes
        notebook = ttk.Notebook(container)
        notebook.pack(fill='both', expand=True)
        
        # Upcoming classes tab
        upcoming_frame = tk.Frame(notebook, bg='white')
        notebook.add(upcoming_frame, text="Upcoming Classes")
        
        # Past classes tab
        past_frame = tk.Frame(notebook, bg='white')
        notebook.add(past_frame, text="Past Classes")
        
        self.display_member_classes(upcoming_frame, future=True)
        self.display_member_classes(past_frame, future=False)
    
    def display_member_classes(self, parent, future=True):
        """Helper to display member classes"""
        member_id = self.user_data["id"]
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        if future:
            query = """
                SELECT c.class_id, c.class_name, c.class_type, c.class_date, 
                       c.class_time, c.duration_minutes, c.zone_id, z.zone_name,
                       s.first_name || ' ' || s.last_name as trainer_name,
                       b.branch_name, cr.registration_date
                FROM class_registrations cr
                JOIN classes c ON cr.class_id = c.class_id
                LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
                LEFT JOIN staff s ON c.trainer_id = s.staff_id
                LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
                WHERE cr.member_id = ? AND c.class_date >= DATE('now')
                ORDER BY c.class_date, c.class_time
            """
        else:
            query = """
                SELECT c.class_id, c.class_name, c.class_type, c.class_date, 
                       c.class_time, c.duration_minutes, c.zone_id, z.zone_name,
                       s.first_name || ' ' || s.last_name as trainer_name,
                       b.branch_name, cr.registration_date,
                       CASE 
                           WHEN a.check_in IS NOT NULL THEN 'Attended'
                           ELSE 'Not Attended'
                       END as attendance
                FROM class_registrations cr
                JOIN classes c ON cr.class_id = c.class_id
                LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
                LEFT JOIN staff s ON c.trainer_id = s.staff_id
                LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
                LEFT JOIN attendance a ON a.member_id = cr.member_id 
                    AND DATE(a.check_in) = c.class_date
                WHERE cr.member_id = ? AND c.class_date < DATE('now')
                ORDER BY c.class_date DESC
            """
        
        cursor.execute(query, (member_id,))
        classes = cursor.fetchall()
        conn.close()
        
        if classes:
            # Create treeview
            columns = ['Class Name', 'Type', 'Date', 'Time', 'Duration', 'Trainer', 'Branch', 'Zone']
            if not future:
                columns.append('Status')
            
            tree = ttk.Treeview(parent, columns=columns, show='headings', height=15)
            
            for col in columns:
                tree.heading(col, text=col)
                tree.column(col, width=100)
            
            # Add scrollbars
            vsb = ttk.Scrollbar(parent, orient="vertical", command=tree.yview)
            hsb = ttk.Scrollbar(parent, orient="horizontal", command=tree.xview)
            tree.configure(yscrollcommand=vsb.set, xscrollcommand=hsb.set)
            
            tree.grid(row=0, column=0, sticky='nsew')
            vsb.grid(row=0, column=1, sticky='ns')
            hsb.grid(row=1, column=0, sticky='ew')
            
            parent.grid_rowconfigure(0, weight=1)
            parent.grid_columnconfigure(0, weight=1)
            
            for cls in classes:
                if future:
                    values = [cls[1], cls[2], cls[3], cls[4], f"{cls[5]} min", cls[8], cls[9], cls[7]]
                else:
                    values = [cls[1], cls[2], cls[3], cls[4], f"{cls[5]} min", cls[8], cls[9], cls[7], cls[11]]
                tree.insert('', 'end', values=values)
            
            # Add cancel button for upcoming classes
            if future:
                btn_frame = tk.Frame(parent, bg='white')
                btn_frame.grid(row=2, column=0, pady=10)
                
                def cancel_class():
                    selected = tree.selection()
                    if selected:
                        class_id = classes[tree.index(selected[0])][0]
                        if messagebox.askyesno("Confirm", "Cancel registration for this class?"):
                            conn = self.db.get_connection()
                            cursor = conn.cursor()
                            cursor.execute(
                                "DELETE FROM class_registrations WHERE class_id = ? AND member_id = ?",
                                (class_id, member_id)
                            )
                            conn.commit()
                            conn.close()
                            messagebox.showinfo("Success", "Registration cancelled")
                            self.show_my_classes()  # Refresh
                
                tk.Button(btn_frame, text="Cancel Registration", 
                         command=cancel_class,
                         bg='#e74c3c', fg='white', padx=20, pady=5).pack()
        else:
            tk.Label(parent, text="No classes found", 
                    font=('Arial', 14), bg='white', fg='#7f8c8d').pack(expand=True)

    def show_my_schedule(self):
        """Show member's appointment schedule"""
        self.clear_main_content()
        
        container = tk.Frame(self.main_content, bg='#ecf0f1', padx=20, pady=20)
        container.pack(fill='both', expand=True)
        
        tk.Label(container, text="My Appointment Schedule", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get appointments
        cursor.execute("""
            SELECT a.appointment_id, a.appointment_type, a.appointment_date,
                   a.start_time, a.end_time, a.status,
                   s.first_name || ' ' || s.last_name as trainer_name,
                   z.zone_name
            FROM appointments a
            LEFT JOIN staff s ON a.trainer_id = s.staff_id
            LEFT JOIN workout_zones z ON a.zone_id = z.zone_id
            WHERE a.member_id = ?
            ORDER BY a.appointment_date, a.start_time
        """, (self.user_data["id"],))
        
        appointments = cursor.fetchall()
        conn.close()
        
        if appointments:
            # Create treeview
            columns = ('Type', 'Date', 'Start Time', 'End Time', 'Trainer', 'Zone', 'Status')
            tree = ttk.Treeview(container, columns=columns, show='headings', height=15)
            
            for col in columns:
                tree.heading(col, text=col)
                tree.column(col, width=120)
            
            # Add scrollbars
            vsb = ttk.Scrollbar(container, orient="vertical", command=tree.yview)
            hsb = ttk.Scrollbar(container, orient="horizontal", command=tree.xview)
            tree.configure(yscrollcommand=vsb.set, xscrollcommand=hsb.set)
            
            tree.pack(side='left', fill='both', expand=True)
            vsb.pack(side='right', fill='y')
            hsb.pack(side='bottom', fill='x')
            
            for apt in appointments:
                tree.insert('', 'end', values=apt[1:])
            
            # Buttons
            btn_frame = tk.Frame(container, bg='#ecf0f1')
            btn_frame.pack(fill='x', pady=20)
            
            tk.Button(btn_frame, text="Book New Appointment", 
                     command=self.show_appointment_management,
                     bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=5)
            
            def cancel_appointment():
                selected = tree.selection()
                if selected:
                    apt_id = appointments[tree.index(selected[0])][0]
                    if messagebox.askyesno("Confirm", "Cancel this appointment?"):
                        conn = self.db.get_connection()
                        cursor = conn.cursor()
                        cursor.execute(
                            "UPDATE appointments SET status='Cancelled' WHERE appointment_id = ?",
                            (apt_id,)
                        )
                        conn.commit()
                        conn.close()
                        messagebox.showinfo("Success", "Appointment cancelled")
                        self.show_my_schedule()  # Refresh
            
            tk.Button(btn_frame, text="Cancel Selected", 
                     command=cancel_appointment,
                     bg='#e74c3c', fg='white', padx=20, pady=5).pack(side='left', padx=5)
        else:
            tk.Label(container, text="No appointments scheduled", 
                    font=('Arial', 14), bg='#ecf0f1', fg='#7f8c8d').pack(expand=True)
            
            tk.Button(container, text="Book Your First Appointment", 
                     command=self.show_appointment_management,
                     bg='#3498db', fg='white', padx=30, pady=10).pack(pady=20)
    
    def show_my_payments(self):
        """Show member's payment history"""
        self.clear_main_content()
        
        container = tk.Frame(self.main_content, bg='#ecf0f1', padx=20, pady=20)
        container.pack(fill='both', expand=True)
        
        tk.Label(container, text="My Payment History", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get payments
        cursor.execute("""
            SELECT payment_id, payment_date, amount, payment_type, 
                   payment_method, subscription_period, discount_applied, 
                   final_amount, invoice_number, status
            FROM payments
            WHERE member_id = ?
            ORDER BY payment_date DESC
        """, (self.user_data["id"],))
        
        payments = cursor.fetchall()
        
        # Get membership summary
        cursor.execute("""
            SELECT membership_type, join_date, expiry_date, status
            FROM members
            WHERE member_id = ?
        """, (self.user_data["id"],))
        membership = cursor.fetchone()
        conn.close()
        
        # Membership summary card
        if membership:
            summary_frame = tk.Frame(container, bg='#3498db', relief='raised', bd=1)
            summary_frame.pack(fill='x', pady=20)
            
            expiry_date = datetime.strptime(membership[2], '%Y-%m-%d')
            days_left = (expiry_date - datetime.now()).days
            
            tk.Label(summary_frame, text="Current Membership", font=('Arial', 14, 'bold'),
                    bg='#3498db', fg='white').pack(pady=(15, 5))
            
            info_frame = tk.Frame(summary_frame, bg='#3498db')
            info_frame.pack(pady=10)
            
            tk.Label(info_frame, text=f"Type: {membership[0]}", font=('Arial', 12),
                    bg='#3498db', fg='white').grid(row=0, column=0, padx=20)
            tk.Label(info_frame, text=f"Status: {membership[3]}", font=('Arial', 12),
                    bg='#3498db', fg='white').grid(row=0, column=1, padx=20)
            tk.Label(info_frame, text=f"Days Left: {days_left}", font=('Arial', 12),
                    bg='#3498db', fg='white').grid(row=0, column=2, padx=20)
            
            tk.Label(summary_frame, text=f"Expires: {membership[2]}", font=('Arial', 11),
                    bg='#3498db', fg='white').pack(pady=(0, 15))
        
        if payments:
            # Create treeview for payments
            columns = ('Date', 'Invoice #', 'Type', 'Amount', 'Discount', 'Final Amount', 'Method', 'Status')
            tree = ttk.Treeview(container, columns=columns, show='headings', height=12)
            
            for col in columns:
                tree.heading(col, text=col)
                tree.column(col, width=100)
            
            # Add scrollbars
            vsb = ttk.Scrollbar(container, orient="vertical", command=tree.yview)
            hsb = ttk.Scrollbar(container, orient="horizontal", command=tree.xview)
            tree.configure(yscrollcommand=vsb.set, xscrollcommand=hsb.set)
            
            tree.pack(side='left', fill='both', expand=True)
            vsb.pack(side='right', fill='y')
            hsb.pack(side='bottom', fill='x')
            
            total_paid = 0
            for payment in payments:
                values = [
                    payment[1],  # date
                    payment[8],  # invoice
                    payment[3],  # type
                    f"PKR {payment[2]:,.0f}",  # amount
                    f"PKR {payment[6]:,.0f}",  # discount
                    f"PKR {payment[7]:,.0f}",  # final
                    payment[4],  # method
                    payment[9]   # status
                ]
                tree.insert('', 'end', values=values)
                total_paid += payment[7] if payment[9] == 'Completed' else 0
            
            # Total summary
            summary_bar = tk.Frame(container, bg='#ecf0f1')
            summary_bar.pack(fill='x', pady=20)
            
            tk.Label(summary_bar, text=f"Total Paid: PKR {total_paid:,.0f}", 
                    font=('Arial', 14, 'bold'), bg='#ecf0f1', fg='#27ae60').pack(side='left')
            
            tk.Button(summary_bar, text="Make New Payment", 
                     command=self.show_payment_management,
                     bg='#3498db', fg='white', padx=20, pady=5).pack(side='right')
        else:
            tk.Label(container, text="No payment history found", 
                    font=('Arial', 14), bg='#ecf0f1', fg='#7f8c8d').pack(expand=True)
            
            tk.Button(container, text="Make Your First Payment", 
                     command=self.show_payment_management,
                     bg='#3498db', fg='white', padx=30, pady=10).pack(pady=20)
    
    def show_admin_dashboard(self):
        self.clear_main_content()
        
        # Dashboard title
        title_frame = tk.Frame(self.main_content, bg='#ecf0f1')
        title_frame.pack(fill='x', padx=20, pady=20)
        
        tk.Label(title_frame, text="Dashboard Overview", font=('Arial', 20, 'bold'),
                bg='#ecf0f1').pack(side='left')
        
        # Statistics cards
        stats_frame = tk.Frame(self.main_content, bg='#ecf0f1')
        stats_frame.pack(fill='x', padx=20, pady=10)
        
        stats_data = self.get_statistics()
        
        # Create stat cards
        colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c']
        stats = [
            ("Total Members", stats_data['total_members'], colors[0]),
            ("Active Members", stats_data['active_members'], colors[1]),
            ("Today's Appointments", stats_data['today_appointments'], colors[2]),
            ("Monthly Revenue", f"PKR {stats_data['monthly_revenue']:,.0f}", colors[3]),
            ("Staff Members", stats_data['staff_count'], colors[4]),
            ("Occupancy Rate", f"{stats_data['occupancy_rate']}%", colors[5])
        ]
        
        for i, (title, value, color) in enumerate(stats):
            card = tk.Frame(stats_frame, bg=color, relief='raised', bd=1)
            card.grid(row=i//3, column=i%3, padx=10, pady=10, sticky='nsew')
            
            tk.Label(card, text=title, font=('Arial', 11),
                    bg=color, fg='white').pack(pady=(15, 5))
            tk.Label(card, text=str(value), font=('Arial', 24, 'bold'),
                    bg=color, fg='white').pack(pady=(5, 15))
            
            stats_frame.grid_columnconfigure(i%3, weight=1)
        
        # Charts frame
        charts_frame = tk.Frame(self.main_content, bg='#ecf0f1')
        charts_frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Left chart - Membership by type
        left_chart_frame = tk.Frame(charts_frame, bg='white', relief='sunken', bd=1)
        left_chart_frame.grid(row=0, column=0, padx=10, pady=10, sticky='nsew')
        
        tk.Label(left_chart_frame, text="Membership Distribution", 
                font=('Arial', 12, 'bold'), bg='white').pack(pady=10)
        
        self.create_membership_chart(left_chart_frame)
        
        # Right chart - Monthly Revenue
        right_chart_frame = tk.Frame(charts_frame, bg='white', relief='sunken', bd=1)
        right_chart_frame.grid(row=0, column=1, padx=10, pady=10, sticky='nsew')
        
        tk.Label(right_chart_frame, text="Monthly Revenue Trend", 
                font=('Arial', 12, 'bold'), bg='white').pack(pady=10)
        
        self.create_revenue_chart(right_chart_frame)
        
        charts_frame.grid_columnconfigure(0, weight=1)
        charts_frame.grid_columnconfigure(1, weight=1)
        charts_frame.grid_rowconfigure(0, weight=1)
        
    def show_branch_zone_management(self):
        if self.user_role not in ("Manager", "Admin"):
            messagebox.showerror("Access Denied", "Admin only")
            return
        self.clear_main_content()
        BranchZoneManagement(self.main_content, self.db)
        
    def show_class_management(self):
        self.clear_main_content()
        ClassManagement(self.main_content, self.db, self.user_data)

    def get_trainer_member_count(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()

        trainer_id = self.user_data["id"]
        branch_id = self.user_data["branch_id"]
        zone_id = self.user_data["zone_id"]
   
        # Total members assigned to trainer
        cursor.execute("""
            SELECT COUNT(*)
            FROM members
            WHERE branch_id = ?
            AND zone_id = ?
        """, (branch_id, zone_id))
        total_members = cursor.fetchone()[0]

        # Active members
        cursor.execute("""
            SELECT COUNT(*)
            FROM members
            WHERE branch_id = ?
            AND zone_id = ?
            AND status = 'Active'
        """, (branch_id, zone_id))
        active_members = cursor.fetchone()[0]

        # Today's appointments
        cursor.execute("""
            SELECT COUNT(*)
            FROM appointments
            WHERE trainer_id = ?
            AND DATE(appointment_date) = DATE('now', 'localtime')
        """, (trainer_id,))
        today_appointments = cursor.fetchone()[0]

        conn.close()

        return {
            "total_members": total_members,
            "active_members": active_members,
            "today_appointments": today_appointments
        }
        
    def show_trainer_dashboard(self):
        stats = self.get_trainer_member_count()
        container = tk.Frame(self.main_content, bg='#ecf0f1')
        container.pack(fill='both', expand=True, padx=20, pady=20)

        tk.Label(
            container,
            text="Trainer Dashboard",
            font=('Arial', 20, 'bold'),
            bg='#ecf0f1'
        ).pack(anchor='w', pady=(0, 20))

        # Trainer cards
        cards = [
            ("Total Members", stats["total_members"]),
            ("Active Members", stats["active_members"]),
            ("Today's Sessions", stats["today_appointments"])
        ]

        card_frame = tk.Frame(container, bg='#ecf0f1')
        card_frame.pack(fill='x')

        for i, (title, value) in enumerate(cards):
            card = tk.Frame(card_frame, bg='#2ecc71', bd=1, relief='raised')
            card.grid(row=0, column=i, padx=10, pady=10, sticky='nsew')

            tk.Label(card, text=title, font=('Arial', 12),
                    bg='#2ecc71', fg='white').pack(pady=(15, 5))
            tk.Label(card, text=value, font=('Arial', 26, 'bold'),
                    bg='#2ecc71', fg='white').pack(pady=(5, 15))

            card_frame.grid_columnconfigure(i, weight=1)

        # Trainer quick actions
        actions = tk.LabelFrame(container, text="Quick Actions",
                                font=('Arial', 12, 'bold'),
                                bg='white')
        actions.pack(fill='x', pady=20)

        tk.Button(actions, text="View My Members",
                command=self.show_trainer_members,
                bg='#3498db', fg='white', padx=20, pady=10).pack(side='left', padx=10, pady=10)

        tk.Button(actions, text="View Appointments",
                command=self.show_appointment_management,
                bg='#3498db', fg='white', padx=20, pady=10).pack(side='left', padx=10, pady=10)
        
        tk.Button(actions, text="My Classes",
                command=self.show_class_management,
                bg='#3498db', fg='white', padx=20, pady=10).pack(side='left', padx=10, pady=10)

    def show_trainer_members(self):
        """Show only members assigned to this trainer's branch and zone"""
        self.clear_main_content()
        
        # Create a custom member management view for trainers
        container = tk.Frame(self.main_content, bg='#ecf0f1', padx=20, pady=20)
        container.pack(fill='both', expand=True)
        
        tk.Label(container, text="My Assigned Members", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        # Get trainer's branch and zone
        trainer_branch = self.user_data.get('branch_id')
        trainer_zone = self.user_data.get('zone_id')
        
        if not trainer_branch or not trainer_zone:
            messagebox.showerror("Error", "Trainer not assigned to any branch/zone")
            return
        
        # Create filter frame
        filter_frame = tk.Frame(container, bg='#ecf0f1')
        filter_frame.pack(fill='x', pady=10)
        
        # Get branch and zone names
        branch_name = self.get_branch_name(trainer_branch)
        zone_name = self.get_zone_name(trainer_zone)
        
        tk.Label(filter_frame, text=f"Branch: {branch_name}", 
                font=('Arial', 11, 'bold'), bg='#ecf0f1', fg='#2c3e50').pack(side='left', padx=10)
        tk.Label(filter_frame, text=f"Zone: {zone_name}", 
                font=('Arial', 11, 'bold'), bg='#ecf0f1', fg='#2c3e50').pack(side='left', padx=10)
        
        # Create treeview for members
        columns = ('ID', 'Name', 'Email', 'Phone', 'Membership', 'Status', 'Join Date')
        tree = ttk.Treeview(container, columns=columns, show='headings', height=20)
        
        # Define headings
        for col in columns:
            tree.heading(col, text=col)
            tree.column(col, width=100)
        
        # Add scrollbars
        vsb = ttk.Scrollbar(container, orient="vertical", command=tree.yview)
        hsb = ttk.Scrollbar(container, orient="horizontal", command=tree.xview)
        tree.configure(yscrollcommand=vsb.set, xscrollcommand=hsb.set)
        
        tree.pack(side='left', fill='both', expand=True)
        vsb.pack(side='right', fill='y')
        hsb.pack(side='bottom', fill='x')
        
        # Load members from trainer's branch and zone
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT member_id, first_name || ' ' || last_name as name, 
                   email, phone, membership_type, status, join_date
            FROM members 
            WHERE branch_id = ? AND zone_id = ?
            ORDER BY join_date DESC
        """, (trainer_branch, trainer_zone))
        
        members = cursor.fetchall()
        conn.close()
        
        if members:
            for member in members:
                tree.insert('', 'end', values=member)
        else:
            # Show "no members" message in treeview
            tree.insert('', 'end', values=('No members found in your zone', '', '', '', '', '', ''))
            tree.item(tree.get_children()[0], tags=('no_data',))
            tree.tag_configure('no_data', foreground='gray')
        
        # Action buttons
        button_frame = tk.Frame(container, bg='#ecf0f1')
        button_frame.pack(fill='x', pady=20)
        
        def view_member():
            selected = tree.selection()
            if selected:
                # Check if it's the "no data" message
                if tree.item(selected[0])['values'][0] == 'No members found in your zone':
                    return
                member_id = tree.item(selected[0])['values'][0]
                self.view_member_details(member_id)
        
        tk.Button(button_frame, text="View Details", 
                command=view_member,
                bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=5)
        
        # Refresh button
        tk.Button(button_frame, text="Refresh", 
                command=self.show_trainer_members,
                bg='#2ecc71', fg='white', padx=20, pady=5).pack(side='left', padx=5)
        
      
    def view_member_details(self, member_id):
        """View member details (read-only for trainers)"""
        self.clear_main_content()
        
        # Create a container for the member details
        container = tk.Frame(self.main_content, bg='#ecf0f1')
        container.pack(fill='both', expand=True)
        
        # Get full member data
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT m.*, b.branch_name, z.zone_name 
            FROM members m
            LEFT JOIN gym_branches b ON m.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON m.zone_id = z.zone_id
            WHERE m.member_id = ?
        """, (member_id,))
        
        member_row = cursor.fetchone()
        
        if not member_row:
            conn.close()
            messagebox.showerror("Error", "Member not found")
            self.show_trainer_members()
            return
        
        # Get column names
        cursor.execute("PRAGMA table_info(members)")
        columns = [col[1] for col in cursor.fetchall()]
        
        # Create member dictionary
        member_dict = {}
        for i, col in enumerate(columns):
            member_dict[col] = member_row[i]
        
        # Add branch and zone names
        member_dict['branch_name'] = member_row[17] if len(member_row) > 17 else 'N/A'
        member_dict['zone_name'] = member_row[18] if len(member_row) > 18 else 'N/A'
        
        conn.close()
        
        # Create a title
        title_frame = tk.Frame(container, bg='#ecf0f1')
        title_frame.pack(fill='x', padx=20, pady=20)
        
        tk.Label(title_frame, text=f"Member Details: {member_dict.get('first_name', '')} {member_dict.get('last_name', '')}", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(side='left')
        
        # Create a frame for the details
        details_frame = tk.Frame(container, bg='white', relief='sunken', bd=1)
        details_frame.pack(fill='both', expand=True, padx=20, pady=10)
        
        # Create scrollable canvas for details
        canvas = tk.Canvas(details_frame, bg='white', highlightthickness=0)
        scrollbar = ttk.Scrollbar(details_frame, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='white')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        # Personal Information Section
        tk.Label(scrollable_frame, text="Personal Information", 
                font=('Arial', 14, 'bold'), bg='white', fg='#3498db').pack(anchor='w', padx=20, pady=(20, 10))
        
        personal_fields = [
            ("First Name:", "first_name"),
            ("Last Name:", "last_name"),
            ("Email:", "email"),
            ("Phone:", "phone"),
            ("Date of Birth:", "date_of_birth"),
            ("Gender:", "gender"),
            ("Address:", "address"),
            ("City:", "city"),
            ("Emergency Contact:", "emergency_contact"),
            ("Medical Conditions:", "medical_conditions"),
            ("Fitness Goals:", "fitness_goals"),
        ]
        
        for label, field in personal_fields:
            field_frame = tk.Frame(scrollable_frame, bg='white')
            field_frame.pack(fill='x', padx=20, pady=2)
            
            tk.Label(field_frame, text=label, font=('Arial', 11, 'bold'),
                    bg='white', width=20, anchor='w').pack(side='left')
            tk.Label(field_frame, text=member_dict.get(field, 'N/A'), 
                    font=('Arial', 11), bg='white', anchor='w').pack(side='left', padx=10)
        
        # Membership Details Section
        tk.Label(scrollable_frame, text="Membership Details", 
                font=('Arial', 14, 'bold'), bg='white', fg='#3498db').pack(anchor='w', padx=20, pady=(20, 10))
        
        membership_fields = [
            ("Membership Type:", "membership_type"),
            ("Subscription Plan:", "subscription_plan"),
            ("Join Date:", "join_date"),
            ("Expiry Date:", "expiry_date"),
            ("Status:", "status"),
            ("Branch:", "branch_name"),
            ("Zone:", "zone_name"),
        ]
        
        for label, field in membership_fields:
            field_frame = tk.Frame(scrollable_frame, bg='white')
            field_frame.pack(fill='x', padx=20, pady=2)
            
            tk.Label(field_frame, text=label, font=('Arial', 11, 'bold'),
                    bg='white', width=20, anchor='w').pack(side='left')
            tk.Label(field_frame, text=member_dict.get(field, 'N/A'), 
                    font=('Arial', 11), bg='white', anchor='w').pack(side='left', padx=10)
        
        # Back button
        button_frame = tk.Frame(container, bg='#ecf0f1')
        button_frame.pack(fill='x', padx=20, pady=20)
        
        tk.Button(button_frame, text="Back to Members List", 
                 command=self.show_trainer_members,
                 bg='#3498db', fg='white', font=('Arial', 11),
                 padx=30, pady=5).pack(side='left')
        
    

    def get_branch_name(self, branch_id):
        """Get branch name by ID"""
        if not branch_id:
            return "Unknown"
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT branch_name FROM gym_branches WHERE branch_id = ?", (branch_id,))
        result = cursor.fetchone()
        conn.close()
        return result[0] if result else "Unknown"

    def get_zone_name(self, zone_id):
        """Get zone name by ID"""
        if not zone_id:
            return "Unknown"
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT zone_name FROM workout_zones WHERE zone_id = ?", (zone_id,))
        result = cursor.fetchone()
        conn.close()
        return result[0] if result else "Unknown"

    def show_member_management(self):
        """Handle role-based member views"""
        if self.user_role == "Trainer":
            self.show_trainer_members()
        else:
            self.clear_main_content()
            MemberManagement(self.main_content, self.db)

    def show_appointment_management(self):
        """Handle role-based appointment views"""
        self.clear_main_content()
        AppointmentManagement(self.main_content, self.db, self.user_data)

    def show_payment_management(self):
        self.clear_main_content()
        PaymentManagement(self.main_content, self.db)
    
    def show_staff_dashboard(self):
        if self.user_role == "Manager" or self.user_role == "Admin":
            self.clear_main_content()
            StaffDashboard(self.main_content, self.db)
        else:
            messagebox.showinfo("Access Denied", "Staff management requires Manager privileges")
    
    def show_reports(self):
        self.clear_main_content()
        ReportsWindow(self.main_content, self.db)
    
    def show_branch_zone_management(self):
        if self.user_role not in ("Manager", "Admin"):
            messagebox.showerror("Access Denied", "Admin only")
            return
        self.clear_main_content()
        BranchZoneManagement(self.main_content, self.db)
    
    def show_class_management(self):
        self.clear_main_content()
        ClassManagement(self.main_content, self.db, self.user_data)
    
    def get_statistics(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        stats = {}
        
        # Total members
        cursor.execute("SELECT COUNT(*) FROM members")
        stats['total_members'] = cursor.fetchone()[0]
        
        # Active members
        cursor.execute("SELECT COUNT(*) FROM members WHERE status='Active'")
        stats['active_members'] = cursor.fetchone()[0]
        
        # Today's appointments
        today = datetime.now().strftime('%Y-%m-%d')
        cursor.execute("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? AND status='Scheduled'", (today,))
        stats['today_appointments'] = cursor.fetchone()[0]
        
        # Monthly revenue
        current_month = datetime.now().strftime('%Y-%m')
        cursor.execute("""
            SELECT SUM(final_amount) FROM payments 
            WHERE strftime('%Y-%m', payment_date) = ? AND status='Completed'
        """, (current_month,))
        result = cursor.fetchone()[0]
        stats['monthly_revenue'] = result if result else 0
        
        # Staff count
        cursor.execute("SELECT COUNT(*) FROM staff WHERE status='Active'")
        stats['staff_count'] = cursor.fetchone()[0]
        
        # Occupancy rate (simplified)
        cursor.execute("SELECT COUNT(*) FROM attendance WHERE date(check_in) = date('now')")
        today_attendance = cursor.fetchone()[0]
        stats['occupancy_rate'] = min(round((today_attendance / max(stats['active_members'], 1)) * 100), 100)
        
        conn.close()
        return stats
    
    def create_membership_chart(self, parent):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT membership_type, COUNT(*) 
            FROM members 
            GROUP BY membership_type
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            types = [row[0] for row in data]
            counts = [row[1] for row in data]
            colors = ['#3498db', '#2ecc71', '#e74c3c']
            
            fig, ax = plt.subplots(figsize=(5, 3))
            ax.pie(counts, labels=types, autopct='%1.1f%%', colors=colors[:len(types)])
            ax.set_title('Membership Types')
            
            canvas = FigureCanvasTkAgg(fig, parent)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
    
    def create_revenue_chart(self, parent):
        # Sample revenue data for last 6 months
        months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
        revenue = [280000, 320000, 350000, 400000, 450000, 500000]
        
        fig, ax = plt.subplots(figsize=(5, 3))
        ax.bar(months, revenue, color='#3498db')
        ax.set_title('Monthly Revenue (PKR)')
        ax.set_ylabel('Amount')
        ax.tick_params(axis='x', rotation=45)
        
        # Format y-axis with commas
        import matplotlib.ticker as ticker
        ax.yaxis.set_major_formatter(ticker.StrMethodFormatter('{x:,.0f}'))
        
        canvas = FigureCanvasTkAgg(fig, parent)
        canvas.draw()
        canvas.get_tk_widget().pack(fill='both', expand=True)
    
    def logout(self):
        if messagebox.askyesno("Logout", "Are you sure you want to logout?"):
            # Clear dashboard UI
            for widget in self.root.winfo_children():
                widget.destroy()

            # Tell main.py to show login
            if self.on_logout:
                self.on_logout()

    def load_statistics(self):
        # This would load real statistics from database
        pass