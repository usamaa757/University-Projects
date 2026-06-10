import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import database

class ClassManagement:
    def __init__(self, parent, db, user_data=None):
        self.parent = parent
        self.db = db
        self.user_data = user_data
        self.setup_ui()
        self.load_classes()
    
    def setup_ui(self):
        # Main container
        self.container = tk.Frame(self.parent, bg='#ecf0f1')
        self.container.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Header
        header_frame = tk.Frame(self.container, bg='#ecf0f1')
        header_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(header_frame, text="Class Management", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(side='left')
        
        # Add class button
        add_btn = tk.Button(header_frame, text="+ Add New Class", 
                           command=self.add_class_dialog,
                           bg='#3498db', fg='white', font=('Arial', 11))
        add_btn.pack(side='right', padx=10)
        
        # Search and filter frame
        filter_frame = tk.Frame(self.container, bg='#ecf0f1')
        filter_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(filter_frame, text="Search:", bg='#ecf0f1').pack(side='left', padx=(0, 5))
        self.search_var = tk.StringVar()
        self.search_var.trace('w', self.search_classes)
        search_entry = tk.Entry(filter_frame, textvariable=self.search_var, width=30)
        search_entry.pack(side='left', padx=(0, 20))
        
        tk.Label(filter_frame, text="Status:", bg='#ecf0f1').pack(side='left', padx=(0, 5))
        self.status_filter = ttk.Combobox(filter_frame, values=['All', 'Scheduled', 'Ongoing', 'Completed', 'Cancelled'], 
                                         state='readonly', width=15)
        self.status_filter.set('All')
        self.status_filter.bind('<<ComboboxSelected>>', lambda e: self.load_classes())
        self.status_filter.pack(side='left', padx=(0, 20))
        
        # Classes table
        table_frame = tk.Frame(self.container, bg='white', relief='sunken', bd=1)
        table_frame.pack(fill='both', expand=True)
        
        # Create treeview
        self.tree = ttk.Treeview(table_frame, columns=('ID', 'Name', 'Type', 'Trainer', 'Branch', 'Zone', 
                                                      'Date', 'Time', 'Duration', 'Capacity', 'Enrolled', 'Status'),
                               show='headings', height=20)
        
        # Define columns
        columns = [
            ('ID', 50),
            ('Name', 150),
            ('Type', 100),
            ('Trainer', 120),
            ('Branch', 120),
            ('Zone', 100),
            ('Date', 100),
            ('Time', 80),
            ('Duration', 80),
            ('Capacity', 80),
            ('Enrolled', 80),
            ('Status', 100)
        ]
        
        for col, width in columns:
            self.tree.heading(col, text=col)
            self.tree.column(col, width=width, anchor='center')
        
        # Scrollbar
        scrollbar = ttk.Scrollbar(table_frame, orient='vertical', command=self.tree.yview)
        self.tree.configure(yscrollcommand=scrollbar.set)
        scrollbar.pack(side='right', fill='y')
        self.tree.pack(side='left', fill='both', expand=True)
        
        # Action buttons frame
        action_frame = tk.Frame(self.container, bg='#ecf0f1')
        action_frame.pack(fill='x', pady=(20, 0))
        
        tk.Button(action_frame, text="View Details", command=self.view_class_details,
                 bg='#3498db', fg='white').pack(side='left', padx=5)
        tk.Button(action_frame, text="Edit Class", command=self.edit_class_dialog,
                 bg='#f39c12', fg='white').pack(side='left', padx=5)
        tk.Button(action_frame, text="Cancel Class", command=self.cancel_class,
                 bg='#e74c3c', fg='white').pack(side='left', padx=5)
        tk.Button(action_frame, text="View Participants", command=self.view_participants,
                 bg='#2ecc71', fg='white').pack(side='left', padx=5)
        tk.Button(action_frame, text="Refresh", command=self.load_classes,
                 bg='#95a5a6', fg='white').pack(side='right', padx=5)
        tk.Button(
            action_frame,
            text="Complete Class",
            command=self.complete_class,
            bg='#27ae60',
            fg='white'
        ).pack(side='right', padx=5)

        
        # Bind double click
        self.tree.bind('<Double-1>', lambda e: self.view_class_details())
    
    def load_classes(self):
        # Clear existing items
        for item in self.tree.get_children():
            self.tree.delete(item)
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Build query based on user role
        if self.user_data and self.user_data["role"] == "Trainer":
            # Trainer can only see their own classes
            query = """
                SELECT c.class_id, c.class_name, c.class_type, 
                       s.first_name || ' ' || s.last_name as trainer_name,
                       b.branch_name, z.zone_name,
                       c.class_date, c.class_time, c.duration_minutes, 
                       c.capacity, c.status,
                       (SELECT COUNT(*) FROM class_registrations WHERE class_id = c.class_id) as enrolled_count
                FROM classes c
                LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
                LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
                LEFT JOIN staff s ON c.trainer_id = s.staff_id
                WHERE c.trainer_id = ?
            """
            params = (self.user_data["id"],)
        else:
            # Admin/Manager can see all classes
            query = """
                SELECT c.class_id, c.class_name, c.class_type, 
                       s.first_name || ' ' || s.last_name as trainer_name,
                       b.branch_name, z.zone_name,
                       c.class_date, c.class_time, c.duration_minutes, 
                       c.capacity, c.status,
                       (SELECT COUNT(*) FROM class_registrations WHERE class_id = c.class_id) as enrolled_count
                FROM classes c
                LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
                LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
                LEFT JOIN staff s ON c.trainer_id = s.staff_id
                WHERE 1=1
            """
            params = ()
        
        # Apply status filter
        status = self.status_filter.get()
        if status != 'All':
            query += " AND c.status = ?"
            params = params + (status,)
        
        # Apply search filter
        search_term = self.search_var.get().strip()
        if search_term:
            query += " AND (c.class_name LIKE ? OR c.class_type LIKE ? OR s.first_name LIKE ?)"
            search_pattern = f"%{search_term}%"
            params = params + (search_pattern, search_pattern, search_pattern)
        
        query += " ORDER BY c.class_date, c.class_time"
        
        cursor.execute(query, params)
        classes = cursor.fetchall()
        
        for cls in classes:
            # Format time
            class_time = cls[7]
            if class_time:
                class_time = class_time[:5]  # Extract HH:MM
            
            self.tree.insert('', 'end', values=(
                cls[0],  # class_id
                cls[1],  # class_name
                cls[2],  # class_type
                cls[3],  # trainer_name
                cls[4],  # branch_name
                cls[5],  # zone_name
                cls[6],  # class_date
                class_time,
                f"{cls[8]} mins",
                cls[9],  # capacity
                cls[10], # enrolled_count
                cls[11]  # status
            ))
        
        conn.close()
    
    def search_classes(self, *args):
        self.load_classes()
    
    def add_class_dialog(self):
        dialog = tk.Toplevel(self.parent)
        dialog.title("Add New Class")
        dialog.geometry("550x650")
        dialog.configure(bg='#ecf0f1')
        dialog.transient(self.parent)
        dialog.grab_set()
        
        # Center the dialog
        dialog.update_idletasks()
        width = dialog.winfo_width()
        height = dialog.winfo_height()
        x = (dialog.winfo_screenwidth() // 2) - (width // 2)
        y = (dialog.winfo_screenheight() // 2) - (height // 2)
        dialog.geometry(f'{width}x{height}+{x}+{y}')
        
        # Form fields
        fields_frame = tk.Frame(dialog, bg='#ecf0f1', padx=20, pady=20)
        fields_frame.pack(fill='both', expand=True)
        
        row = 0
        
        # Class Name
        tk.Label(fields_frame, text="Class Name:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        name_entry = tk.Entry(fields_frame, width=40)
        name_entry.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Class Type
        tk.Label(fields_frame, text="Class Type:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        type_combo = ttk.Combobox(fields_frame, values=['Yoga', 'Zumba', 'HIIT', 'Pilates', 'Spinning', 'Boxing', 
                                                       'Strength Training', 'Cardio', 'CrossFit', 'Dance'], width=37)
        type_combo.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Description
        tk.Label(fields_frame, text="Description:", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        desc_text = tk.Text(fields_frame, height=3, width=30)
        desc_text.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Date
        tk.Label(fields_frame, text="Date:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        date_entry = tk.Entry(fields_frame, width=40)
        date_entry.insert(0, datetime.now().strftime('%Y-%m-%d'))
        date_entry.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Time
        tk.Label(fields_frame, text="Time:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        time_entry = tk.Entry(fields_frame, width=40)
        time_entry.insert(0, "09:00")
        time_entry.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Duration
        tk.Label(fields_frame, text="Duration (mins):*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        duration_entry = tk.Entry(fields_frame, width=40)
        duration_entry.insert(0, "60")
        duration_entry.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Capacity
        tk.Label(fields_frame, text="Capacity:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        capacity_entry = tk.Entry(fields_frame, width=40)
        capacity_entry.insert(0, "20")
        capacity_entry.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Branch selection
        tk.Label(fields_frame, text="Branch:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        branch_var = tk.StringVar()
        branch_combo = ttk.Combobox(fields_frame, textvariable=branch_var, state='readonly', width=37)
        branch_combo.grid(row=row, column=1, pady=5, padx=(0, 10))
        row += 1
        
        # Zone selection
        tk.Label(fields_frame, text="Zone:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        zone_var = tk.StringVar()
        zone_combo = ttk.Combobox(fields_frame, textvariable=zone_var, state='readonly', width=37)
        zone_combo.grid(row=row, column=1, pady=5, padx=(0, 10))
        zone_combo['state'] = 'disabled'  # Disabled until branch is selected
        row += 1
        
        # Trainer selection
        tk.Label(fields_frame, text="Trainer:*", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        trainer_var = tk.StringVar()
        trainer_combo = ttk.Combobox(fields_frame, textvariable=trainer_var, state='readonly', width=37)
        trainer_combo.grid(row=row, column=1, pady=5, padx=(0, 10))
        trainer_combo['state'] = 'disabled'  # Disabled until zone is selected
        row += 1
        
        # Available members (for info)
        tk.Label(fields_frame, text="Available Members:", bg='#ecf0f1').grid(row=row, column=0, sticky='w', pady=5)
        members_label = tk.Label(fields_frame, text="Select branch and zone first", 
                                bg='#ecf0f1', fg='#7f8c8d')
        members_label.grid(row=row, column=1, sticky='w', pady=5, padx=(0, 10))
        row += 1
        
        # Function to load branches
        def load_branches():
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            if self.user_data and self.user_data["role"] == "Trainer":
                # Trainer can only use their own branch
                cursor.execute("SELECT branch_id, branch_name FROM gym_branches WHERE branch_id = ?", 
                             (self.user_data["branch_id"],))
            else:
                # Admin/Manager can select any branch
                cursor.execute("SELECT branch_id, branch_name FROM gym_branches ORDER BY branch_name")
            
            branches = cursor.fetchall()
            conn.close()
            
            branch_dict = {name: bid for bid, name in branches}
            branch_combo['values'] = [name for _, name in branches]
            
            # Set default for trainer
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("SELECT branch_name FROM gym_branches WHERE branch_id = ?", 
                             (self.user_data["branch_id"],))
                result = cursor.fetchone()
                if result:
                    branch_combo.set(result[0])
                    branch_combo['state'] = 'readonly'
                    # Trigger zone loading
                    load_zones()  # Auto-load zones for trainer
            
            return branch_dict
        
        # Function to load zones based on selected branch
        def load_zones(*args):
            selected_branch = branch_var.get()
            if not selected_branch:
                zone_combo['values'] = []
                zone_combo['state'] = 'disabled'
                trainer_combo['values'] = []
                trainer_combo['state'] = 'disabled'
                members_label.config(text="Select branch first")
                return
            
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            # Get branch_id
            cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
            branch_id = cursor.fetchone()[0]
            
            if self.user_data and self.user_data["role"] == "Trainer":
                # Trainer can only use their own zone
                cursor.execute("SELECT zone_id, zone_name FROM workout_zones WHERE branch_id = ? AND zone_id = ?", 
                             (branch_id, self.user_data["zone_id"]))
            else:
                # Admin/Manager can select any zone in the branch
                cursor.execute("SELECT zone_id, zone_name FROM workout_zones WHERE branch_id = ? ORDER BY zone_name", 
                             (branch_id,))
            
            zones = cursor.fetchall()
            conn.close()
            
            zone_dict = {name: zid for zid, name in zones}
            zone_combo['values'] = [name for _, name in zones]
            zone_combo['state'] = 'readonly'
            
            # Clear dependent fields
            trainer_combo['values'] = []
            trainer_combo['state'] = 'disabled'
            members_label.config(text="Select zone first")
            
            # Set default for trainer
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("SELECT zone_name FROM workout_zones WHERE zone_id = ?", 
                             (self.user_data["zone_id"],))
                result = cursor.fetchone()
                if result:
                    zone_combo.set(result[0])
                    zone_combo['state'] = 'readonly'
                    # Trigger trainer loading
                    load_trainers()  # Auto-load trainers for trainer
            
            return zone_dict
        
        # Function to load trainers based on selected zone
        def load_trainers(*args):
            selected_zone = zone_var.get()
            selected_branch = branch_var.get()
            
            if not selected_zone or not selected_branch:
                trainer_combo['values'] = []
                trainer_combo['state'] = 'disabled'
                return
            
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            # Get branch_id and zone_id
            cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
            branch_id = cursor.fetchone()[0]
            
            cursor.execute("SELECT zone_id FROM workout_zones WHERE zone_name = ? AND branch_id = ?", 
                          (selected_zone, branch_id))
            zone_id = cursor.fetchone()[0]
            
            if self.user_data and self.user_data["role"] == "Trainer":
                # Trainer can only select themselves
                cursor.execute("""
                    SELECT staff_id, first_name || ' ' || last_name as name 
                    FROM staff 
                    WHERE staff_id = ? AND role = 'Trainer' AND status = 'Active'
                """, (self.user_data["id"],))
            else:
                # Admin/Manager can select any trainer in the same branch and zone
                cursor.execute("""
                    SELECT staff_id, first_name || ' ' || last_name as name 
                    FROM staff 
                    WHERE branch_id = ? AND zone_id = ? AND role = 'Trainer' AND status = 'Active'
                    ORDER BY first_name
                """, (branch_id, zone_id))
            
            trainers = cursor.fetchall()
            
            # Get available members count
            cursor.execute("""
                SELECT COUNT(*) 
                FROM members 
                WHERE branch_id = ? AND zone_id = ? AND status = 'Active'
            """, (branch_id, zone_id))
            members_count = cursor.fetchone()[0]
            
            conn.close()
            
            trainer_dict = {name: tid for tid, name in trainers}
            trainer_combo['values'] = [name for _, name in trainers]
            trainer_combo['state'] = 'readonly' if trainers else 'disabled'
            
            # Update members label
            members_label.config(text=f"{members_count} active members available in this zone")
            
            # Set default for trainer
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("SELECT first_name || ' ' || last_name FROM staff WHERE staff_id = ?", 
                             (self.user_data["id"],))
                result = cursor.fetchone()
                if result:
                    trainer_combo.set(result[0])
            
            return trainer_dict
        
        # Track dictionaries for later use
        branch_dict = {}
        zone_dict = {}
        trainer_dict = {}
        
        # Load initial data
        def initialize_dialog():
            nonlocal branch_dict, zone_dict, trainer_dict
            branch_dict = load_branches()
            
            # Set up trace for branch selection
            branch_var.trace('w', lambda *args: (load_zones(), update_zone_dict()))
            zone_var.trace('w', lambda *args: (load_trainers(), update_trainer_dict()))
        
        # Helper functions to update dictionaries
        def update_zone_dict():
            nonlocal zone_dict
            selected_branch = branch_var.get()
            if selected_branch:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
                branch_id = cursor.fetchone()[0]
                
                cursor.execute("SELECT zone_id, zone_name FROM workout_zones WHERE branch_id = ? ORDER BY zone_name", 
                              (branch_id,))
                zones = cursor.fetchall()
                zone_dict = {name: zid for zid, name in zones}
                conn.close()
        
        def update_trainer_dict():
            nonlocal trainer_dict
            selected_zone = zone_var.get()
            selected_branch = branch_var.get()
            
            if selected_zone and selected_branch:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
                branch_id = cursor.fetchone()[0]
                
                cursor.execute("SELECT zone_id FROM workout_zones WHERE zone_name = ? AND branch_id = ?", 
                              (selected_zone, branch_id))
                zone_id = cursor.fetchone()[0]
                
                cursor.execute("""
                    SELECT staff_id, first_name || ' ' || last_name as name 
                    FROM staff 
                    WHERE branch_id = ? AND zone_id = ? AND role = 'Trainer' AND status = 'Active'
                """, (branch_id, zone_id))
                
                trainers = cursor.fetchall()
                trainer_dict = {name: tid for tid, name in trainers}
                conn.close()
        
        # Initialize dialog
        initialize_dialog()
        
        # Buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(fill='x')
        
        def save_class():
            # Validate inputs
            if not name_entry.get().strip():
                messagebox.showerror("Error", "Class name is required")
                return
            
            if not branch_var.get():
                messagebox.showerror("Error", "Branch is required")
                return
            
            if not zone_var.get():
                messagebox.showerror("Error", "Zone is required")
                return
            
            if not trainer_var.get():
                messagebox.showerror("Error", "Trainer is required")
                return
            
            try:
                # Get branch_id and zone_id
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (branch_var.get(),))
                branch_id = cursor.fetchone()[0]
                
                cursor.execute("SELECT zone_id FROM workout_zones WHERE zone_name = ? AND branch_id = ?", 
                              (zone_var.get(), branch_id))
                zone_id = cursor.fetchone()[0]
                
                # Get trainer_id from trainer_dict
                if trainer_var.get() not in trainer_dict:
                    # Update trainer_dict if needed
                    cursor.execute("SELECT staff_id FROM staff WHERE first_name || ' ' || last_name = ?", 
                                  (trainer_var.get(),))
                    trainer_id = cursor.fetchone()[0]
                    trainer_dict[trainer_var.get()] = trainer_id
                else:
                    trainer_id = trainer_dict[trainer_var.get()]
                
                # Prepare class data
                class_data = (
                    name_entry.get().strip(),
                    type_combo.get(),
                    desc_text.get("1.0", tk.END).strip(),
                    date_entry.get(),
                    time_entry.get(),
                    int(duration_entry.get()),
                    int(capacity_entry.get()),
                    trainer_id,
                    branch_id,
                    zone_id,
                    'Scheduled'
                )
                
                # Use database method to add class
                class_id = self.db.add_class(class_data)
                
                if class_id:
                    messagebox.showinfo("Success", f"Class added successfully! Class ID: {class_id}")
                    dialog.destroy()
                    self.load_classes()
                else:
                    messagebox.showerror("Error", "Failed to add class")
                
                conn.close()
                    
            except ValueError as e:
                messagebox.showerror("Error", f"Invalid input: {str(e)}")
            except Exception as e:
                messagebox.showerror("Error", f"Failed to add class: {str(e)}")
                import traceback
                traceback.print_exc()
        
        tk.Button(button_frame, text="Save", command=save_class,
                 bg='#3498db', fg='white', width=15).pack(side='left', padx=10)
        tk.Button(button_frame, text="Cancel", command=dialog.destroy,
                 bg='#95a5a6', fg='white', width=15).pack(side='right', padx=10)
    
    def view_class_details(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT c.*, b.branch_name, z.zone_name, s.first_name || ' ' || s.last_name as trainer_name
            FROM classes c
            LEFT JOIN gym_branches b ON c.branch_id = b.branch_id
            LEFT JOIN workout_zones z ON c.zone_id = z.zone_id
            LEFT JOIN staff s ON c.trainer_id = s.staff_id
            WHERE c.class_id = ?
        """, (class_id,))
        
        cls = cursor.fetchone()
        conn.close()
        
        if not cls:
            return
        
        # Create details dialog
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Class Details - {cls[1]}")
        dialog.geometry("500x450")
        dialog.configure(bg='#ecf0f1')
        
        details_frame = tk.Frame(dialog, bg='#ecf0f1', padx=20, pady=20)
        details_frame.pack(fill='both', expand=True)
        
        # Display class details
        details = [
            ("Class Name:", cls[1]),
            ("Type:", cls[2]),
            ("Description:", cls[3] if cls[3] else "N/A"),
            ("Trainer:", cls[13]),
            ("Branch:", cls[14]),
            ("Zone:", cls[15]),
            ("Date:", cls[6]),
            ("Time:", cls[7]),
            ("Duration:", f"{cls[8]} minutes"),
            ("Capacity:", f"{cls[9]} people"),
            ("Status:", cls[10]),
            ("Created:", cls[11]),
            ("Last Updated:", cls[12] if cls[12] else "N/A")
        ]
        
        for i, (label, value) in enumerate(details):
            tk.Label(details_frame, text=label, font=('Arial', 10, 'bold'), 
                    bg='#ecf0f1').grid(row=i, column=0, sticky='w', pady=5)
            tk.Label(details_frame, text=value, bg='#ecf0f1').grid(row=i, column=1, sticky='w', pady=5, padx=(10, 0))
    
    def edit_class_dialog(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        
        # Get class details first
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM classes WHERE class_id = ?", (class_id,))
        class_data = cursor.fetchone()
        conn.close()
        
        if not class_data:
            messagebox.showerror("Error", "Class not found")
            return
        
        # Create edit dialog (similar to add_class_dialog but pre-filled)
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Edit Class - {class_data[1]}")
        dialog.geometry("550x650")
        dialog.configure(bg='#ecf0f1')
        dialog.transient(self.parent)
        dialog.grab_set()
        
        # Similar to add_class_dialog but with pre-filled values
        # You can copy the add_class_dialog code and modify it to:
        # 1. Pre-fill all fields with existing class data
        # 2. Change save function to call db.update_class() instead of db.add_class()
        # 3. Add a "Delete" button if needed
        
        messagebox.showinfo("Info", "Edit functionality not yet implemented")
    
    def cancel_class(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        if messagebox.askyesno("Confirm Cancellation", 
                              f"Are you sure you want to cancel '{class_name}'?\n\nThis will notify all registered participants."):
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                # FIXED: Use class_id column name
                cursor.execute("UPDATE classes SET status = 'Cancelled' WHERE class_id = ?", (class_id,))
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Class cancelled successfully")
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to cancel class: {str(e)}")
                import traceback
                traceback.print_exc()
    def complete_class(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        if messagebox.askyesno("Confirm Cancellation", 
                              f"Are you sure you want to complete '{class_name}'?\n\nThis will notify all registered participants."):
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                # FIXED: Use class_id column name
                cursor.execute("UPDATE classes SET status = 'Completed' WHERE class_id = ?", (class_id,))
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Class completed successfully")
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to complete class: {str(e)}")
                import traceback
                traceback.print_exc()
                
    def view_participants(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Participants - {class_name}")
        dialog.geometry("700x500")
        dialog.configure(bg='#ecf0f1')
        
        # Participants list
        tree_frame = tk.Frame(dialog, bg='white', relief='sunken', bd=1)
        tree_frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        tree = ttk.Treeview(tree_frame, columns=('ID', 'Name', 'Email', 'Phone', 'Registration Date'),
                           show='headings', height=15)
        
        columns = [('ID', 50), ('Name', 150), ('Email', 150), ('Phone', 120), ('Registration Date', 120)]
        for col, width in columns:
            tree.heading(col, text=col)
            tree.column(col, width=width, anchor='center')
        
        scrollbar = ttk.Scrollbar(tree_frame, orient='vertical', command=tree.yview)
        tree.configure(yscrollcommand=scrollbar.set)
        scrollbar.pack(side='right', fill='y')
        tree.pack(side='left', fill='both', expand=True)
        
        # Load participants
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT m.member_id, m.first_name || ' ' || m.last_name, m.email, m.phone, cr.registration_date
            FROM class_registrations cr
            JOIN members m ON cr.member_id = m.member_id
            WHERE cr.class_id = ?
            ORDER BY cr.registration_date DESC
        """, (class_id,))
        
        participants = cursor.fetchall()
        
        for participant in participants:
            tree.insert('', 'end', values=participant)
        
        conn.close()
        
        # Action buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=10)
        button_frame.pack(fill='x')
        
        tk.Button(button_frame, text="Export to CSV", 
                 command=lambda: self.export_participants(participants, class_name)).pack(side='left', padx=10)
        tk.Button(button_frame, text="Close", command=dialog.destroy).pack(side='right', padx=10)
    
    def export_participants(self, participants, class_name):
        # Simple CSV export
        import csv
        from tkinter import filedialog
        
        filename = filedialog.asksaveasfilename(
            defaultextension=".csv",
            filetypes=[("CSV files", "*.csv"), ("All files", "*.*")],
            initialfile=f"{class_name}_participants.csv"
        )
        
        if filename:
            try:
                with open(filename, 'w', newline='') as file:
                    writer = csv.writer(file)
                    writer.writerow(['ID', 'Name', 'Email', 'Phone', 'Registration Date'])
                    writer.writerows(participants)
                messagebox.showinfo("Success", f"Participants exported to {filename}")
            except Exception as e:
                messagebox.showerror("Error", f"Failed to export: {str(e)}")