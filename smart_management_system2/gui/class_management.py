import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import database

class ClassManagement:
    def __init__(self, parent, db, user_data=None):
        self.parent = parent
        self.db = db
        self.user_data = user_data
        self.current_class = None
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
        
        # Action buttons frame with horizontal scrolling
        action_frame = tk.Frame(self.container, bg='#ecf0f1')
        action_frame.pack(fill='x', pady=(20, 0))
        
        # Create canvas for horizontal scrolling
        canvas = tk.Canvas(action_frame, bg='#ecf0f1', height=50, highlightthickness=0)
        canvas.pack(side='left', fill='both', expand=True)
        
        # Add horizontal scrollbar
        h_scrollbar = ttk.Scrollbar(action_frame, orient='horizontal', command=canvas.xview)
        h_scrollbar.pack(side='bottom', fill='x')
        
        canvas.configure(xscrollcommand=h_scrollbar.set)
        
        # Frame inside canvas to hold buttons
        buttons_frame = tk.Frame(canvas, bg='#ecf0f1')
        canvas.create_window((0, 0), window=buttons_frame, anchor='nw')
        
        buttons = [
            ("📋 View Details", self.view_class_details, '#3498db'),
            ("✏️ Edit", self.edit_class_dialog, '#f39c12'),
            ("👥 Participants", self.view_participants, '#2ecc71'),
            ("✅ Complete", self.complete_class, '#27ae60'),
            ("❌ Cancel", self.cancel_class, '#e74c3c'),
            ("📊 Attendance", self.mark_attendance, '#9b59b6'),
            ("📧 Notify", self.notify_participants, '#e67e22'),
            ("📝 Register Member", self.register_member_dialog, '#1abc9c'),
            ("📎 Export", self.export_class_data, '#34495e'),
            ("🔄 Refresh", self.load_classes, '#95a5a6'),
        ]
        
        for text, command, color in buttons:
            btn = tk.Button(buttons_frame, text=text, bg=color, fg='white',
                           font=('Arial', 10, 'bold'), padx=15, pady=8,
                           cursor='hand2', command=command)
            btn.pack(side='left', padx=5)
        
        # Update scroll region
        buttons_frame.update_idletasks()
        canvas.configure(scrollregion=canvas.bbox('all'))
        
        # Mouse wheel scrolling
        def on_mousewheel(event):
            canvas.xview_scroll(int(-1 * (event.delta / 120)), 'units')
        canvas.bind_all('<MouseWheel>', on_mousewheel)
        
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
                       c.capacity, 
                       (SELECT COUNT(*) FROM class_registrations WHERE class_id = c.class_id) as enrolled_count,
                       c.status
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
                       c.capacity, 
                       (SELECT COUNT(*) FROM class_registrations WHERE class_id = c.class_id) as enrolled_count,
                       c.status
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
            
            # Check if class date is passed and update status if needed
            class_date = datetime.strptime(cls[6], '%Y-%m-%d').date()
            today = datetime.now().date()
            
            status_value = cls[11]
            if class_date < today and status_value == 'Scheduled':
                status_value = 'Completed'
            
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
                status_value  # status
            ))
        
        conn.close()
    
    def search_classes(self, *args):
        self.load_classes()
    
    def add_class_dialog(self):
        dialog = tk.Toplevel(self.parent)
        dialog.title("Add New Class")
        dialog.geometry("600x750")
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
        
        # Main container with scrollbar
        main_container = tk.Frame(dialog, bg='#ecf0f1')
        main_container.pack(fill='both', expand=True)
        
        # Create canvas and scrollbar for the entire form
        canvas = tk.Canvas(main_container, bg='#ecf0f1', highlightthickness=0)
        scrollbar = ttk.Scrollbar(main_container, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='#ecf0f1')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        # Form fields
        fields_frame = tk.Frame(scrollable_frame, bg='#ecf0f1', padx=30, pady=20)
        fields_frame.pack(fill='both', expand=True)
        
        row = 0
        
        # Class Name
        tk.Label(fields_frame, text="Class Name:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        name_entry = tk.Entry(fields_frame, width=40, font=('Arial', 11))
        name_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Class Type
        tk.Label(fields_frame, text="Class Type:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        type_combo = ttk.Combobox(fields_frame, values=['Yoga', 'Zumba', 'HIIT', 'Pilates', 'Spinning', 'Boxing', 
                                                       'Strength Training', 'Cardio', 'CrossFit', 'Dance'], width=37)
        type_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Description
        tk.Label(fields_frame, text="Description:", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        desc_text = tk.Text(fields_frame, height=3, width=30, font=('Arial', 11))
        desc_text.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Date
        tk.Label(fields_frame, text="Date:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        date_frame = tk.Frame(fields_frame, bg='#ecf0f1')
        date_frame.grid(row=row, column=1, pady=8, padx=(10, 10), sticky='w')
        date_entry = tk.Entry(date_frame, width=30, font=('Arial', 11))
        date_entry.insert(0, datetime.now().strftime('%Y-%m-%d'))
        date_entry.pack(side='left')
        tk.Button(date_frame, text="📅", command=lambda: self.pick_date(date_entry)).pack(side='left', padx=5)
        row += 1
        
        # Time
        tk.Label(fields_frame, text="Time:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        time_frame = tk.Frame(fields_frame, bg='#ecf0f1')
        time_frame.grid(row=row, column=1, pady=8, padx=(10, 10), sticky='w')
        time_entry = tk.Entry(time_frame, width=30, font=('Arial', 11))
        time_entry.insert(0, "09:00")
        time_entry.pack(side='left')
        tk.Button(time_frame, text="⏰", command=lambda: self.pick_time(time_entry)).pack(side='left', padx=5)
        row += 1
        
        # Duration
        tk.Label(fields_frame, text="Duration (mins):*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        duration_entry = tk.Entry(fields_frame, width=40, font=('Arial', 11))
        duration_entry.insert(0, "60")
        duration_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Capacity
        tk.Label(fields_frame, text="Capacity:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        capacity_entry = tk.Entry(fields_frame, width=40, font=('Arial', 11))
        capacity_entry.insert(0, "20")
        capacity_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Branch selection
        tk.Label(fields_frame, text="Branch:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        branch_var = tk.StringVar()
        branch_combo = ttk.Combobox(fields_frame, textvariable=branch_var, state='readonly', width=37)
        branch_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Zone selection
        tk.Label(fields_frame, text="Zone:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        zone_var = tk.StringVar()
        zone_combo = ttk.Combobox(fields_frame, textvariable=zone_var, state='readonly', width=37)
        zone_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        zone_combo['state'] = 'disabled'
        row += 1
        
        # Trainer selection
        tk.Label(fields_frame, text="Trainer:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        trainer_var = tk.StringVar()
        trainer_combo = ttk.Combobox(fields_frame, textvariable=trainer_var, state='readonly', width=37)
        trainer_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        trainer_combo['state'] = 'disabled'
        row += 1
        
        # Recurring options
        recurring_var = tk.BooleanVar()
        tk.Checkbutton(fields_frame, text="Recurring Class", variable=recurring_var,
                      bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, columnspan=2, sticky='w', pady=10)
        row += 1
        
        recurring_frame = tk.Frame(fields_frame, bg='#ecf0f1')
        recurring_frame.grid(row=row, column=0, columnspan=2, sticky='w', pady=5)
        
        tk.Label(recurring_frame, text="Repeat:", bg='#ecf0f1', font=('Arial', 11)).pack(side='left')
        repeat_var = tk.StringVar(value="Weekly")
        repeat_combo = ttk.Combobox(recurring_frame, textvariable=repeat_var, 
                                    values=['Daily', 'Weekly', 'Bi-weekly', 'Monthly'], width=10)
        repeat_combo.pack(side='left', padx=10)
        
        tk.Label(recurring_frame, text="End After:", bg='#ecf0f1', font=('Arial', 11)).pack(side='left', padx=(20, 5))
        occurrences_var = tk.StringVar(value="4")
        occurrences_entry = tk.Entry(recurring_frame, textvariable=occurrences_var, width=5, font=('Arial', 11))
        occurrences_entry.pack(side='left')
        tk.Label(recurring_frame, text="classes", bg='#ecf0f1', font=('Arial', 11)).pack(side='left', padx=5)
        
        # Toggle recurring frame
        recurring_frame.pack_forget()
        
        def toggle_recurring():
            if recurring_var.get():
                recurring_frame.pack()
            else:
                recurring_frame.pack_forget()
        
        recurring_var.trace('w', lambda *args: toggle_recurring())
        
        # Function to load branches
        def load_branches():
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("SELECT branch_id, branch_name FROM gym_branches WHERE branch_id = ?", 
                             (self.user_data["branch_id"],))
            else:
                cursor.execute("SELECT branch_id, branch_name FROM gym_branches ORDER BY branch_name")
            
            branches = cursor.fetchall()
            
            if branches:
                branch_combo['values'] = [name for _, name in branches]
                
                # For trainer, auto-select their branch
                if self.user_data and self.user_data["role"] == "Trainer":
                    cursor.execute("SELECT branch_name FROM gym_branches WHERE branch_id = ?", 
                                 (self.user_data["branch_id"],))
                    result = cursor.fetchone()
                    if result:
                        branch_combo.set(result[0])
                        # Trigger zone loading
                        load_zones()
            
            conn.close()
        
        # Function to load zones
        def load_zones(*args):
            selected_branch = branch_var.get()
            if not selected_branch:
                zone_combo['values'] = []
                zone_combo['state'] = 'disabled'
                trainer_combo['values'] = []
                trainer_combo['state'] = 'disabled'
                return
            
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
            result = cursor.fetchone()
            if not result:
                conn.close()
                return
            branch_id = result[0]
            
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("SELECT zone_id, zone_name FROM workout_zones WHERE branch_id = ? AND zone_id = ?", 
                             (branch_id, self.user_data["zone_id"]))
            else:
                cursor.execute("SELECT zone_id, zone_name FROM workout_zones WHERE branch_id = ? ORDER BY zone_name", 
                             (branch_id,))
            
            zones = cursor.fetchall()
            zone_combo['values'] = [name for _, name in zones]
            zone_combo['state'] = 'readonly' if zones else 'disabled'
            
            # Auto-select for trainer
            if self.user_data and self.user_data["role"] == "Trainer" and zones:
                cursor.execute("SELECT zone_name FROM workout_zones WHERE zone_id = ?", 
                             (self.user_data["zone_id"],))
                zone_result = cursor.fetchone()
                if zone_result:
                    zone_var.set(zone_result[0])
                    # Trigger trainer loading
                    load_trainers()
            
            conn.close()
        
        # Function to load trainers - FIXED
        def load_trainers(*args):
            selected_zone = zone_var.get()
            selected_branch = branch_var.get()
            
            if not selected_zone or not selected_branch:
                trainer_combo['values'] = []
                trainer_combo['state'] = 'disabled'
                return
            
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (selected_branch,))
            branch_result = cursor.fetchone()
            if not branch_result:
                conn.close()
                return
            branch_id = branch_result[0]
            
            cursor.execute("SELECT zone_id FROM workout_zones WHERE zone_name = ? AND branch_id = ?", 
                          (selected_zone, branch_id))
            zone_result = cursor.fetchone()
            if not zone_result:
                conn.close()
                return
            zone_id = zone_result[0]
            
            if self.user_data and self.user_data["role"] == "Trainer":
                cursor.execute("""
                    SELECT staff_id, first_name || ' ' || last_name as name 
                    FROM staff 
                    WHERE staff_id = ? AND role = 'Trainer' AND status = 'Active'
                """, (self.user_data["id"],))
            else:
                cursor.execute("""
                    SELECT staff_id, first_name || ' ' || last_name as name 
                    FROM staff 
                    WHERE branch_id = ? AND zone_id = ? AND role = 'Trainer' AND status = 'Active'
                    ORDER BY first_name
                """, (branch_id, zone_id))
            
            trainers = cursor.fetchall()
            
            trainer_combo['values'] = [name for _, name in trainers]
            trainer_combo['state'] = 'readonly' if trainers else 'disabled'
            
            # Auto-select for trainer
            if self.user_data and self.user_data["role"] == "Trainer" and trainers:
                cursor.execute("SELECT first_name || ' ' || last_name FROM staff WHERE staff_id = ?", 
                             (self.user_data["id"],))
                result = cursor.fetchone()
                if result:
                    trainer_var.set(result[0])
            
            conn.close()
        
        # Bind events
        branch_var.trace('w', load_zones)
        zone_var.trace('w', load_trainers)
        
        # Load initial data
        load_branches()
        
        # Button frame - placed outside scrollable area
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(side='bottom', fill='x')
        
        def save_class():
            # Validate inputs
            if not name_entry.get().strip():
                messagebox.showerror("Error", "Class name is required")
                return
            
            if not type_combo.get():
                messagebox.showerror("Error", "Class type is required")
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
                # Validate numeric fields
                try:
                    duration = int(duration_entry.get())
                    capacity = int(capacity_entry.get())
                except ValueError:
                    messagebox.showerror("Error", "Duration and capacity must be numbers")
                    return
                
                # Get branch_id and zone_id
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                cursor.execute("SELECT branch_id FROM gym_branches WHERE branch_name = ?", (branch_var.get(),))
                branch_id = cursor.fetchone()[0]
                
                cursor.execute("SELECT zone_id FROM workout_zones WHERE zone_name = ? AND branch_id = ?", 
                              (zone_var.get(), branch_id))
                zone_id = cursor.fetchone()[0]
                
                # Get trainer_id
                cursor.execute("SELECT staff_id FROM staff WHERE first_name || ' ' || last_name = ?", 
                              (trainer_var.get(),))
                trainer_id = cursor.fetchone()[0]
                
                conn.close()
                
                # Prepare class data
                class_data = (
                    name_entry.get().strip(),
                    type_combo.get(),
                    desc_text.get("1.0", tk.END).strip(),
                    date_entry.get(),
                    time_entry.get(),
                    duration,
                    capacity,
                    trainer_id,
                    branch_id,
                    zone_id,
                    'Scheduled'
                )
                
                # Add the main class
                class_id = self.db.add_class(class_data)
                
                # Add recurring classes if selected
                if recurring_var.get() and class_id:
                    repeat = repeat_var.get()
                    occurrences = int(occurrences_var.get())
                    
                    base_date = datetime.strptime(date_entry.get(), '%Y-%m-%d')
                    
                    for i in range(1, occurrences):
                        if repeat == 'Daily':
                            next_date = base_date + timedelta(days=i)
                        elif repeat == 'Weekly':
                            next_date = base_date + timedelta(weeks=i)
                        elif repeat == 'Bi-weekly':
                            next_date = base_date + timedelta(weeks=i*2)
                        elif repeat == 'Monthly':
                            # Simple monthly addition (approximate)
                            next_date = base_date + timedelta(days=30*i)
                        
                        recurring_data = (
                            f"{name_entry.get().strip()} (Session {i+1})",
                            type_combo.get(),
                            desc_text.get("1.0", tk.END).strip(),
                            next_date.strftime('%Y-%m-%d'),
                            time_entry.get(),
                            duration,
                            capacity,
                            trainer_id,
                            branch_id,
                            zone_id,
                            'Scheduled'
                        )
                        self.db.add_class(recurring_data)
                
                messagebox.showinfo("Success", "Class added successfully!")
                dialog.destroy()
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to add class: {str(e)}")
                import traceback
                traceback.print_exc()
        
        # Create Save and Cancel buttons
        tk.Button(button_frame, text="Save", command=save_class,
                 bg='#2ecc71', fg='white', width=15, font=('Arial', 12, 'bold'),
                 padx=20, pady=10).pack(side='left', padx=20)
        
        tk.Button(button_frame, text="Cancel", command=dialog.destroy,
                 bg='#e74c3c', fg='white', width=15, font=('Arial', 12),
                 padx=20, pady=10).pack(side='right', padx=20)
    
    def edit_class_dialog(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        # Get class details
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM classes WHERE class_id = ?", (class_id,))
        class_data = cursor.fetchone()
        
        if not class_data:
            conn.close()
            messagebox.showerror("Error", "Class not found")
            return
        
        # Get trainer name
        cursor.execute("SELECT first_name || ' ' || last_name FROM staff WHERE staff_id = ?", 
                      (class_data[8],))
        trainer_name = cursor.fetchone()[0]
        
        # Get branch name
        cursor.execute("SELECT branch_name FROM gym_branches WHERE branch_id = ?", 
                      (class_data[9],))
        branch_name = cursor.fetchone()[0]
        
        # Get zone name
        cursor.execute("SELECT zone_name FROM workout_zones WHERE zone_id = ?", 
                      (class_data[10],))
        zone_name = cursor.fetchone()[0]
        
        conn.close()
        
        # Create edit dialog
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Edit Class - {class_name}")
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
        
        # Create scrollable canvas
        canvas = tk.Canvas(fields_frame, bg='#ecf0f1', highlightthickness=0)
        scrollbar = ttk.Scrollbar(fields_frame, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='#ecf0f1')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        row = 0
        
        # Class Name
        tk.Label(scrollable_frame, text="Class Name:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        name_entry = tk.Entry(scrollable_frame, width=40, font=('Arial', 11))
        name_entry.insert(0, class_data[1])
        name_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Class Type
        tk.Label(scrollable_frame, text="Class Type:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        type_combo = ttk.Combobox(scrollable_frame, values=['Yoga', 'Zumba', 'HIIT', 'Pilates', 'Spinning', 'Boxing', 
                                                           'Strength Training', 'Cardio', 'CrossFit', 'Dance'], width=37)
        type_combo.set(class_data[2])
        type_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Description
        tk.Label(scrollable_frame, text="Description:", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        desc_text = tk.Text(scrollable_frame, height=3, width=30, font=('Arial', 11))
        if class_data[3]:
            desc_text.insert('1.0', class_data[3])
        desc_text.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Date
        tk.Label(scrollable_frame, text="Date:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        date_entry = tk.Entry(scrollable_frame, width=40, font=('Arial', 11))
        date_entry.insert(0, class_data[4])
        date_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Time
        tk.Label(scrollable_frame, text="Time:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        time_entry = tk.Entry(scrollable_frame, width=40, font=('Arial', 11))
        time_entry.insert(0, class_data[5])
        time_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Duration
        tk.Label(scrollable_frame, text="Duration (mins):*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        duration_entry = tk.Entry(scrollable_frame, width=40, font=('Arial', 11))
        duration_entry.insert(0, str(class_data[6]))
        duration_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Capacity
        tk.Label(scrollable_frame, text="Capacity:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        capacity_entry = tk.Entry(scrollable_frame, width=40, font=('Arial', 11))
        capacity_entry.insert(0, str(class_data[7]))
        capacity_entry.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Branch (read-only for display)
        tk.Label(scrollable_frame, text="Branch:", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        branch_label = tk.Label(scrollable_frame, text=branch_name, bg='#ecf0f1', font=('Arial', 11))
        branch_label.grid(row=row, column=1, sticky='w', pady=8, padx=(10, 10))
        row += 1
        
        # Zone (read-only for display)
        tk.Label(scrollable_frame, text="Zone:", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        zone_label = tk.Label(scrollable_frame, text=zone_name, bg='#ecf0f1', font=('Arial', 11))
        zone_label.grid(row=row, column=1, sticky='w', pady=8, padx=(10, 10))
        row += 1
        
        # Trainer (read-only for display)
        tk.Label(scrollable_frame, text="Trainer:", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        trainer_label = tk.Label(scrollable_frame, text=trainer_name, bg='#ecf0f1', font=('Arial', 11))
        trainer_label.grid(row=row, column=1, sticky='w', pady=8, padx=(10, 10))
        row += 1
        
        # Status
        tk.Label(scrollable_frame, text="Status:*", bg='#ecf0f1', font=('Arial', 11)).grid(row=row, column=0, sticky='w', pady=8)
        status_combo = ttk.Combobox(scrollable_frame, values=['Scheduled', 'Ongoing', 'Completed', 'Cancelled'], width=37)
        status_combo.set(class_data[11])
        status_combo.grid(row=row, column=1, pady=8, padx=(10, 10))
        row += 1
        
        # Buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(fill='x')
        
        def save_changes():
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                # Prepare update data
                update_data = (
                    name_entry.get().strip(),
                    type_combo.get(),
                    desc_text.get("1.0", tk.END).strip(),
                    date_entry.get(),
                    time_entry.get(),
                    int(duration_entry.get()),
                    int(capacity_entry.get()),
                    status_combo.get(),
                    class_id
                )
                
                cursor.execute("""
                    UPDATE classes 
                    SET class_name=?, class_type=?, description=?, class_date=?, class_time=?,
                        duration_minutes=?, capacity=?, status=?, updated_at=CURRENT_TIMESTAMP
                    WHERE class_id=?
                """, update_data)
                
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Class updated successfully!")
                dialog.destroy()
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to update class: {str(e)}")
        
        tk.Button(button_frame, text="Save Changes", command=save_changes,
                 bg='#3498db', fg='white', width=15, font=('Arial', 11)).pack(side='left', padx=10)
        tk.Button(button_frame, text="Cancel", command=dialog.destroy,
                 bg='#95a5a6', fg='white', width=15, font=('Arial', 11)).pack(side='right', padx=10)
    
    def view_class_details(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT c.*, b.branch_name, z.zone_name, s.first_name || ' ' || s.last_name as trainer_name,
                   (SELECT COUNT(*) FROM class_registrations WHERE class_id = c.class_id) as enrolled_count
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
        dialog.geometry("600x550")
        dialog.configure(bg='#ecf0f1')
        
        # Center dialog
        dialog.update_idletasks()
        width = dialog.winfo_width()
        height = dialog.winfo_height()
        x = (dialog.winfo_screenwidth() // 2) - (width // 2)
        y = (dialog.winfo_screenheight() // 2) - (height // 2)
        dialog.geometry(f'{width}x{height}+{x}+{y}')
        
        # Create notebook for tabs
        notebook = ttk.Notebook(dialog)
        notebook.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Tab 1: Class Details
        details_frame = tk.Frame(notebook, bg='white', padx=20, pady=20)
        notebook.add(details_frame, text="Class Details")
        
        # Display class details in a grid
        details = [
            ("Class Name:", cls[1]),
            ("Type:", cls[2]),
            ("Description:", cls[3] if cls[3] else "N/A"),
            ("Trainer:", cls[15]),
            ("Branch:", cls[13]),
            ("Zone:", cls[14]),
            ("Date:", cls[4]),
            ("Time:", cls[5]),
            ("Duration:", f"{cls[6]} minutes"),
            ("Capacity:", f"{cls[7]} people"),
            ("Enrolled:", f"{cls[16]} members"),
            ("Status:", cls[11]),
            ("Created:", cls[9]),
            ("Last Updated:", cls[10] if cls[10] else "N/A")
        ]
        
        for i, (label, value) in enumerate(details):
            tk.Label(details_frame, text=label, font=('Arial', 11, 'bold'), 
                    bg='white').grid(row=i, column=0, sticky='w', pady=5)
            tk.Label(details_frame, text=value, bg='white', font=('Arial', 11)
                    ).grid(row=i, column=1, sticky='w', pady=5, padx=(10, 0))
        
        # Tab 2: Participants
        participants_frame = tk.Frame(notebook, bg='white', padx=10, pady=10)
        notebook.add(participants_frame, text="Participants")
        
        # Participants tree
        tree_frame = tk.Frame(participants_frame, bg='white')
        tree_frame.pack(fill='both', expand=True)
        
        participant_tree = ttk.Treeview(tree_frame, columns=('ID', 'Name', 'Email', 'Phone', 'Registered'),
                                       show='headings', height=10)
        
        p_columns = [('ID', 50), ('Name', 150), ('Email', 150), ('Phone', 100), ('Registered', 100)]
        for col, width in p_columns:
            participant_tree.heading(col, text=col)
            participant_tree.column(col, width=width, anchor='center')
        
        p_scrollbar = ttk.Scrollbar(tree_frame, orient='vertical', command=participant_tree.yview)
        participant_tree.configure(yscrollcommand=p_scrollbar.set)
        p_scrollbar.pack(side='right', fill='y')
        participant_tree.pack(side='left', fill='both', expand=True)
        
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
        
        for p in participants:
            participant_tree.insert('', 'end', values=p)
        
        conn.close()
        
        # Tab 3: Attendance
        attendance_frame = tk.Frame(notebook, bg='white', padx=10, pady=10)
        notebook.add(attendance_frame, text="Attendance")
        
        tk.Label(attendance_frame, text="Mark Attendance", font=('Arial', 14, 'bold'),
                bg='white').pack(pady=10)
        
        # Attendance tree
        att_tree_frame = tk.Frame(attendance_frame, bg='white')
        att_tree_frame.pack(fill='both', expand=True)
        
        attendance_tree = ttk.Treeview(att_tree_frame, columns=('ID', 'Name', 'Status'),
                                       show='headings', height=10)
        
        attendance_tree.heading('ID', text='ID')
        attendance_tree.heading('Name', text='Member Name')
        attendance_tree.heading('Status', text='Attendance Status')
        
        attendance_tree.column('ID', width=50, anchor='center')
        attendance_tree.column('Name', width=200, anchor='w')
        attendance_tree.column('Status', width=100, anchor='center')
        
        att_scrollbar = ttk.Scrollbar(att_tree_frame, orient='vertical', command=attendance_tree.yview)
        attendance_tree.configure(yscrollcommand=att_scrollbar.set)
        att_scrollbar.pack(side='right', fill='y')
        attendance_tree.pack(side='left', fill='both', expand=True)
        
        # Load participants for attendance
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT m.member_id, m.first_name || ' ' || m.last_name,
                   CASE WHEN a.attendance_id IS NOT NULL THEN 'Present' ELSE 'Absent' END as status
            FROM class_registrations cr
            JOIN members m ON cr.member_id = m.member_id
            LEFT JOIN attendance a ON a.member_id = m.member_id 
                AND DATE(a.check_in) = c.class_date
            CROSS JOIN classes c
            WHERE cr.class_id = ? AND c.class_id = ?
            ORDER BY m.first_name
        """, (class_id, class_id))
        
        attendance_records = cursor.fetchall()
        
        for record in attendance_records:
            attendance_tree.insert('', 'end', values=record)
        
        conn.close()
        
        # Close button
        tk.Button(dialog, text="Close", command=dialog.destroy,
                 bg='#95a5a6', fg='white', font=('Arial', 11),
                 padx=30, pady=5).pack(pady=10)
    
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
                
                cursor.execute("UPDATE classes SET status = 'Cancelled' WHERE class_id = ?", (class_id,))
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Class cancelled successfully")
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to cancel class: {str(e)}")
    
    def complete_class(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        if messagebox.askyesno("Confirm Completion", 
                              f"Are you sure you want to mark '{class_name}' as completed?"):
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                cursor.execute("UPDATE classes SET status = 'Completed' WHERE class_id = ?", (class_id,))
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Class marked as completed successfully")
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to complete class: {str(e)}")
    
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
        
        # Center dialog
        dialog.update_idletasks()
        width = dialog.winfo_width()
        height = dialog.winfo_height()
        x = (dialog.winfo_screenwidth() // 2) - (width // 2)
        y = (dialog.winfo_screenheight() // 2) - (height // 2)
        dialog.geometry(f'{width}x{height}+{x}+{y}')
        
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
        conn.close()
        
        for participant in participants:
            tree.insert('', 'end', values=participant)
        
        # Action buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=10)
        button_frame.pack(fill='x')
        
        tk.Button(button_frame, text="Export to CSV", 
                 command=lambda: self.export_participants(participants, class_name),
                 bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Send Email to All", 
                 command=lambda: self.notify_participants(class_id),
                 bg='#2ecc71', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Close", command=dialog.destroy,
                 bg='#95a5a6', fg='white', padx=20, pady=5).pack(side='right', padx=10)
    
    def mark_attendance(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Mark Attendance - {class_name}")
        dialog.geometry("500x400")
        dialog.configure(bg='#ecf0f1')
        
        tk.Label(dialog, text=f"Mark Attendance for {class_name}", 
                font=('Arial', 16, 'bold'), bg='#ecf0f1').pack(pady=20)
        
        # Attendance frame
        att_frame = tk.Frame(dialog, bg='white', padx=20, pady=20)
        att_frame.pack(fill='both', expand=True, padx=20, pady=10)
        
        # Get participants
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("""
            SELECT m.member_id, m.first_name || ' ' || m.last_name
            FROM class_registrations cr
            JOIN members m ON cr.member_id = m.member_id
            WHERE cr.class_id = ?
            ORDER BY m.first_name
        """, (class_id,))
        
        participants = cursor.fetchall()
        conn.close()
        
        if not participants:
            messagebox.showinfo("Info", "No participants registered for this class")
            dialog.destroy()
            return
        
        # Create checkboxes for each participant
        attendance_vars = {}
        
        canvas = tk.Canvas(att_frame, bg='white', highlightthickness=0)
        scrollbar = ttk.Scrollbar(att_frame, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='white')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        for i, (member_id, member_name) in enumerate(participants):
            var = tk.BooleanVar()
            attendance_vars[member_id] = var
            tk.Checkbutton(scrollable_frame, text=member_name, variable=var,
                          bg='white', font=('Arial', 11)).pack(anchor='w', pady=5)
        
        def save_attendance():
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                class_date = datetime.now().strftime('%Y-%m-%d')
                
                for member_id, var in attendance_vars.items():
                    if var.get():  # Member is present
                        # Check if attendance already recorded
                        cursor.execute("""
                            SELECT attendance_id FROM attendance 
                            WHERE member_id = ? AND DATE(check_in) = ?
                        """, (member_id, class_date))
                        
                        if not cursor.fetchone():
                            # Get duration from class
                            duration = self.tree.item(selection[0])['values'][8].replace(' mins', '')
                            
                            # Record attendance
                            cursor.execute("""
                                INSERT INTO attendance (member_id, check_in, check_out, duration_minutes, zone_id)
                                VALUES (?, ?, ?, ?, ?)
                            """, (member_id, 
                                  datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                                  datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
                                  duration,
                                  None))
                
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", "Attendance marked successfully!")
                dialog.destroy()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to save attendance: {str(e)}")
        
        # Buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(fill='x')
        
        tk.Button(button_frame, text="Save Attendance", command=save_attendance,
                 bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Mark All", 
                 command=lambda: [v.set(True) for v in attendance_vars.values()],
                 bg='#2ecc71', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Clear All", 
                 command=lambda: [v.set(False) for v in attendance_vars.values()],
                 bg='#e74c3c', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Close", command=dialog.destroy,
                 bg='#95a5a6', fg='white', padx=20, pady=5).pack(side='right', padx=10)
    
    def register_member_dialog(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        enrolled = int(self.tree.item(selection[0])['values'][10])
        capacity = int(self.tree.item(selection[0])['values'][9])
        
        if enrolled >= capacity:
            messagebox.showwarning("Class Full", "This class has reached maximum capacity")
            return
        
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Register Member - {class_name}")
        dialog.geometry("400x300")
        dialog.configure(bg='#ecf0f1')
        
        tk.Label(dialog, text=f"Register Member for {class_name}", 
                font=('Arial', 14, 'bold'), bg='#ecf0f1').pack(pady=20)
        
        # Get class details
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        cursor.execute("SELECT branch_id, zone_id FROM classes WHERE class_id = ?", (class_id,))
        branch_id, zone_id = cursor.fetchone()
        
        # Get available members (not already registered)
        cursor.execute("""
            SELECT member_id, first_name || ' ' || last_name as name
            FROM members 
            WHERE branch_id = ? AND zone_id = ? AND status = 'Active'
            AND member_id NOT IN (
                SELECT member_id FROM class_registrations WHERE class_id = ?
            )
            ORDER BY first_name
        """, (branch_id, zone_id, class_id))
        
        available_members = cursor.fetchall()
        conn.close()
        
        if not available_members:
            messagebox.showinfo("Info", "No available members to register")
            dialog.destroy()
            return
        
        # Member selection
        tk.Label(dialog, text="Select Member:", bg='#ecf0f1').pack(pady=10)
        
        member_var = tk.StringVar()
        member_combo = ttk.Combobox(dialog, textvariable=member_var, 
                                    values=[m[1] for m in available_members],
                                    state='readonly', width=30)
        member_combo.pack(pady=10)
        
        # Member to ID mapping
        member_dict = {name: mid for mid, name in available_members}
        
        def register():
            selected_name = member_var.get()
            if not selected_name:
                messagebox.showerror("Error", "Please select a member")
                return
            
            member_id = member_dict[selected_name]
            
            try:
                conn = self.db.get_connection()
                cursor = conn.cursor()
                
                cursor.execute("""
                    INSERT INTO class_registrations (class_id, member_id)
                    VALUES (?, ?)
                """, (class_id, member_id))
                
                conn.commit()
                conn.close()
                
                messagebox.showinfo("Success", f"{selected_name} registered successfully!")
                dialog.destroy()
                self.load_classes()
                
            except Exception as e:
                messagebox.showerror("Error", f"Failed to register member: {str(e)}")
        
        # Buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(fill='x')
        
        tk.Button(button_frame, text="Register", command=register,
                 bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Cancel", command=dialog.destroy,
                 bg='#95a5a6', fg='white', padx=20, pady=5).pack(side='right', padx=10)
    
    def notify_participants(self, class_id=None):
        if not class_id:
            selection = self.tree.selection()
            if not selection:
                messagebox.showwarning("Warning", "Please select a class first")
                return
            class_id = self.tree.item(selection[0])['values'][0]
        
        class_name = self.tree.item(selection[0])['values'][1]
        
        dialog = tk.Toplevel(self.parent)
        dialog.title(f"Notify Participants - {class_name}")
        dialog.geometry("500x400")
        dialog.configure(bg='#ecf0f1')
        
        tk.Label(dialog, text=f"Send Notification for {class_name}", 
                font=('Arial', 14, 'bold'), bg='#ecf0f1').pack(pady=20)
        
        # Notification type
        tk.Label(dialog, text="Notification Type:", bg='#ecf0f1').pack(pady=10)
        type_var = tk.StringVar(value="Email")
        type_combo = ttk.Combobox(dialog, textvariable=type_var,
                                  values=['Email', 'SMS', 'Both'], state='readonly', width=20)
        type_combo.pack(pady=10)
        
        # Subject
        tk.Label(dialog, text="Subject:", bg='#ecf0f1').pack(pady=10)
        subject_entry = tk.Entry(dialog, width=40)
        subject_entry.insert(0, f"Update regarding {class_name}")
        subject_entry.pack(pady=10)
        
        # Message
        tk.Label(dialog, text="Message:", bg='#ecf0f1').pack(pady=10)
        message_text = tk.Text(dialog, height=8, width=50)
        message_text.insert('1.0', f"Dear Member,\n\nThis is a notification regarding {class_name} scheduled on {self.tree.item(selection[0])['values'][6]} at {self.tree.item(selection[0])['values'][7]}.\n\nThank you for your participation.\n\nM9 Fitness Team")
        message_text.pack(pady=10)
        
        def send():
            notification_type = type_var.get()
            subject = subject_entry.get()
            message = message_text.get('1.0', tk.END).strip()
            
            # Get participants
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            cursor.execute("""
                SELECT m.email, m.phone, m.first_name || ' ' || m.last_name
                FROM class_registrations cr
                JOIN members m ON cr.member_id = m.member_id
                WHERE cr.class_id = ?
            """, (class_id,))
            
            participants = cursor.fetchall()
            conn.close()
            
            if not participants:
                messagebox.showinfo("Info", "No participants to notify")
                dialog.destroy()
                return
            
            # Simulate sending notifications
            if notification_type in ['Email', 'Both']:
                # In a real app, you'd integrate with an email service
                print(f"Sending email to {len(participants)} participants")
            
            if notification_type in ['SMS', 'Both']:
                # In a real app, you'd integrate with an SMS service
                print(f"Sending SMS to {len(participants)} participants")
            
            messagebox.showinfo("Success", f"Notification sent to {len(participants)} participants")
            dialog.destroy()
        
        # Buttons
        button_frame = tk.Frame(dialog, bg='#ecf0f1', pady=20)
        button_frame.pack(fill='x')
        
        tk.Button(button_frame, text="Send", command=send,
                 bg='#3498db', fg='white', padx=20, pady=5).pack(side='left', padx=10)
        tk.Button(button_frame, text="Cancel", command=dialog.destroy,
                 bg='#95a5a6', fg='white', padx=20, pady=5).pack(side='right', padx=10)
    
    def export_class_data(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showwarning("Warning", "Please select a class first")
            return
        
        class_id = self.tree.item(selection[0])['values'][0]
        class_name = self.tree.item(selection[0])['values'][1]
        
        from tkinter import filedialog
        import csv
        
        filename = filedialog.asksaveasfilename(
            defaultextension=".csv",
            filetypes=[("CSV files", "*.csv"), ("All files", "*.*")],
            initialfile=f"{class_name}_data.csv"
        )
        
        if not filename:
            return
        
        try:
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            # Get class details
            cursor.execute("SELECT * FROM classes WHERE class_id = ?", (class_id,))
            class_details = cursor.fetchone()
            
            # Get participants
            cursor.execute("""
                SELECT m.member_id, m.first_name, m.last_name, m.email, m.phone, cr.registration_date
                FROM class_registrations cr
                JOIN members m ON cr.member_id = m.member_id
                WHERE cr.class_id = ?
            """, (class_id,))
            
            participants = cursor.fetchall()
            conn.close()
            
            with open(filename, 'w', newline='', encoding='utf-8') as file:
                writer = csv.writer(file)
                
                # Write class info
                writer.writerow(['Class Information'])
                writer.writerow(['Class ID', class_details[0]])
                writer.writerow(['Class Name', class_details[1]])
                writer.writerow(['Type', class_details[2]])
                writer.writerow(['Date', class_details[4]])
                writer.writerow(['Time', class_details[5]])
                writer.writerow(['Duration', f"{class_details[6]} minutes"])
                writer.writerow(['Capacity', class_details[7]])
                writer.writerow(['Status', class_details[11]])
                writer.writerow([])
                
                # Write participants
                writer.writerow(['Participants'])
                writer.writerow(['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Registration Date'])
                writer.writerows(participants)
            
            messagebox.showinfo("Success", f"Data exported to {filename}")
            
        except Exception as e:
            messagebox.showerror("Error", f"Failed to export data: {str(e)}")
    
    def export_participants(self, participants, class_name):
        from tkinter import filedialog
        import csv
        
        filename = filedialog.asksaveasfilename(
            defaultextension=".csv",
            filetypes=[("CSV files", "*.csv"), ("All files", "*.*")],
            initialfile=f"{class_name}_participants.csv"
        )
        
        if filename:
            try:
                with open(filename, 'w', newline='', encoding='utf-8') as file:
                    writer = csv.writer(file)
                    writer.writerow(['ID', 'Name', 'Email', 'Phone', 'Registration Date'])
                    writer.writerows(participants)
                messagebox.showinfo("Success", f"Participants exported to {filename}")
            except Exception as e:
                messagebox.showerror("Error", f"Failed to export: {str(e)}")
    
    def pick_date(self, entry):
        from tkinter import simpledialog
        date_str = simpledialog.askstring("Enter Date", "Enter date (YYYY-MM-DD):",
                                         initialvalue=datetime.now().strftime('%Y-%m-%d'))
        if date_str:
            try:
                datetime.strptime(date_str, '%Y-%m-%d')
                entry.delete(0, tk.END)
                entry.insert(0, date_str)
            except ValueError:
                messagebox.showerror("Error", "Invalid date format. Use YYYY-MM-DD")
    
    def pick_time(self, entry):
        from tkinter import simpledialog
        time_str = simpledialog.askstring("Enter Time", "Enter time (HH:MM):",
                                         initialvalue="09:00")
        if time_str:
            try:
                datetime.strptime(time_str, '%H:%M')
                entry.delete(0, tk.END)
                entry.insert(0, time_str)
            except ValueError:
                messagebox.showerror("Error", "Invalid time format. Use HH:MM")