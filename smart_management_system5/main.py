# main.py (Updated with Enhanced Navbar)
import tkinter as tk
from tkinter import ttk, messagebox
import matplotlib.pyplot as plt
from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
import matplotlib.font_manager as fm
from datetime import datetime, timedelta
from database import Database
import random

# Professional color scheme
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

class ProfessionalGymManagementSystem:
    def __init__(self, root):
        self.root = root
        self.root.title("M9's Fitness - Smart Gym Management System")
        self.root.geometry("1400x800")
        self.root.configure(bg=COLORS['light'])
        
        # Set window icon (you can add an icon file)
        try:
            self.root.iconbitmap('gym_icon.ico')
        except:
            pass
        
        self.db = Database()
        self.current_user = None
        self.current_role = None
        
        self.setup_styles()
        self.show_login_screen()
    
    def setup_styles(self):
        self.style = ttk.Style()
        
        # Configure styles
        self.style.configure('TFrame', background=COLORS['light'])
        self.style.configure('TLabel', background=COLORS['light'], foreground=COLORS['text'], font=('Segoe UI', 10))
        self.style.configure('TButton', font=('Segoe UI', 10), padding=(15, 8))
        
        # Custom styles
        self.style.configure('Primary.TButton', background=COLORS['accent'], foreground='white')
        self.style.configure('Success.TButton', background=COLORS['success'], foreground='white')
        self.style.configure('Danger.TButton', background=COLORS['danger'], foreground='white')
        
        self.style.configure('Title.TLabel', font=('Segoe UI', 20, 'bold'), foreground=COLORS['primary'])
        self.style.configure('Header.TLabel', font=('Segoe UI', 14, 'bold'), foreground=COLORS['primary'])
        self.style.configure('Subheader.TLabel', font=('Segoe UI', 12, 'bold'), foreground=COLORS['secondary'])
        
        self.style.configure('Card.TFrame', background='white', relief='raised', borderwidth=1)
        self.style.configure('Nav.TFrame', background=COLORS['navbar'])
        self.style.configure('Nav.TLabel', background=COLORS['navbar'], foreground='white')
        
        # Configure treeview
        self.style.configure('Treeview', font=('Segoe UI', 9), rowheight=25)
        self.style.configure('Treeview.Heading', font=('Segoe UI', 10, 'bold'), background=COLORS['light'])
        
        # Set matplotlib style
        plt.style.use('seaborn-v0_8')
    
    def clear_window(self):
        for widget in self.root.winfo_children():
            widget.destroy()
    
    def show_login_screen(self):
        self.clear_window()
        
        # Main container with gradient effect
        main_container = tk.Frame(self.root, bg=COLORS['primary'])
        main_container.pack(fill=tk.BOTH, expand=True)
        
        # Login card
        login_card = tk.Frame(main_container, bg='white', relief='raised', bd=2)
        login_card.place(relx=0.5, rely=0.5, anchor='center', width=450, height=550)
        
        # Logo/Title section
        title_frame = tk.Frame(login_card, bg=COLORS['accent'], height=100)
        title_frame.pack(fill=tk.X)
        title_frame.pack_propagate(False)
        
        title_label = tk.Label(title_frame, text="M9's Fitness", font=('Segoe UI', 24, 'bold'), 
                            bg=COLORS['accent'], fg='white')
        title_label.pack(expand=True)
        
        subtitle_label = tk.Label(title_frame, text="Staff Management System", font=('Segoe UI', 12), 
                                bg=COLORS['accent'], fg='white')
        subtitle_label.pack(pady=(0, 20))
        
        # Form section
        form_frame = tk.Frame(login_card, bg='white', padx=30, pady=30)
        form_frame.pack(fill=tk.BOTH, expand=True)
        
        # Role selection
        tk.Label(form_frame, text="Staff Role", font=('Segoe UI', 11, 'bold'), 
                bg='white', fg=COLORS['text']).pack(anchor='w', pady=(10, 5))
        self.role_var = tk.StringVar(value="admin")
        role_combo = ttk.Combobox(form_frame, textvariable=self.role_var, 
                                values=["admin", "manager", "attendant", "trainer"], 
                                state="readonly", font=('Segoe UI', 10))
        role_combo.pack(fill=tk.X, pady=(0, 15))
        
        # Username
        tk.Label(form_frame, text="Username", font=('Segoe UI', 11, 'bold'), 
                bg='white', fg=COLORS['text']).pack(anchor='w', pady=(10, 5))
        self.username_entry = ttk.Entry(form_frame, font=('Segoe UI', 11))
        self.username_entry.pack(fill=tk.X, pady=(0, 15))
        
        # Password
        tk.Label(form_frame, text="Password", font=('Segoe UI', 11, 'bold'), 
                bg='white', fg=COLORS['text']).pack(anchor='w', pady=(10, 5))
        self.password_entry = ttk.Entry(form_frame, font=('Segoe UI', 11), show="•")
        self.password_entry.pack(fill=tk.X, pady=(0, 20))
        
        # Login button
        login_btn = tk.Button(form_frame, text="STAFF LOGIN", font=('Segoe UI', 12, 'bold'), 
                            bg=COLORS['accent'], fg='white', relief='flat',
                            command=self.login, cursor='hand2', height=2)
        login_btn.pack(fill=tk.X, pady=(10, 20))
        
        # Demo credentials
        demo_frame = tk.Frame(form_frame, bg='white')
        demo_frame.pack(fill=tk.X)
        
        demo_text = """Staff Demo Credentials:
    • Admin: admin / admin123
    • Manager: manager / manager123  
    • Attendant: attendant / attendant123
    • Trainer: trainer / trainer123

    All passwords are: role123 (e.g., admin123)"""
        
        demo_label = tk.Label(demo_frame, text=demo_text, font=('Segoe UI', 9), 
                            bg='white', fg=COLORS['text_light'], justify=tk.LEFT)
        demo_label.pack(anchor='w')
        
        # Bind Enter key to login
        self.password_entry.bind('<Return>', lambda e: self.login())
        
        # Focus on username field
        self.username_entry.focus()
    
    def login(self):
        username = self.username_entry.get()
        password = self.password_entry.get()
        role = self.role_var.get()
        
        if not username or not password:
            messagebox.showerror("Error", "Please enter both username and password")
            return
        
        user = self.db.verify_user(username, password, role)
        if user:
            self.current_user = user
            self.current_role = role
            self.create_main_dashboard()
        else:
            messagebox.showerror("Error", "Invalid credentials or role mismatch")
    
    def create_main_dashboard(self):
        self.clear_window()
        
        # Create header with navbar
        self.create_header_navbar()
        
        # Main content area
        self.main_content = tk.Frame(self.root, bg=COLORS['light'])
        self.main_content.pack(fill=tk.BOTH, expand=True)
        
        # Show dashboard by default
        self.show_dashboard()
    
    def create_header_navbar(self):
        # Main header frame
        self.header_frame = tk.Frame(self.root, bg=COLORS['navbar'], height=120)
        self.header_frame.pack(fill=tk.X)
        self.header_frame.pack_propagate(False)
        
        # Top bar - User info and quick actions
        top_bar = tk.Frame(self.header_frame, bg=COLORS['navbar'], height=40)
        top_bar.pack(fill=tk.X)
        top_bar.pack_propagate(False)
        
        # Left side - Welcome message
        welcome_frame = tk.Frame(top_bar, bg=COLORS['navbar'])
        welcome_frame.pack(side=tk.LEFT, padx=20, pady=8)
        
        welcome_text = f"Welcome, {self.current_user[1]} {self.current_user[2]}"
        welcome_label = tk.Label(welcome_frame, text=welcome_text, font=('Segoe UI', 11, 'bold'), 
                                bg=COLORS['navbar'], fg='white')
        welcome_label.pack(side=tk.LEFT)
        
        # Role badge
        role_badge = tk.Label(welcome_frame, text=self.current_role.upper(), 
                             font=('Segoe UI', 9, 'bold'), bg=COLORS['accent'], fg='white',
                             padx=12, pady=4, bd=0, relief='flat')
        role_badge.pack(side=tk.LEFT, padx=10)
        
        # Right side - Quick actions and user menu
        quick_actions_frame = tk.Frame(top_bar, bg=COLORS['navbar'])
        quick_actions_frame.pack(side=tk.RIGHT, padx=20, pady=8)
        
        # Quick action buttons
        quick_actions = [
            ("🔔 Notifications", self.show_notifications),
            ("⚙️ Settings", self.show_settings),
            ("❓ Help", self.show_help)
        ]
        
        for text, command in quick_actions:
            btn = tk.Button(quick_actions_frame, text=text, font=('Segoe UI', 9), 
                           bg=COLORS['navbar'], fg='white', relief='flat',
                           command=command, cursor='hand2', bd=0)
            btn.pack(side=tk.LEFT, padx=8)
        
        # Logout button
        logout_btn = tk.Button(quick_actions_frame, text="🚪 Logout", font=('Segoe UI', 9, 'bold'), 
                              bg=COLORS['danger'], fg='white', relief='flat',
                              command=self.show_login_screen, cursor='hand2', padx=12)
        logout_btn.pack(side=tk.LEFT, padx=8)
        
        # Main Navigation Bar
        nav_bar = tk.Frame(self.header_frame, bg=COLORS['primary'], height=60)
        nav_bar.pack(fill=tk.X, side=tk.BOTTOM)
        nav_bar.pack_propagate(False)
        
        # Brand/Logo section
        brand_frame = tk.Frame(nav_bar, bg=COLORS['primary'])
        brand_frame.pack(side=tk.LEFT, padx=20)
        
        brand_label = tk.Label(brand_frame, text="M9's Fitness", font=('Segoe UI', 20, 'bold'), 
                              bg=COLORS['primary'], fg='white')
        brand_label.pack(side=tk.LEFT)
        
        # Main Navigation Menu
        nav_menu_frame = tk.Frame(nav_bar, bg=COLORS['primary'])
        nav_menu_frame.pack(side=tk.LEFT, padx=30, fill=tk.X, expand=True)
        
        # Define navigation items based on user role
        self.nav_items = self.get_navigation_items()
        
        self.nav_buttons = {}
        for item in self.nav_items:
            btn = tk.Button(nav_menu_frame, text=item['text'], font=('Segoe UI', 11), 
                           bg=COLORS['primary'], fg='white', relief='flat',
                           command=item['command'], cursor='hand2', bd=0, padx=15, pady=8)
            btn.pack(side=tk.LEFT, padx=5)
            self.nav_buttons[item['key']] = btn
        
        # Set first button as active
        self.set_active_nav('dashboard')
        
        # Stats overview in navbar
        stats_frame = tk.Frame(nav_bar, bg=COLORS['primary'])
        stats_frame.pack(side=tk.RIGHT, padx=20)
        
        # Quick stats
        stats_data = [
            ("👥", self.db.get_total_members(), "Members"),
            ("💰", self.db.get_recent_payments(), "Payments"),
            ("📅", self.db.get_upcoming_appointments(), "Appointments")
        ]
        
        for icon, value, text in stats_data:
            stat_item = tk.Frame(stats_frame, bg=COLORS['primary'])
            stat_item.pack(side=tk.LEFT, padx=12)
            
            icon_label = tk.Label(stat_item, text=icon, font=('Segoe UI', 12), 
                                 bg=COLORS['primary'], fg='white')
            icon_label.pack(side=tk.LEFT)
            
            value_label = tk.Label(stat_item, text=str(value), font=('Segoe UI', 12, 'bold'), 
                                  bg=COLORS['primary'], fg=COLORS['accent'])
            value_label.pack(side=tk.LEFT, padx=(5, 2))
            
            text_label = tk.Label(stat_item, text=text, font=('Segoe UI', 9), 
                                 bg=COLORS['primary'], fg='white')
            text_label.pack(side=tk.LEFT)
    
    def get_navigation_items(self):
        """Get navigation items based on staff role"""
        base_items = [
            # Dashboard - Available to all staff
            {'key': 'dashboard', 'text': '🏠 Dashboard', 'command': self.show_dashboard, 'roles': ['admin', 'manager', 'attendant', 'trainer']},
            
            # Member Management - Admin and Manager only
            {'key': 'members', 'text': '👥 Members', 'command': self.show_member_management, 'roles': ['admin', 'manager']},
            
            # Appointments - All staff except maybe some restrictions
            {'key': 'appointments', 'text': '📅 Appointments', 'command': self.show_appointments, 'roles': ['admin', 'manager', 'attendant', 'trainer']},
            
            # Payments - Admin and Manager only
            {'key': 'payments', 'text': '💰 Payments', 'command': self.show_payments, 'roles': ['admin', 'manager']},
            
            # Workout Zones - Attendants and above
            {'key': 'workout_zones', 'text': '💪 Workout Zones', 'command': self.show_workout_zones, 'roles': ['admin', 'manager', 'attendant']},
            
            # Staff Management - Admin only
            {'key': 'staff', 'text': '👨‍💼 Staff', 'command': self.show_staff_management, 'roles': ['admin']},
            
            # Trainer Management - Admin and Manager
            {'key': 'trainers', 'text': '🏋️ Trainers', 'command': self.show_trainer_management, 'roles': ['admin', 'manager']},
            
            # Attendance - All staff
            {'key': 'attendance', 'text': '📊 Attendance', 'command': self.show_attendance, 'roles': ['admin', 'manager', 'attendant']},
            
            # Reports - Admin and Manager
            {'key': 'reports', 'text': '📈 Reports', 'command': self.show_reports, 'roles': ['admin', 'manager']},
            
            # System Settings - Admin only
            {'key': 'settings', 'text': '⚙️ System', 'command': self.show_system_settings, 'roles': ['admin']}
        ]
        
        # Filter items based on current staff role
        return [item for item in base_items if self.current_role in item['roles']]
    
    def set_active_nav(self, active_key):
        """Set active navigation button"""
        for key, btn in self.nav_buttons.items():
            if key == active_key:
                btn.configure(bg=COLORS['accent'], fg='white', font=('Segoe UI', 11, 'bold'))
            else:
                btn.configure(bg=COLORS['primary'], fg='white', font=('Segoe UI', 11))
    
    def show_notifications(self):
        messagebox.showinfo("Notifications", "You have no new notifications")
    
    def show_settings(self):
        messagebox.showinfo("Settings", "Settings panel - Implement your logic here")
    
    def show_help(self):
        messagebox.showinfo("Help", "M9 Fitness Management System Help\n\nContact support for assistance.")
    
    def show_dashboard(self):
        self.set_active_nav('dashboard')
        self.clear_content()
        
        # Dashboard container
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Dashboard title
        title_frame = tk.Frame(container, bg=COLORS['light'])
        title_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(title_frame, text="Dashboard Overview", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(anchor='w')
        
        # Quick actions row
        quick_actions_frame = tk.Frame(container, bg=COLORS['light'])
        quick_actions_frame.pack(fill=tk.X, pady=(0, 20))
        
        quick_actions = [
            ("➕ Add Member", self.show_add_member_form, COLORS['success']),
            ("📅 Schedule", self.schedule_appointment, COLORS['accent']),
            ("💳 Process Payment", self.process_payment, COLORS['warning']),
            ("📊 Generate Report", self.show_reports, COLORS['info'])
        ]
        
        for i, (text, command, color) in enumerate(quick_actions):
            btn = tk.Button(quick_actions_frame, text=text, font=('Segoe UI', 10, 'bold'),
                           bg=color, fg='white', relief='flat', padx=20, pady=10,
                           command=command, cursor='hand2')
            btn.grid(row=0, column=i, padx=10, pady=10, sticky='nsew')
            quick_actions_frame.columnconfigure(i, weight=1)
        
        # Stats cards
        stats_frame = tk.Frame(container, bg=COLORS['light'])
        stats_frame.pack(fill=tk.X, pady=(0, 30))
        
        # Get dynamic stats
        stats_data = [
            ("Total Members", self.db.get_total_members(), "#3498db", "👥"),
            ("Total Staff", self.db.get_total_staff(), "#e74c3c", "👨‍💼"),
            ("Recent Payments", self.db.get_recent_payments(), "#2ecc71", "💰"),
            ("Upcoming Appointments", self.db.get_upcoming_appointments(), "#f39c12", "📅")
        ]
        
        for i, (text, value, color, icon) in enumerate(stats_data):
            card = tk.Frame(stats_frame, bg='white', relief='raised', bd=1)
            card.grid(row=0, column=i, padx=10, pady=10, sticky='nsew')
            stats_frame.columnconfigure(i, weight=1)
            
            # Icon and value
            icon_value_frame = tk.Frame(card, bg='white')
            icon_value_frame.pack(fill=tk.X, padx=20, pady=15)
            
            icon_label = tk.Label(icon_value_frame, text=icon, font=('Segoe UI', 24), 
                                 bg='white', fg=color)
            icon_label.pack(side=tk.LEFT)
            
            value_label = tk.Label(icon_value_frame, text=str(value), font=('Segoe UI', 32, 'bold'), 
                                  bg='white', fg=color)
            value_label.pack(side=tk.RIGHT)
            
            # Stat title
            title_label = tk.Label(card, text=text, font=('Segoe UI', 12, 'bold'), 
                                  bg='white', fg=COLORS['text_light'])
            title_label.pack(fill=tk.X, padx=20, pady=(0, 15))
        
        # Charts section
        charts_container = tk.Frame(container, bg=COLORS['light'])
        charts_container.pack(fill=tk.BOTH, expand=True)
        
        # Create charts
        self.create_dashboard_charts(charts_container)
    
    def show_staff_management(self):
        self.set_active_nav('staff')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Staff Management", 
                            font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Action buttons
        action_frame = tk.Frame(header_frame, bg=COLORS['light'])
        action_frame.pack(side=tk.RIGHT)
        
        add_btn = tk.Button(action_frame, text="+ Add Staff", font=('Segoe UI', 10, 'bold'),
                        bg=COLORS['success'], fg='white', relief='flat', padx=15,
                        command=self.show_add_staff_form, cursor='hand2')
        add_btn.pack(side=tk.LEFT, padx=5)
        
        refresh_btn = tk.Button(action_frame, text="🔄 Refresh", font=('Segoe UI', 10),
                            bg=COLORS['accent'], fg='white', relief='flat', padx=15,
                            command=self.show_staff_management, cursor='hand2')
        refresh_btn.pack(side=tk.LEFT, padx=5)
        
        # Staff table
        table_frame = tk.Frame(container, bg='white', relief='raised', bd=1)
        table_frame.pack(fill=tk.BOTH, expand=True)
        
        # Create treeview with scrollbar
        tree_scroll = ttk.Scrollbar(table_frame)
        tree_scroll.pack(side=tk.RIGHT, fill=tk.Y)
        
        columns = ('ID', 'Name', 'Role', 'Branch', 'Email', 'Phone', 'Join Date')
        tree = ttk.Treeview(table_frame, columns=columns, show='headings', height=20,
                        yscrollcommand=tree_scroll.set)
        
        # Configure columns
        column_widths = [50, 150, 100, 120, 200, 120, 100]
        for col, width in zip(columns, column_widths):
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        # Get dynamic data
        staff_members = self.db.get_all_staff()
        for staff in staff_members:
            tree.insert('', tk.END, values=staff)
        
        tree.pack(fill=tk.BOTH, expand=True)
        tree_scroll.config(command=tree.yview)

    def show_trainer_management(self):
        self.set_active_nav('trainers')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Trainer Management", 
                            font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Action buttons
        action_frame = tk.Frame(header_frame, bg=COLORS['light'])
        action_frame.pack(side=tk.RIGHT)
        
        add_btn = tk.Button(action_frame, text="+ Add Trainer", font=('Segoe UI', 10, 'bold'),
                        bg=COLORS['success'], fg='white', relief='flat', padx=15,
                        command=self.show_add_trainer_form, cursor='hand2')
        add_btn.pack(side=tk.LEFT, padx=5)
        
        refresh_btn = tk.Button(action_frame, text="🔄 Refresh", font=('Segoe UI', 10),
                            bg=COLORS['accent'], fg='white', relief='flat', padx=15,
                            command=self.show_trainer_management, cursor='hand2')
        refresh_btn.pack(side=tk.LEFT, padx=5)
        
        # Trainers table
        table_frame = tk.Frame(container, bg='white', relief='raised', bd=1)
        table_frame.pack(fill=tk.BOTH, expand=True)
        
        tree_scroll = ttk.Scrollbar(table_frame)
        tree_scroll.pack(side=tk.RIGHT, fill=tk.Y)
        
        columns = ('ID', 'Name', 'Specialization', 'Hourly Rate', 'Certification')
        tree = ttk.Treeview(table_frame, columns=columns, show='headings', height=20,
                        yscrollcommand=tree_scroll.set)
        
        column_widths = [50, 150, 200, 100, 150]
        for col, width in zip(columns, column_widths):
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        # Get dynamic data
        trainers = self.db.get_trainers_with_specialization()
        for trainer in trainers:
            # Format hourly rate
            formatted_trainer = list(trainer)
            formatted_trainer[3] = f"Rs {trainer[3]:,}/hr"
            tree.insert('', tk.END, values=formatted_trainer)
        
        tree.pack(fill=tk.BOTH, expand=True)
        tree_scroll.config(command=tree.yview)
        
    def show_member_management(self):
        self.set_active_nav('members')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Member Management", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Action buttons
        action_frame = tk.Frame(header_frame, bg=COLORS['light'])
        action_frame.pack(side=tk.RIGHT)
        
        add_btn = tk.Button(action_frame, text="+ Add Member", font=('Segoe UI', 10, 'bold'),
                           bg=COLORS['success'], fg='white', relief='flat', padx=15,
                           command=self.show_add_member_form, cursor='hand2')
        add_btn.pack(side=tk.LEFT, padx=5)
        
        refresh_btn = tk.Button(action_frame, text="🔄 Refresh", font=('Segoe UI', 10),
                               bg=COLORS['accent'], fg='white', relief='flat', padx=15,
                               command=self.show_member_management, cursor='hand2')
        refresh_btn.pack(side=tk.LEFT, padx=5)
        
        # Members table
        table_frame = tk.Frame(container, bg='white', relief='raised', bd=1)
        table_frame.pack(fill=tk.BOTH, expand=True)
        
        # Create treeview with scrollbar
        tree_scroll = ttk.Scrollbar(table_frame)
        tree_scroll.pack(side=tk.RIGHT, fill=tk.Y)
        
        columns = ('ID', 'Name', 'Email', 'Phone', 'Membership Type', 'Join Date', 'Status', 'Branch')
        tree = ttk.Treeview(table_frame, columns=columns, show='headings', height=20,
                           yscrollcommand=tree_scroll.set)
        
        # Configure columns
        column_widths = [50, 150, 200, 120, 150, 100, 80, 120]
        for col, width in zip(columns, column_widths):
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        # Get dynamic data
        members = self.db.get_all_members()
        for member in members:
            tree.insert('', tk.END, values=member)
        
        tree.pack(fill=tk.BOTH, expand=True)
        tree_scroll.config(command=tree.yview)
    
    def show_appointments(self):
        self.set_active_nav('appointments')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Appointment Management", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Action buttons
        action_frame = tk.Frame(header_frame, bg=COLORS['light'])
        action_frame.pack(side=tk.RIGHT)
        
        buttons = [
            ("+ Schedule", self.schedule_appointment),
            ("📅 Calendar", self.view_calendar),
            ("🔄 Refresh", self.show_appointments)
        ]
        
        for text, command in buttons:
            btn = tk.Button(action_frame, text=text, font=('Segoe UI', 10),
                           bg=COLORS['accent'], fg='white', relief='flat', padx=15,
                           command=command, cursor='hand2')
            btn.pack(side=tk.LEFT, padx=5)
        
        # Appointments table
        table_frame = tk.Frame(container, bg='white', relief='raised', bd=1)
        table_frame.pack(fill=tk.BOTH, expand=True)
        
        tree_scroll = ttk.Scrollbar(table_frame)
        tree_scroll.pack(side=tk.RIGHT, fill=tk.Y)
        
        columns = ('ID', 'Member', 'Trainer', 'Type', 'Date', 'Time', 'Status')
        tree = ttk.Treeview(table_frame, columns=columns, show='headings', height=20,
                           yscrollcommand=tree_scroll.set)
        
        column_widths = [50, 150, 120, 150, 100, 120, 100]
        for col, width in zip(columns, column_widths):
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        # Get dynamic data
        appointments = self.db.get_all_appointments()
        for appointment in appointments:
            tree.insert('', tk.END, values=appointment)
        
        tree.pack(fill=tk.BOTH, expand=True)
        tree_scroll.config(command=tree.yview)
    
    def show_payments(self):
        self.set_active_nav('payments')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Payment Management", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Action buttons
        action_frame = tk.Frame(header_frame, bg=COLORS['light'])
        action_frame.pack(side=tk.RIGHT)
        
        buttons = [
            ("💳 Process Payment", self.process_payment),
            ("📋 View Plans", self.view_subscription_plans),
            ("🔄 Refresh", self.show_payments)
        ]
        
        for text, command in buttons:
            btn = tk.Button(action_frame, text=text, font=('Segoe UI', 10),
                           bg=COLORS['accent'], fg='white', relief='flat', padx=15,
                           command=command, cursor='hand2')
            btn.pack(side=tk.LEFT, padx=5)
        
        # Payments table
        table_frame = tk.Frame(container, bg='white', relief='raised', bd=1)
        table_frame.pack(fill=tk.BOTH, expand=True)
        
        tree_scroll = ttk.Scrollbar(table_frame)
        tree_scroll.pack(side=tk.RIGHT, fill=tk.Y)
        
        columns = ('ID', 'Member', 'Amount', 'Date', 'Method', 'Type', 'Status')
        tree = ttk.Treeview(table_frame, columns=columns, show='headings', height=20,
                           yscrollcommand=tree_scroll.set)
        
        column_widths = [50, 150, 80, 100, 100, 150, 100]
        for col, width in zip(columns, column_widths):
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        # Get dynamic data
        payments = self.db.get_all_payments()
        for payment in payments:
            # Format amount with currency
            formatted_payment = list(payment)
            formatted_payment[2] = f"Rs {payment[2]:,}"
            tree.insert('', tk.END, values=formatted_payment)
        
        tree.pack(fill=tk.BOTH, expand=True)
        tree_scroll.config(command=tree.yview)
    
    def show_workout_zones(self):
        self.set_active_nav('workout_zones')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        title_label = tk.Label(container, text="Workout Zones Management", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(anchor='w', pady=(0, 20))
        
        # Implement workout zones management here
        messagebox.showinfo("Info", "Workout Zones Management - Implement your logic here")
    
    
    def show_staff_management(self):
        self.set_active_nav('staff')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        title_label = tk.Label(container, text="Staff Management", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(anchor='w', pady=(0, 20))
        
        # Implement staff management here
        messagebox.showinfo("Info", "Staff Management - Implement your logic here")
    
    def show_attendance(self):
        self.set_active_nav('attendance')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        title_label = tk.Label(container, text="Attendance Tracking", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(anchor='w', pady=(0, 20))
        
        # Implement attendance tracking here
        messagebox.showinfo("Info", "Attendance Tracking - Implement your logic here")
    
    def show_system_settings(self):
        self.set_active_nav('settings')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        title_label = tk.Label(container, text="System Settings", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(anchor='w', pady=(0, 20))
        
        # Implement system settings here
        messagebox.showinfo("Info", "System Settings - Implement your logic here")
    
    def show_reports(self):
        self.set_active_nav('reports')
        self.clear_content()
        
        container = tk.Frame(self.main_content, bg=COLORS['light'], padx=20, pady=20)
        container.pack(fill=tk.BOTH, expand=True)
        
        # Header
        header_frame = tk.Frame(container, bg=COLORS['light'])
        header_frame.pack(fill=tk.X, pady=(0, 20))
        
        title_label = tk.Label(header_frame, text="Reports & Analytics", 
                              font=('Segoe UI', 24, 'bold'), bg=COLORS['light'], fg=COLORS['primary'])
        title_label.pack(side=tk.LEFT)
        
        # Report controls
        controls_frame = tk.Frame(header_frame, bg=COLORS['light'])
        controls_frame.pack(side=tk.RIGHT)
        
        reports = [
            ("📈 Membership Growth", self.membership_growth_report),
            ("💰 Revenue Analysis", self.revenue_analysis_report),
            ("👥 Attendance Report", self.attendance_report),
            ("⏰ Peak Hours", self.peak_hours_report)
        ]
        
        for text, command in reports:
            btn = tk.Button(controls_frame, text=text, font=('Segoe UI', 10),
                           bg=COLORS['accent'], fg='white', relief='flat', padx=10,
                           command=command, cursor='hand2')
            btn.pack(side=tk.LEFT, padx=5)
        
        # Default report
        self.membership_growth_report()
    
    # ... (Keep all the existing report methods and other methods from previous version)
    
    def create_dashboard_charts(self, parent):
        # Left chart frame
        left_frame = tk.Frame(parent, bg=COLORS['light'])
        left_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(0, 10))
        
        # Right chart frame
        right_frame = tk.Frame(parent, bg=COLORS['light'])
        right_frame.pack(side=tk.RIGHT, fill=tk.BOTH, expand=True, padx=(10, 0))
        
        # Membership distribution (left)
        membership_data = self.db.get_membership_distribution()
        if membership_data:
            types = [data[0] for data in membership_data]
            counts = [data[1] for data in membership_data]
            
            fig1, ax1 = plt.subplots(figsize=(6, 4))
            colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c']
            ax1.pie(counts, labels=types, autopct='%1.1f%%', startangle=90, colors=colors)
            ax1.set_title('Membership Type Distribution', fontsize=14, fontweight='bold', pad=20)
            
            chart1_frame = tk.Frame(left_frame, bg='white', relief='raised', bd=1)
            chart1_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 10))
            
            canvas1 = FigureCanvasTkAgg(fig1, chart1_frame)
            canvas1.draw()
            canvas1.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # City distribution (right)
        city_data = self.db.get_city_member_distribution()
        if city_data:
            cities = [data[0] for data in city_data]
            members = [data[1] for data in city_data]
            
            fig2, ax2 = plt.subplots(figsize=(6, 4))
            bars = ax2.bar(cities, members, color=['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'])
            ax2.set_title('Members by City', fontsize=14, fontweight='bold', pad=20)
            ax2.set_ylabel('Number of Members')
            plt.setp(ax2.get_xticklabels(), rotation=45, ha='right')
            
            # Add value labels on bars
            for bar, value in zip(bars, members):
                ax2.text(bar.get_x() + bar.get_width()/2, bar.get_height() + 0.1, 
                        str(value), ha='center', va='bottom', fontweight='bold')
            
            chart2_frame = tk.Frame(right_frame, bg='white', relief='raised', bd=1)
            chart2_frame.pack(fill=tk.BOTH, expand=True, pady=(0, 10))
            
            canvas2 = FigureCanvasTkAgg(fig2, chart2_frame)
            canvas2.draw()
            canvas2.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
    
    def show_add_member_form(self):
        # Implement add member form
        messagebox.showinfo("Info", "Add Member Form - Implement your logic here")
    
    def schedule_appointment(self):
        messagebox.showinfo("Info", "Schedule Appointment - Implement your logic here")
    
    def view_calendar(self):
        messagebox.showinfo("Info", "Calendar View - Implement your logic here")
    
    def process_payment(self):
        messagebox.showinfo("Info", "Process Payment - Implement your logic here")
    
    def view_subscription_plans(self):
        messagebox.showinfo("Info", "View Subscription Plans - Implement your logic here")
    
    def membership_growth_report(self):
        self.clear_chart_area()
        
        chart_frame = tk.Frame(self.main_content, bg='white', relief='raised', bd=1)
        chart_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        
        growth_data = self.db.get_membership_growth()
        
        if growth_data:
            months = [data[0] for data in growth_data]
            new_members = [data[1] for data in growth_data]
            total_members = [data[2] for data in growth_data]
            
            fig, ax = plt.subplots(figsize=(12, 6))
            
            # Plot line for total members and bars for new members
            ax.plot(months, total_members, marker='o', linewidth=3, label='Total Members', color=COLORS['accent'])
            bars = ax.bar(months, new_members, alpha=0.7, label='New Members (Monthly)', color=COLORS['success'])
            
            # Add value labels on bars
            for bar, value in zip(bars, new_members):
                ax.text(bar.get_x() + bar.get_width()/2, bar.get_height() + 0.1, 
                        str(value), ha='center', va='bottom', fontweight='bold')
            
            ax.set_title('Membership Growth Report', fontsize=16, fontweight='bold', pad=20)
            ax.set_xlabel('Months')
            ax.set_ylabel('Number of Members')
            ax.legend()
            ax.grid(True, alpha=0.3)
            
            plt.tight_layout()
            
            canvas = FigureCanvasTkAgg(fig, chart_frame)
            canvas.draw()
            canvas.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        else:
            no_data_label = tk.Label(chart_frame, text="No data available for membership growth report", 
                                   font=('Segoe UI', 12), bg='white', fg=COLORS['text_light'])
            no_data_label.pack(expand=True)
    
    def revenue_analysis_report(self):
        self.clear_chart_area()
        
        chart_frame = tk.Frame(self.main_content, bg='white', relief='raised', bd=1)
        chart_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        
        monthly_revenue, revenue_by_type = self.db.get_revenue_analysis()
        
        if monthly_revenue or revenue_by_type:
            fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(14, 6))
            
            # Monthly revenue
            if monthly_revenue:
                months = [data[0] for data in monthly_revenue]
                revenue = [data[1] for data in monthly_revenue]
                
                ax1.plot(months, revenue, marker='o', linewidth=3, color=COLORS['success'])
                ax1.set_title('Monthly Revenue', fontsize=14, fontweight='bold')
                ax1.set_xlabel('Months')
                ax1.set_ylabel('Revenue (PKR)')
                ax1.grid(True, alpha=0.3)
                
                # Format y-axis with commas
                ax1.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'Rs {x:,.0f}'))
            else:
                ax1.text(0.5, 0.5, 'No revenue data', ha='center', va='center', transform=ax1.transAxes)
            
            # Revenue by subscription type
            if revenue_by_type:
                types = [data[0] for data in revenue_by_type]
                revenue_share = [data[1] for data in revenue_by_type]
                colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6']
                
                ax2.pie(revenue_share, labels=types, autopct='%1.1f%%', startangle=90, colors=colors)
                ax2.set_title('Revenue by Subscription Type', fontsize=14, fontweight='bold')
            else:
                ax2.text(0.5, 0.5, 'No subscription data', ha='center', va='center', transform=ax2.transAxes)
            
            plt.tight_layout()
            
            canvas = FigureCanvasTkAgg(fig, chart_frame)
            canvas.draw()
            canvas.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        else:
            no_data_label = tk.Label(chart_frame, text="No data available for revenue analysis", 
                                   font=('Segoe UI', 12), bg='white', fg=COLORS['text_light'])
            no_data_label.pack(expand=True)
    
    def attendance_report(self):
        self.clear_chart_area()
        
        chart_frame = tk.Frame(self.main_content, bg='white', relief='raised', bd=1)
        chart_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        
        attendance_data = self.db.get_attendance_pattern()
        
        if attendance_data:
            days = [data[0] for data in attendance_data]
            visits = [data[1] for data in attendance_data]
            
            fig, ax = plt.subplots(figsize=(10, 6))
            
            bars = ax.bar(days, visits, color=COLORS['accent'], edgecolor='navy', alpha=0.8)
            ax.set_title('Weekly Attendance Pattern', fontsize=16, fontweight='bold', pad=20)
            ax.set_xlabel('Days')
            ax.set_ylabel('Number of Visits')
            ax.grid(True, alpha=0.3)
            
            # Add value labels on bars
            for bar, value in zip(bars, visits):
                ax.text(bar.get_x() + bar.get_width()/2, bar.get_height() + 0.1, 
                        str(value), ha='center', va='bottom', fontweight='bold')
            
            plt.tight_layout()
            
            canvas = FigureCanvasTkAgg(fig, chart_frame)
            canvas.draw()
            canvas.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        else:
            no_data_label = tk.Label(chart_frame, text="No data available for attendance report", 
                                   font=('Segoe UI', 12), bg='white', fg=COLORS['text_light'])
            no_data_label.pack(expand=True)
    
    def peak_hours_report(self):
        self.clear_chart_area()
        
        chart_frame = tk.Frame(self.main_content, bg='white', relief='raised', bd=1)
        chart_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        
        peak_data = self.db.get_peak_hours()
        
        if peak_data:
            hours = [f"{int(data[0])}:00" for data in peak_data]
            visitors = [data[1] for data in peak_data]
            
            fig, ax = plt.subplots(figsize=(12, 6))
            
            ax.plot(hours, visitors, marker='o', linewidth=3, color=COLORS['danger'])
            ax.set_title('Peak Hours Analysis', fontsize=16, fontweight='bold', pad=20)
            ax.set_xlabel('Time of Day')
            ax.set_ylabel('Number of Visitors')
            ax.tick_params(axis='x', rotation=45)
            ax.grid(True, alpha=0.3)
            
            # Highlight peak hours
            peak_index = visitors.index(max(visitors))
            ax.axvline(x=hours[peak_index], color=COLORS['warning'], linestyle='--', alpha=0.7, linewidth=2)
            ax.text(hours[peak_index], max(visitors), 'Peak Hour', ha='center', va='bottom', 
                   fontweight='bold', fontsize=12, color=COLORS['warning'])
            
            plt.tight_layout()
            
            canvas = FigureCanvasTkAgg(fig, chart_frame)
            canvas.draw()
            canvas.get_tk_widget().pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        else:
            no_data_label = tk.Label(chart_frame, text="No data available for peak hours analysis", 
                                   font=('Segoe UI', 12), bg='white', fg=COLORS['text_light'])
            no_data_label.pack(expand=True)
    
    def clear_content(self):
        for widget in self.main_content.winfo_children():
            widget.destroy()
    
    def clear_chart_area(self):
        for widget in self.main_content.winfo_children():
            if isinstance(widget, tk.Frame):
                widget.destroy()

if __name__ == "__main__":
    root = tk.Tk()
    app = ProfessionalGymManagementSystem(root)
    root.mainloop()