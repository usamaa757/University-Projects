import tkinter as tk
from tkinter import ttk, messagebox
import hashlib
from database import Database


class LoginWindow:
    def __init__(self, root, on_login_success):
        self.root = root
        self.root.title("M9 Fitness - Login")
        self.root.geometry("400x500")
        self.root.configure(bg="#2c3e50")

        self.on_login_success = on_login_success
        self.db = Database()  # connect to your DB

        self.center_window(400, 500)
        self.create_widgets()

    def center_window(self, width, height):
        screen_width = self.root.winfo_screenwidth()
        screen_height = self.root.winfo_screenheight()
        x = (screen_width - width) // 2
        y = (screen_height - height) // 2
        self.root.geometry(f"{width}x{height}+{x}+{y}")

    def create_widgets(self):
        header_frame = tk.Frame(self.root, bg="#34495e", height=100)
        header_frame.pack(fill="x")
        header_frame.pack_propagate(False)

        tk.Label(
            header_frame,
            text="M9 Fitness",
            font=("Arial", 24, "bold"),
            fg="white",
            bg="#34495e",
        ).pack(expand=True)
        tk.Label(
            header_frame,
            text="Smart Gym Management System",
            font=("Arial", 12),
            fg="#ecf0f1",
            bg="#34495e",
        ).pack(pady=(0, 10))

        main_frame = tk.Frame(self.root, bg="#2c3e50", padx=40, pady=30)
        main_frame.pack(expand=True, fill="both")
        form_frame = tk.Frame(main_frame, bg="#2c3e50")
        form_frame.pack(expand=True)

        # Email
        tk.Label(
            form_frame, text="Email:", font=("Arial", 11), bg="#2c3e50", fg="white"
        ).grid(row=0, column=0, sticky="w", pady=(0, 5))
        self.username_entry = ttk.Entry(form_frame, font=("Arial", 11), width=30)
        self.username_entry.grid(row=1, column=0, pady=(0, 20))

        # Password
        tk.Label(
            form_frame, text="Password:", font=("Arial", 11), bg="#2c3e50", fg="white"
        ).grid(row=2, column=0, sticky="w", pady=(0, 5))
        self.password_entry = ttk.Entry(
            form_frame, font=("Arial", 11), width=30, show="*"
        )
        self.password_entry.grid(row=3, column=0, pady=(0, 30))

        # # Role selection
        # tk.Label(
        #     form_frame, text="Login As:", font=("Arial", 11), bg="#2c3e50", fg="white"
        # ).grid(row=4, column=0, sticky="w", pady=(0, 5))
        # self.role_var = tk.StringVar(value="Manager")
        # ttk.Combobox(
        #     form_frame,
        #     textvariable=self.role_var,
        #     values=["Manager", "Trainer", "Attendant", "Nutritionist"],
        #     state="readonly",
        #     width=27,
        #     font=("Arial", 11),
        # ).grid(row=5, column=0, pady=(0, 30))

        # Login button
        tk.Button(
            form_frame,
            text="Login",
            font=("Arial", 12, "bold"),
            bg="#3498db",
            fg="white",
            padx=30,
            pady=5,
            command=self.authenticate,
            cursor="hand2",
        ).grid(row=6, column=0)

        footer_frame = tk.Frame(self.root, bg="#34495e", height=50)
        footer_frame.pack(fill="x", side="bottom")
        footer_frame.pack_propagate(False)
        tk.Label(
            footer_frame,
            text="© 2024 M9 Fitness. All rights reserved.",
            font=("Arial", 9),
            fg="#bdc3c7",
            bg="#34495e",
        ).pack(expand=True)

    # ==================== HASH HELPER ====================
    def hash_password(self, password):
        return hashlib.sha256(password.encode()).hexdigest()

    # ==================== AUTHENTICATE ====================
    def authenticate(self):
        email = self.username_entry.get().strip()
        password = self.password_entry.get().strip()

        if not email or not password:
            messagebox.showwarning("Input Error", "Please enter email and password")
            return

        hashed_pw = self.hash_password(password)
        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Check staff table
        cursor.execute(
            """
            SELECT *
            FROM staff
            WHERE email=? AND password_hash=?
        """,
            (email, hashed_pw),
        )
        user = cursor.fetchone()
        if user:
            conn.close()
            self.on_login_success(
                {
                    "id": user[0],
                    "first_name": user[1],
                    "role": user[3],
                    "branch_id": user[4],
                    "zone_id": user[12],
                }
            )
            return

        conn.close()
        messagebox.showerror("Login Failed", "Invalid email, password, or role")
