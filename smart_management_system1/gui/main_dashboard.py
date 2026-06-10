import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime
import matplotlib.pyplot as plt
from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
import database
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
        
        buttons = [
            ("🏠 Dashboard", self.show_dashboard),
            ("👥 Members", self.show_member_management),
            ("📅 Appointments", self.show_appointment_management),
            ("💳 Payments", self.show_payment_management),
            ("📊 Reports", self.show_reports),
            ("👨‍💼 Staff", self.show_staff_dashboard),
            ("🏢 Branches & Zones", self.show_branch_zone_management),
            ("🏋️ Classes", self.show_class_management),
            ("🚪 Logout", self.logout)
        ]
        
        # Role-based filtering
        role_buttons = {
            "Member": ["🏠 Dashboard", "📅 Appointments", "🚪 Logout"],
            "Trainer": ["🏠 Dashboard", "👥 Members", "📅 Appointments", "🏋️ Classes", "🚪 Logout"],
            "Admin": None
        }
        
        allowed = role_buttons.get(self.user_role, None)
        if allowed:
            buttons = [b for b in buttons if b[0] in allowed]
        
        for text, command in buttons:
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
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT COUNT(*) FROM members WHERE status='Active'")
        active_members = cursor.fetchone()[0]
        conn.close()
        
        tk.Label(status_frame, text=f"Active Members: {active_members}",
                font=('Arial', 9), bg='#34495e', fg='#bdc3c7').pack(expand=True)

    
    def clear_main_content(self):
        for widget in self.main_content.winfo_children():
            if widget != self.main_content.winfo_children()[0]:  # Keep header
                widget.destroy()
                
    def show_dashboard(self):
        self.clear_main_content()

        if self.user_role in ("Manager", "Admin"):
            self.show_admin_dashboard()
        elif self.user_role == "Trainer":
            self.show_trainer_dashboard()

    
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


    def get_today_sessions(self):
        return 5

    def get_upcoming_sessions(self):
        return 8
        
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
                command=self.show_member_management).pack(side='left', padx=10, pady=10)

        tk.Button(actions, text="View Appointments",
                command=self.show_appointment_management).pack(side='left', padx=10, pady=10)

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
    
    def show_member_management(self):
        self.clear_main_content()
        MemberManagement(self.main_content, self.db)
    
    def show_appointment_management(self):
        self.clear_main_content()
        AppointmentManagement(self.main_content, self.db, self.user_data)
    
    def show_payment_management(self):
        self.clear_main_content()
        PaymentManagement(self.main_content, self.db)
    
    def show_staff_dashboard(self):
        if self.user_role == "Manager" or self.user_role=="Admin":
            self.clear_main_content()
            StaffDashboard(self.main_content, self.db)
        else:
            messagebox.showinfo("Access Denied", "Staff management requires Manager privileges")
    
    def show_reports(self):
        self.clear_main_content()
        ReportsWindow(self.main_content, self.db)
    
    def show_settings(self):
        self.clear_main_content()
        
        settings_frame = tk.Frame(self.main_content, bg='#ecf0f1', padx=20, pady=20)
        settings_frame.pack(fill='both', expand=True)
        
        tk.Label(settings_frame, text="Settings", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        # Settings content based on role
        if self.user_role == "Manager":
            self.create_manager_settings(settings_frame)
        elif self.user_role == "Trainer":
            self.create_trainer_settings(settings_frame)
        else:
            tk.Label(settings_frame, text="No settings available for your role",
                    font=('Arial', 12), bg='#ecf0f1').pack()
    
    def create_manager_settings(self, parent):
        # Branch settings
        branch_frame = tk.LabelFrame(parent, text="Branch Settings", 
                                    font=('Arial', 12, 'bold'),
                                    bg='white', padx=15, pady=15)
        branch_frame.pack(fill='x', pady=10)
        
        tk.Label(branch_frame, text="Default Branch:", bg='white').grid(row=0, column=0, sticky='w')
        branch_combo = ttk.Combobox(branch_frame, values=["Lahore Gulberg", "Lahore DHA", "Islamabad", "Karachi", "Peshawar"])
        branch_combo.grid(row=0, column=1, padx=10, pady=5)
        branch_combo.set("Lahore Gulberg")
        
        # Notification settings
        notif_frame = tk.LabelFrame(parent, text="Notifications", 
                                   font=('Arial', 12, 'bold'),
                                   bg='white', padx=15, pady=15)
        notif_frame.pack(fill='x', pady=10)
        
        tk.Checkbutton(notif_frame, text="Email notifications for new members", 
                      bg='white').grid(row=0, column=0, sticky='w', pady=2)
        tk.Checkbutton(notif_frame, text="SMS reminders for appointments", 
                      bg='white').grid(row=1, column=0, sticky='w', pady=2)
        tk.Checkbutton(notif_frame, text="Payment due alerts", 
                      bg='white').grid(row=2, column=0, sticky='w', pady=2)
    
    def create_trainer_settings(self, parent):
        # Trainer availability
        avail_frame = tk.LabelFrame(parent, text="My Availability", 
                                   font=('Arial', 12, 'bold'),
                                   bg='white', padx=15, pady=15)
        avail_frame.pack(fill='x', pady=10)
        
        days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        for i, day in enumerate(days):
            tk.Label(avail_frame, text=f"{day}:", bg='white').grid(row=i, column=0, sticky='w', pady=2)
            
            start_var = tk.StringVar(value="09:00")
            end_var = tk.StringVar(value="17:00")
            
            ttk.Entry(avail_frame, textvariable=start_var, width=8).grid(row=i, column=1, padx=5)
            tk.Label(avail_frame, text="to", bg='white').grid(row=i, column=2)
            ttk.Entry(avail_frame, textvariable=end_var, width=8).grid(row=i, column=3, padx=(5, 20))
    
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