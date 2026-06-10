import tkinter as tk
from tkinter import ttk, messagebox
from database import Database
from styles import Styles

class Authentication:
    def __init__(self, root, on_login_success):
        self.root = root
        self.on_login_success = on_login_success
        self.db = Database()
        self.styles = Styles()
        self.setup_ui()
    
    def setup_ui(self):
        # Main frame
        self.main_frame = tk.Frame(self.root, bg=self.styles.LIGHT)
        self.main_frame.pack(fill=tk.BOTH, expand=True)
        
        # Center container
        container = tk.Frame(self.main_frame, bg=self.styles.LIGHT)
        container.place(relx=0.5, rely=0.5, anchor=tk.CENTER)
        
        # Title
        title_label = tk.Label(
            container,
            text="Clothes Management System",
            font=self.styles.TITLE_FONT,
            fg=self.styles.PRIMARY,
            bg=self.styles.LIGHT
        )
        title_label.pack(pady=(0, 30))
        
        # Login Frame
        login_frame = tk.Frame(container, bg="white", bd=1, relief="solid", padx=40, pady=30)
        login_frame.pack()
        
        # Username
        tk.Label(
            login_frame,
            text="Username:",
            font=self.styles.NORMAL_FONT,
            bg="white"
        ).grid(row=0, column=0, padx=10, pady=10, sticky=tk.W)
        
        self.username_entry = tk.Entry(
            login_frame,
            width=25,
            **self.styles.ENTRY_STYLE
        )
        self.username_entry.grid(row=0, column=1, padx=10, pady=10)
        self.username_entry.focus()
                
        # Password
        tk.Label(
            login_frame,
            text="Password:",
            font=self.styles.NORMAL_FONT,
            bg="white"
        ).grid(row=1, column=0, padx=10, pady=10, sticky=tk.W)
        
        self.password_entry = tk.Entry(
            login_frame,
            width=25,
            show="*",
            **self.styles.ENTRY_STYLE
        )
        self.password_entry.grid(row=1, column=1, padx=10, pady=10)

        
        # Login Button
        login_btn = tk.Button(
            login_frame,
            text="Login",
            command=self.login,
            **self.styles.BTN_PRIMARY
        )
        login_btn.grid(row=2, column=0, columnspan=2, pady=20)
        
        # Bind Enter key
        self.root.bind('<Return>', lambda e: self.login())
    
    def login(self):
        username = self.username_entry.get().strip()
        password = self.password_entry.get().strip()
        
        if not username or not password:
            messagebox.showwarning("Warning", "Please enter both username and password")
            return
        
        user = self.db.fetch_one(
            "SELECT username, user_type FROM users WHERE username = ? AND password = ?",
            (username, password)
        )
        
        if user:
            self.main_frame.destroy()
            self.on_login_success(user[0], user[1])
        else:
            messagebox.showerror("Error", "Invalid username or password")