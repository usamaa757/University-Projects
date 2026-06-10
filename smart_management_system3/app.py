import tkinter as tk
from tkinter import messagebox
import sqlite3
import csv
import os

# ---------- Database Setup ----------
conn = sqlite3.connect("gym.db")
cursor = conn.cursor()
cursor.execute("""
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT,
    fullname TEXT,
    email TEXT,
    role TEXT
)
""")
conn.commit()

CSV_FILE = "users.csv"
if not os.path.exists(CSV_FILE):
    with open(CSV_FILE, "w", newline="") as f:
        writer = csv.writer(f)
        writer.writerow(["username", "password", "role"])  # header

# ---------- Tkinter App ----------
root = tk.Tk()
root.title("Smart Gym Management System")
root.geometry("450x500")

# ---------- Functions ----------
def save_to_csv(username, password, role):
    with open(CSV_FILE, "a", newline="") as f:
        writer = csv.writer(f)
        writer.writerow([username, password, role])

def register_user():
    username = reg_username.get()
    password = reg_password.get()
    fullname = reg_fullname.get()
    email = reg_email.get()
    role = reg_role.get()

    if not (username and password and fullname and email and role):
        messagebox.showerror("Error", "All fields required!")
        return

    try:
        cursor.execute("INSERT INTO users (username, password, fullname, email, role) VALUES (?, ?, ?, ?, ?)",
                       (username, password, fullname, email, role))
        conn.commit()
        save_to_csv(username, password, role)
        messagebox.showinfo("Success", f"{role.capitalize()} Registration Successful!")
        show_login_page()  # back to login
    except sqlite3.IntegrityError:
        messagebox.showerror("Error", "Username already exists!")

def login_user():
    username = login_username.get()
    password = login_password.get()

    # Hardcoded admin
    if username == "admin" and password == "admin123":
        messagebox.showinfo("Admin Login", "Welcome Admin!")
        open_admin_panel()
        return

    # Check from database instead of CSV
    cursor.execute("SELECT role FROM users WHERE username=? AND password=?", (username, password))
    result = cursor.fetchone()
    
    if result:
        role = result[0]
        messagebox.showinfo("Login Success", f"Welcome {role} {username}!")
        # You can add role-specific dashboards here if needed
    else:
        messagebox.showerror("Error", "Invalid credentials!")

def open_admin_panel():
    admin_window = tk.Toplevel(root)
    admin_window.title("Admin Panel")
    admin_window.geometry("400x400")

    tk.Label(admin_window, text="Admin Panel", font=("Arial", 16, "bold")).pack(pady=10)

    # Function to show role-specific users
    def show_users_by_role(role):
        view_window = tk.Toplevel(admin_window)
        view_window.title(f"{role} List")
        view_window.geometry("500x400")

        tk.Label(view_window, text=f"{role}s", font=("Arial", 14, "bold")).pack(pady=5)
        
        # Create a frame with a scrollbar for the user list
        frame = tk.Frame(view_window)
        frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Add a scrollbar
        scrollbar = tk.Scrollbar(frame)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        # Create a listbox to display users
        listbox = tk.Listbox(frame, yscrollcommand=scrollbar.set, width=70)
        listbox.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.config(command=listbox.yview)

        cursor.execute("SELECT fullname, email, username FROM users WHERE role=?", (role,))
        records = cursor.fetchall()

        if not records:
            listbox.insert(tk.END, f"No {role}s found.")
        else:
            listbox.insert(tk.END, "Full Name                 Email                            Username")
            listbox.insert(tk.END, "-" * 70)
            for rec in records:
                fullname, email, username = rec
                # Format the output for better readability
                listbox.insert(tk.END, f"{fullname.ljust(20)} {email.ljust(30)} {username}")

    # Buttons for Manager & Trainer
    tk.Button(admin_window, text="Show Managers", command=lambda: show_users_by_role("Manager"),
              bg="blue", fg="white", width=20).pack(pady=5)

    tk.Button(admin_window, text="Show Trainers", command=lambda: show_users_by_role("Trainer"),
              bg="green", fg="white", width=20).pack(pady=5)
              
    tk.Button(admin_window, text="Show Members", command=lambda: show_users_by_role("Member"),
              bg="orange", fg="white", width=20).pack(pady=5)

    # Logout button
    def logout():
        login_username.delete(0, tk.END)
        login_password.delete(0, tk.END)
        messagebox.showinfo("Logout", "You have been logged out.")
        admin_window.destroy()

    tk.Button(admin_window, text="Logout", command=logout, bg="red", fg="white").pack(pady=20)


# ---------- Navigation ----------
def show_login_page():
    register_frame.pack_forget()
    login_frame.pack(fill="both", expand=True)

def show_register_page():
    login_frame.pack_forget()
    register_frame.pack(fill="both", expand=True)

# ---------- Frames ----------
login_frame = tk.Frame(root)
register_frame = tk.Frame(root)

# ---------- Login Page ----------
tk.Label(login_frame, text="Login Page", font=("Arial", 16, "bold")).pack(pady=10)

tk.Label(login_frame, text="Username").pack()
login_username = tk.Entry(login_frame)
login_username.pack()

tk.Label(login_frame, text="Password").pack()
login_password = tk.Entry(login_frame, show="*")
login_password.pack()

tk.Button(login_frame, text="Login", command=login_user).pack(pady=10)
tk.Button(login_frame, text="Register", command=show_register_page).pack()

# ---------- Register Page ----------
tk.Label(register_frame, text="Register User", font=("Arial", 16, "bold")).pack(pady=10)

tk.Label(register_frame, text="Full Name").pack()
reg_fullname = tk.Entry(register_frame)
reg_fullname.pack()

tk.Label(register_frame, text="Email").pack()
reg_email = tk.Entry(register_frame)
reg_email.pack()

tk.Label(register_frame, text="Username").pack()
reg_username = tk.Entry(register_frame)
reg_username.pack()

tk.Label(register_frame, text="Password").pack()
reg_password = tk.Entry(register_frame, show="*")
reg_password.pack()

tk.Label(register_frame, text="Role").pack()
reg_role = tk.StringVar()
reg_role.set("Member")  # default
tk.OptionMenu(register_frame, reg_role, "Member", "Manager", "Trainer").pack()

tk.Button(register_frame, text="Submit", command=register_user).pack(pady=10)
tk.Button(register_frame, text="Back to Login", command=show_login_page).pack()

# ---------- Start with Login Page ----------
show_login_page()

root.mainloop()