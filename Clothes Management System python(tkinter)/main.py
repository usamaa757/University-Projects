import tkinter as tk
from tkinter import ttk, messagebox
from auth import Authentication
from admin import AdminPanel
from sale_purchase import SalePurchase
from reports import Reports
from styles import Styles
import sys

class ClothesManagementSystem:
    def __init__(self, root):
        self.root = root
        self.root.title("Clothes Management System")

        # Open maximized (Windows)
        self.root.state('zoomed')

        self.root.configure(bg="#ECF0F1")

        # Center window on screen
        self.center_window()
        
        self.styles = Styles()
        self.current_user = None
        self.user_type = None
        
        # Setup styles
        self.setup_styles()
        
        # Start with authentication
        self.show_auth()
    
    def center_window(self):
        self.root.update_idletasks()
        width = self.root.winfo_width()
        height = self.root.winfo_height()
        x = (self.root.winfo_screenwidth() // 2) - (width // 2)
        y = (self.root.winfo_screenheight() // 2) - (height // 2)
        self.root.geometry(f'{width}x{height}+{x}+{y}')
    
    def setup_styles(self):
        style = ttk.Style()
        style.theme_use('clam')
        
        # Configure custom styles
        style.configure('Title.TLabel', font=self.styles.TITLE_FONT, foreground=self.styles.PRIMARY)
        style.configure('Heading.TLabel', font=self.styles.HEADING_FONT, foreground=self.styles.PRIMARY)
        
        style.configure('Primary.TButton', background=self.styles.ACCENT, foreground='white')
        style.map('Primary.TButton',
                 background=[('active', self.styles.ACCENT)],
                 foreground=[('active', 'white')])
    
    def show_auth(self):
        # Clear any existing widgets
        for widget in self.root.winfo_children():
            widget.destroy()
        
        # Show authentication screen
        self.auth = Authentication(self.root, self.on_login_success)
    
    def on_login_success(self, username, user_type):
        self.current_user = username
        self.user_type = user_type
        self.show_main_menu()
    
    def show_main_menu(self):
        # Clear any existing widgets
        for widget in self.root.winfo_children():
            widget.destroy()
        
        # Setup main menu
        self.setup_menu_bar()
        
        # Welcome label
        welcome_frame = tk.Frame(self.root, bg=self.styles.LIGHT)
        welcome_frame.pack(fill=tk.X, pady=10)
        
        welcome_label = tk.Label(
            welcome_frame,
            text=f"Welcome, {self.current_user} ({self.user_type})",
            font=self.styles.HEADING_FONT,
            fg=self.styles.PRIMARY,
            bg=self.styles.LIGHT
        )
        welcome_label.pack()
        
        # Dashboard frame
        dashboard_frame = tk.Frame(self.root, bg=self.styles.LIGHT)
        dashboard_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        
        # Statistics
        stats_frame = tk.LabelFrame(dashboard_frame, text="Quick Statistics",
                                   font=self.styles.HEADING_FONT,
                                   bg=self.styles.LIGHT,
                                   padx=20, pady=20)
        stats_frame.pack(fill=tk.X, pady=10)
        
        # Calculate statistics
        self.show_statistics(stats_frame)
        
        # Quick actions
        actions_frame = tk.LabelFrame(dashboard_frame, text="Quick Actions",
                                     font=self.styles.HEADING_FONT,
                                     bg=self.styles.LIGHT,
                                     padx=20, pady=20)
        actions_frame.pack(fill=tk.BOTH, expand=True, pady=10)
        
        self.show_quick_actions(actions_frame)
    
    def setup_menu_bar(self):
        menubar = tk.Menu(self.root)
        self.root.config(menu=menubar)
        
        # File menu
        file_menu = tk.Menu(menubar, tearoff=0)
        menubar.add_cascade(label="File", menu=file_menu)
        file_menu.add_command(label="Dashboard", command=self.show_main_menu)
        file_menu.add_separator()
        file_menu.add_command(label="Logout", command=self.logout)
        file_menu.add_command(label="Exit", command=self.root.quit)
        
        # Operations menu (for all users)
        ops_menu = tk.Menu(menubar, tearoff=0)
        menubar.add_cascade(label="Operations", menu=ops_menu)
        ops_menu.add_command(label="Sales & Purchases", command=self.show_sale_purchase)
        ops_menu.add_command(label="Item Management", command=self.setup_view_tab)

        # Admin menu (only for admin users)
        if self.user_type == "admin":
            admin_menu = tk.Menu(menubar, tearoff=0)
            menubar.add_cascade(label="Admin", menu=admin_menu)
            admin_menu.add_command(label="User Management", command=self.show_admin_panel)
            admin_menu.add_command(label="Item Management", command=self.show_admin_panel)
        
        # Reports menu
        reports_menu = tk.Menu(menubar, tearoff=0)
        menubar.add_cascade(label="Reports", menu=reports_menu)
        reports_menu.add_command(label="Generate Reports", command=self.show_reports)
        
        # Help menu
        help_menu = tk.Menu(menubar, tearoff=0)
        menubar.add_cascade(label="Help", menu=help_menu)
        help_menu.add_command(label="About", command=self.show_about)
    
    def show_statistics(self, parent):
        from database import Database
        db = Database()
        
        # Get statistics
        total_items = db.fetch_one("SELECT COUNT(*) FROM clothing_items")[0] or 0
        total_stock = db.fetch_one("SELECT SUM(stock) FROM clothing_items")[0] or 0
        low_stock = db.fetch_one("SELECT COUNT(*) FROM clothing_items WHERE stock < 10")[0] or 0
        
        today = tk.StringVar()
        today_sales = db.fetch_one("SELECT SUM(total_amount) FROM sales WHERE sale_date = DATE('now')")
        today.set(f"${today_sales[0]:.2f}" if today_sales and today_sales[0] else "$0.00")
        
        db.close()
        
        # Display statistics
        stats = [
            ("Total Items", str(total_items)),
            ("Total Stock", str(total_stock)),
            ("Low Stock Items", str(low_stock)),
            ("Today's Sales", today.get())
        ]
        
        for i, (label, value) in enumerate(stats):
            frame = tk.Frame(parent, bg="white", bd=1, relief="solid", padx=20, pady=10)
            frame.grid(row=i//2, column=i%2, padx=10, pady=10, sticky="nsew")
            
            tk.Label(frame, text=label, font=self.styles.SMALL_FONT, 
                    bg="white", fg=self.styles.SECONDARY).pack()
            tk.Label(frame, text=value, font=("Helvetica", 18, "bold"), 
                    bg="white", fg=self.styles.PRIMARY).pack()
        
        # Configure grid weights
        parent.grid_columnconfigure(0, weight=1)
        parent.grid_columnconfigure(1, weight=1)
    
    def show_quick_actions(self, parent):
        actions = []
        
        if self.user_type == "admin":
            actions = [
                ("Manage Users", self.show_admin_panel, self.styles.ACCENT),
                ("Manage Items", self.show_admin_panel, self.styles.SECONDARY),
                ("Make Sale", self.show_sale_purchase, self.styles.SUCCESS),
                ("Make Purchase", self.show_sale_purchase, self.styles.WARNING),
                ("Generate Reports", self.show_reports, self.styles.PRIMARY),
                ("View Inventory", self.show_inventory, self.styles.ACCENT)
            ]
        else:
            actions = [
                ("Make Sale", self.show_sale_purchase, self.styles.SUCCESS),
                ("Make Purchase", self.show_sale_purchase, self.styles.WARNING),
                ("View Reports", self.show_reports, self.styles.PRIMARY),
                ("Transaction History", self.show_sale_purchase, self.styles.ACCENT)
            ]
        
        for i, (text, command, color) in enumerate(actions):
            btn = tk.Button(
                parent,
                text=text,
                command=command,
                bg=color,
                fg="white",
                font=self.styles.NORMAL_FONT,
                bd=0,
                padx=20,
                pady=15,
                cursor="hand2",
                activebackground=color
            )
            btn.grid(row=i//3, column=i%3, padx=10, pady=10, sticky="nsew")
        
        # Configure grid weights
        for i in range(3):
            parent.grid_columnconfigure(i, weight=1)
        parent.grid_rowconfigure(0, weight=1)
        if len(actions) > 3:
            parent.grid_rowconfigure(1, weight=1)
    
    def show_admin_panel(self):
        self.clear_main_area()
        AdminPanel(self.root, self.current_user)
    
    def show_sale_purchase(self):
        self.clear_main_area()
        SalePurchase(self.root, self.current_user, self.user_type)
    
    def show_reports(self):
        self.clear_main_area()
        Reports(self.root, self.current_user)
    
    def show_inventory(self):
        # For non-admin users, show read-only inventory view
        self.clear_main_area()
        
        from database import Database
        db = Database()
        
        container = tk.Frame(self.root, bg=self.styles.LIGHT)
        container.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        title = tk.Label(container, text="Inventory View", 
                        font=self.styles.TITLE_FONT,
                        fg=self.styles.PRIMARY,
                        bg=self.styles.LIGHT)
        title.pack(pady=10)
        
        # Treeview for items
        columns = ("ID", "Name", "Target Group", "Season", "Stock", "Price")
        tree = ttk.Treeview(container, columns=columns, show="headings", height=20)
        
        for col in columns:
            tree.heading(col, text=col)
            tree.column(col, width=100)
        
        # Get items
        items = db.fetch_all("""
            SELECT id, name, target_group, season, stock, sale_price
            FROM clothing_items
            ORDER BY target_group, name
        """)
        
        for item in items:
            tree.insert("", tk.END, values=item)
        
        scrollbar = ttk.Scrollbar(container, orient=tk.VERTICAL, command=tree.yview)
        tree.configure(yscrollcommand=scrollbar.set)
        
        tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        db.close()
    
    def clear_main_area(self):
        # Clear everything except menu
        for widget in self.root.winfo_children():
            if not isinstance(widget, tk.Menu):
                widget.destroy()
    
    def logout(self):
        self.current_user = None
        self.user_type = None
        self.show_auth()
    
    def show_about(self):
        about_text = """Clothes Management System
Version 1.0

A comprehensive desktop application for managing 
clothing inventory, sales, purchases, and reports.

Features:
• User authentication and role-based access
• Clothing item management
• Sales and purchase tracking
• Automated stock updates
• Comprehensive reporting
• Profit calculation

Developed with Python and Tkinter
"""
        messagebox.showinfo("About", about_text)

def main():
    root = tk.Tk()
    app = ClothesManagementSystem(root)
    root.mainloop()

if __name__ == "__main__":
    main()