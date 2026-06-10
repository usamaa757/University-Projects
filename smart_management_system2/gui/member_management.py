import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import bcrypt
import database

class MemberManagement:
    def __init__(self, parent, db):
        self.parent = parent
        self.db = db
        self.current_member = None
        self.zone_combo_widget = None
        
        self.setup_ui()
        self.load_members()
    
    def setup_ui(self):
        # Main container
        main_frame = tk.Frame(self.parent, bg='#ecf0f1')
        main_frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Header
        header_frame = tk.Frame(main_frame, bg='#ecf0f1')
        header_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(header_frame, text="Member Management", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(side='left')
        
        # Add new member button
        add_btn = tk.Button(header_frame, text="➕ Add New Member", 
                        font=('Arial', 11, 'bold'), bg='#2ecc71', fg='white',
                        padx=20, pady=8, cursor='hand2', command=self.add_new_member)
        add_btn.pack(side='right', padx=(0, 10))
        
        # Search frame
        search_frame = tk.Frame(main_frame, bg='white', padx=15, pady=15)
        search_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(search_frame, text="Search:", font=('Arial', 11), 
                bg='white').grid(row=0, column=0, padx=(0, 10))
        
        self.search_var = tk.StringVar()
        search_entry = ttk.Entry(search_frame, textvariable=self.search_var, width=40)
        search_entry.grid(row=0, column=1, padx=(0, 20))
        search_entry.bind('<KeyRelease>', self.on_search)
        
        tk.Button(search_frame, text="Search", bg='#3498db', fg='white',
                padx=15, command=self.search_members).grid(row=0, column=2, padx=(0, 10))
        
        # Filter by membership type
        tk.Label(search_frame, text="Filter by:", font=('Arial', 11), 
                bg='white').grid(row=0, column=3, padx=(20, 10))
        
        self.filter_var = tk.StringVar(value="All")
        filter_combo = ttk.Combobox(search_frame, textvariable=self.filter_var,
                                values=["All", "Regular", "Premium", "Trial", "Active", "Expired"],
                                state="readonly", width=15)
        filter_combo.grid(row=0, column=4, padx=(0, 10))
        filter_combo.bind('<<ComboboxSelected>>', self.apply_filter)
        
        # Create a paned window to split treeview and buttons
        paned_window = ttk.PanedWindow(main_frame, orient='vertical')
        paned_window.pack(fill='both', expand=True)
        
        # Top frame for treeview
        top_frame = tk.Frame(paned_window)
        paned_window.add(top_frame, weight=3)  # Treeview takes 3/4 of space
        
        # Treeview for members list
        tree_frame = tk.Frame(top_frame)
        tree_frame.pack(fill='both', expand=True)
        
        # Scrollbars
        tree_scroll_y = ttk.Scrollbar(tree_frame)
        tree_scroll_y.pack(side='right', fill='y')
        
     
        # Treeview
        columns = ('ID', 'Name', 'Email', 'Phone', 'Type', 'Plan', 'Join Date', 'Expiry', 'Status')
        self.tree = ttk.Treeview(tree_frame, columns=columns, 
                                yscrollcommand=tree_scroll_y.set,
                                selectmode='browse', height=15)  # Reduced height
        
        # Configure scrollbars
        tree_scroll_y.config(command=self.tree.yview)
        
        # Define columns
        self.tree.column('#0', width=0, stretch=False)
        for col in columns:
            self.tree.column(col, anchor='center', width=100)
        
        # Define headings
        for col in columns:
            self.tree.heading(col, text=col)
        
        self.tree.pack(fill='both', expand=True)
        
        # Bind selection event
        self.tree.bind('<<TreeviewSelect>>', self.on_member_select)
        
        # Bottom frame for buttons
        bottom_frame = tk.Frame(paned_window, bg='#ecf0f1')
        paned_window.add(bottom_frame, weight=1)  # Buttons take 1/4 of space
        
        # Action buttons in bottom frame
        self.create_action_buttons(bottom_frame)
    
    def create_action_buttons(self, parent):
        """Create compact action buttons"""
        # Container for buttons
        button_container = tk.Frame(parent, bg='#ecf0f1')
        button_container.pack(fill='both', expand=True, pady=10)
        
        # Create a canvas for horizontal scrolling
        canvas = tk.Canvas(button_container, bg='#ecf0f1', height=50, highlightthickness=0)
        canvas.pack(side='top', fill='x')
        
      
    
        # Frame inside canvas to hold buttons
        buttons_frame = tk.Frame(canvas, bg='#ecf0f1')
        canvas.create_window((0, 0), window=buttons_frame, anchor='nw')
        
        # Define buttons (reduced number to 6 main ones)
        buttons = [
            ("👁️ View", self.view_member_details, '#3498db'),
            ("✏️ Edit", self.edit_member, '#f39c12'),
            ("✅ Renew", self.renew_membership, '#2ecc71'),
            ("📋 Attendance", self.view_attendance, '#9b59b6'),
            ("🧾 Invoice", self.generate_invoice, '#1abc9c'),
            ("❌ Deactivate", self.deactivate_member, '#e74c3c'),
        ]
        
        # Create buttons
        for text, command, color in buttons:
            btn = tk.Button(buttons_frame, text=text, bg=color, fg='white',
                        font=('Arial', 10, 'bold'), padx=20, pady=6,
                        cursor='hand2', command=command)
            btn.pack(side='left', padx=8)
        
        # Update scroll region after buttons are added
        buttons_frame.update_idletasks()
        canvas.configure(scrollregion=canvas.bbox('all'))
        
   
        
        # Also add keyboard navigation
        def on_left_arrow(event):
            canvas.xview_scroll(-1, 'units')
        
        def on_right_arrow(event):
            canvas.xview_scroll(1, 'units')
        
        canvas.bind_all('<Left>', on_left_arrow)
        canvas.bind_all('<Right>', on_right_arrow)
    
    def load_members(self, search_term="", filter_type="All"):
        # Clear existing items
        for item in self.tree.get_children():
            self.tree.delete(item)
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Build query
        query = """
            SELECT member_id, first_name, last_name, email, phone, 
                membership_type, subscription_plan, join_date, 
                expiry_date, status 
            FROM members 
            WHERE 1=1
        """
        params = []
        
        if search_term:
            query += " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)"
            search_pattern = f"%{search_term}%"
            params.extend([search_pattern] * 4)
        
        if filter_type != "All":
            if filter_type in ["Regular", "Premium", "Trial"]:
                query += " AND membership_type = ?"
                params.append(filter_type)
            elif filter_type == "Active":
                query += " AND status = 'Active'"
            elif filter_type == "Expired":
                query += " AND (status = 'Expired' OR expiry_date < date('now'))"
        
        query += " ORDER BY join_date DESC"
        
        cursor.execute(query, params)
        members = cursor.fetchall()
        conn.close()
        
        # Insert into treeview
        for member in members:
            full_name = f"{member[1]} {member[2]}"
            join_date_str = member[7]
            expiry_date_str = member[8]
            
            # Parse expiry date
            if expiry_date_str:
                expiry_date = datetime.strptime(expiry_date_str, '%Y-%m-%d')
                expiry_display = expiry_date.strftime('%Y-%m-%d')
            else:
                expiry_date = None
                expiry_display = "-"
            
            # Calculate dynamic status
            status = member[9]
            if expiry_date and expiry_date < datetime.now():
                status = "Expired"
            elif status != "Expired":
                status = "Active"
            
            # Insert into treeview
            self.tree.insert('', 'end', values=(
                member[0], full_name, member[3], member[4], 
                member[5], member[6], join_date_str or "-", expiry_display, status
            ))
    
    def on_search(self, event=None):
        search_term = self.search_var.get()
        filter_type = self.filter_var.get()
        self.load_members(search_term, filter_type)
    
    def search_members(self):
        self.on_search()
    
    def apply_filter(self, event=None):
        self.on_search()
    
    def on_member_select(self, event):
        selection = self.tree.selection()
        if selection:
            item = self.tree.item(selection[0])
            member_id = item['values'][0]
            self.load_member_details(member_id)
    
    def load_member_details(self, member_id):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM members WHERE member_id = ?", (member_id,))
        member_data = cursor.fetchone()
        
        if member_data:
            # Get column names
            columns = [desc[0] for desc in cursor.description]
            self.current_member = dict(zip(columns, member_data))
        conn.close()
    
    def add_new_member(self):
        self.show_member_form()
    
    def view_member_details(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return
        self.show_member_form(view_only=True)
    
    def edit_member(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return
        self.show_member_form(edit_mode=True)
    
    def show_member_form(self, edit_mode=False, view_only=False):
        # Create a new top-level window
        form_window = tk.Toplevel(self.parent)
        form_window.title("Member Details" if view_only else "Edit Member" if edit_mode else "Add New Member")
        form_window.geometry("600x900")
        form_window.configure(bg='#ecf0f1')
        
        form_window.transient(self.parent)
        form_window.grab_set()
        
        # Main container
        main_container = tk.Frame(form_window, bg='#ecf0f1')
        main_container.pack(fill='both', expand=True, padx=30, pady=30)
        
        # Title
        tk.Label(main_container, text="Member Information",
                font=('Arial', 18, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=(0, 20))
        
        # Scrollable form
        form_frame = tk.Frame(main_container, bg='#ecf0f1')
        form_frame.pack(fill='both', expand=True)
        
        canvas = tk.Canvas(form_frame, bg='#ecf0f1', highlightthickness=0)
        scrollbar = ttk.Scrollbar(form_frame, orient="vertical", command=canvas.yview)
        scrollable_frame = tk.Frame(canvas, bg='#ecf0f1')
        
        scrollable_frame.bind(
            "<Configure>",
            lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
        )
        
        canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")
        canvas.configure(yscrollcommand=scrollbar.set)
        
        fields = [
            ("First Name:", "first_name", "entry"),
            ("Last Name:", "last_name", "entry"),
            ("Email:", "email", "entry"),
            ("Password:", "password", "password"),
            ("Phone:", "phone", "entry"),
            ("Date of Birth:", "date_of_birth", "date"),
            ("Gender:", "gender", "combo", ["Male", "Female", "Other"]),
            ("Address:", "address", "text"),
            ("City:", "city", "entry"),
            ("Emergency Contact:", "emergency_contact", "entry"),
            ("Medical Conditions:", "medical_conditions", "text"),
            ("Fitness Goals:", "fitness_goals", "text"),
            ("Membership Type:", "membership_type", "combo", ["Regular", "Premium", "Trial"]),
            ("Subscription Plan:", "subscription_plan", "combo", ["Monthly", "Quarterly", "Annual"]),
            ("Payment Method:", "payment_method", "combo", ["Cash", "Card", "Online Banking"]),
            ("Branch:", "branch_id", "combo", self.db.get_branch_names()),
            ("Workout Zone:", "zone_id", "combo", [])
        ]
        
        self.form_vars = {}
        row = 0
        
        for label_text, field_name, field_type, *options in fields:
            tk.Label(scrollable_frame, text=label_text, font=('Arial', 11),
                    bg='#ecf0f1', anchor='w').grid(row=row, column=0, sticky='w', pady=5, padx=(0, 10))
            
            if field_type == "entry":
                var = tk.StringVar()
                entry = ttk.Entry(scrollable_frame, textvariable=var, width=40)
                entry.grid(row=row, column=1, pady=5, sticky='w')
                self.form_vars[field_name] = var
            
            elif field_type == "password":
                var = tk.StringVar()
                entry = ttk.Entry(scrollable_frame, textvariable=var, width=40, show="*")
                entry.grid(row=row, column=1, pady=5, sticky='w')
                self.form_vars[field_name] = var
                
                show_btn = tk.Button(scrollable_frame, text="👁", 
                                    command=lambda e=entry: self.toggle_password_visibility(e))
                show_btn.grid(row=row, column=2, pady=5, padx=5)
            
            elif field_type == "date":
                var = tk.StringVar()
                date_frame = tk.Frame(scrollable_frame, bg='#ecf0f1')
                date_frame.grid(row=row, column=1, pady=5, sticky='w')
                entry = ttk.Entry(date_frame, textvariable=var, width=37)
                entry.pack(side='left')
                tk.Button(date_frame, text="📅", command=lambda v=var: self.pick_date(v)).pack(side='left', padx=5)
                self.form_vars[field_name] = var
            
            elif field_type == "combo":
                var = tk.StringVar()
                combo = ttk.Combobox(scrollable_frame, textvariable=var,
                                    values=options[0] if options else [], state='readonly', width=37)
                combo.grid(row=row, column=1, pady=5, sticky='w')
                self.form_vars[field_name] = var
                if field_name == "zone_id":
                    self.zone_combo_widget = combo 

            elif field_type == "text":
                text = tk.Text(scrollable_frame, height=3, width=30)
                text.grid(row=row, column=1, pady=5, sticky='w')
                self.form_vars[field_name] = text
            
            row += 1
        
        # Update zone combo when branch changes
        branch_var = self.form_vars['branch_id']
        def on_branch_change(event):
            branch_name = branch_var.get()
            branch_id = self.db.get_branch_id_by_name(branch_name)
            zones = self.db.get_zones_by_branch(branch_id)
            zone_names = [z[1] for z in zones]
            if hasattr(self, 'zone_combo_widget'):
                self.zone_combo_widget['values'] = zone_names
                if zone_names:
                    self.zone_combo_widget.current(0)
        
        branch_combo_widget = scrollable_frame.grid_slaves(row=row-2, column=1)[0]
        branch_combo_widget.bind("<<ComboboxSelected>>", on_branch_change)
        
        canvas.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
        # Buttons
        button_frame = tk.Frame(main_container, bg='#ecf0f1')
        button_frame.pack(fill='x', pady=20)

        if not view_only:
            tk.Button(button_frame, text="Save", bg='#2ecc71', fg='white',
                    font=('Arial', 11, 'bold'), padx=30, pady=10,
                    command=lambda: self.save_member(edit_mode, form_window)).pack(side='left', padx=10)

        tk.Button(button_frame, text="Close", bg='#95a5a6', fg='white',
                font=('Arial', 11), padx=30, pady=10,
                command=form_window.destroy).pack(side='left', padx=10)
        
        # Fill form if editing/viewing
        if self.current_member and (edit_mode or view_only):
            self.fill_form_data()
        
        # Disable fields if view only
        if view_only:
            for widget in scrollable_frame.winfo_children():
                if isinstance(widget, (ttk.Entry, ttk.Combobox, tk.Text)):
                    widget.config(state='disabled')
            for widget in scrollable_frame.winfo_children():
                if isinstance(widget, tk.Button) and widget.cget('text') == "👁":
                    widget.config(state='disabled')
        
        canvas.update_idletasks()
        canvas.config(scrollregion=canvas.bbox("all"))

    def toggle_password_visibility(self, entry_widget):
        if entry_widget.cget('show') == '*':
            entry_widget.config(show='')
        else:
            entry_widget.config(show='*')

    def fill_form_data(self):
        if self.current_member:
            mapping = {
                'first_name': self.current_member.get('first_name', ''),
                'last_name': self.current_member.get('last_name', ''),
                'email': self.current_member.get('email', ''),
                'phone': self.current_member.get('phone', ''),
                'date_of_birth': self.current_member.get('date_of_birth', ''),
                'gender': self.current_member.get('gender', ''),
                'address': self.current_member.get('address', ''),
                'city': self.current_member.get('city', ''),
                'emergency_contact': self.current_member.get('emergency_contact', ''),
                'medical_conditions': self.current_member.get('medical_conditions', ''),
                'fitness_goals': self.current_member.get('fitness_goals', ''),
                'membership_type': self.current_member.get('membership_type', 'Regular'),
                'subscription_plan': self.current_member.get('subscription_plan', 'Monthly'),
                'payment_method': self.current_member.get('payment_method', 'Cash'),
            }

            for field_name, value in mapping.items():
                widget = self.form_vars.get(field_name)
                if widget:
                    if isinstance(widget, tk.StringVar):
                        widget.set(value)
                    elif isinstance(widget, tk.Text):
                        widget.delete('1.0', tk.END)
                        widget.insert('1.0', value)

            if 'password' in self.form_vars:
                self.form_vars['password'].set('')

            branch_id = self.current_member.get('branch_id')
            if branch_id:
                branch_row = self.db.get_branch_by_id(branch_id)
                if branch_row:
                    branch_name = branch_row[1]
                    self.form_vars['branch_id'].set(branch_name)

                    zone_id = self.current_member.get('zone_id')
                    if branch_id:
                        zones = self.db.get_zones_by_branch(branch_id)
                        zone_names = [z[1] for z in zones]

                        if hasattr(self, 'zone_combo_widget'):
                            self.zone_combo_widget['values'] = zone_names

                            if zone_id:
                                zone_row = self.db.get_zone_by_id(zone_id)
                                if zone_row:
                                    zone_name = zone_row[1]
                                    self.form_vars['zone_id'].set(zone_name)

    def hash_password(self, plain_password):
        if plain_password:
            salt = bcrypt.gensalt()
            hashed = bcrypt.hashpw(plain_password.encode('utf-8'), salt)
            return hashed.decode('utf-8')
        return None

    def pick_date(self, var):
        from datetime import datetime
        var.set(datetime.now().strftime('%Y-%m-%d'))

    def save_member(self, edit_mode, window):
        member_data = {}

        for field_name, widget in self.form_vars.items():
            if isinstance(widget, tk.StringVar):
                member_data[field_name] = widget.get().strip()
            elif isinstance(widget, tk.Text):
                member_data[field_name] = widget.get("1.0", "end").strip()

        required_fields = ["first_name", "last_name", "email"]
        if not edit_mode:
            required_fields.append("password")
        
        for field in required_fields:
            if not member_data.get(field):
                messagebox.showerror("Error", f"{field.replace('_', ' ').title()} is required!")
                return

        if 'join_date' not in member_data or not member_data['join_date']:
            member_data['join_date'] = datetime.now().strftime('%Y-%m-%d')

        join_date = datetime.strptime(member_data['join_date'], '%Y-%m-%d')

        plan = member_data.get('subscription_plan', 'Monthly')
        if plan == "Monthly":
            expiry_date = join_date + timedelta(days=30)
        elif plan == "Quarterly":
            expiry_date = join_date + timedelta(days=90)
        elif plan == "Annual":
            expiry_date = join_date + timedelta(days=365)
        else:
            expiry_date = join_date + timedelta(days=30)

        member_data['expiry_date'] = expiry_date.strftime('%Y-%m-%d')

        try:
            conn = self.db.get_connection()
            cursor = conn.cursor()

            # Resolve branch_id and zone_id
            branch_name = member_data.pop('branch_id')
            branch_id = self.db.get_branch_id_by_name(branch_name)
            member_data['branch_id'] = branch_id

            zone_name = member_data.pop('zone_id')
            zone_id = self.db.get_zone_id_by_name_and_branch(zone_name, branch_id)
            member_data['zone_id'] = zone_id

            # Handle password hashing
            plain_password = member_data.pop('password', None)
            
            if edit_mode and self.current_member:
                if plain_password:
                    hashed_password = self.hash_password(plain_password)
                    member_data['password_hash'] = hashed_password
                
                set_clause = ', '.join([f"{key} = ?" for key in member_data.keys()])
                values = list(member_data.values()) + [self.current_member["member_id"]]
                cursor.execute(f"UPDATE members SET {set_clause} WHERE member_id = ?", values)
                messagebox.showinfo("Success", "Member updated successfully!")
            else:
                if plain_password:
                    hashed_password = self.hash_password(plain_password)
                    member_data['password_hash'] = hashed_password
                
                columns = ', '.join(member_data.keys())
                placeholders = ', '.join(['?' for _ in member_data])
                cursor.execute(f"INSERT INTO members ({columns}) VALUES ({placeholders})", list(member_data.values()))
                
                # Get the new member ID
                member_id = cursor.lastrowid
                
                # Create initial payment
                self.create_initial_payment(cursor, member_id, member_data)
                
                messagebox.showinfo("Success", "Member added successfully!")

            conn.commit()
            self.load_members()
            window.destroy()

        except Exception as e:
            conn.rollback()
            messagebox.showerror("Database Error", f"Error saving member: {str(e)}")
        finally:
            conn.close()

    def create_initial_payment(self, cursor, member_id, member_data):
        from datetime import datetime
        import random
        
        # Calculate amount based on membership type and plan
        amount = 0
        membership_type = member_data.get('membership_type', 'Regular')
        if membership_type == "Premium":
            amount = 5000
        elif membership_type == "Regular":
            amount = 3000
        elif membership_type == "Trial":
            amount = 0
        
        # Adjust for subscription plan
        plan = member_data.get('subscription_plan', 'Monthly')
        if plan == "Quarterly":
            amount *= 3
        elif plan == "Annual":
            amount *= 12
        
        payment_data = {
            'member_id': member_id,
            'amount': amount,
            'payment_date': datetime.now().strftime('%Y-%m-%d'),
            'payment_type': 'Membership',
            'payment_method': member_data.get('payment_method', 'Cash'),
            'subscription_period': member_data.get('subscription_plan', 'Monthly'),
            'discount_applied': 0,
            'final_amount': amount,
            'status': 'Completed',
            'invoice_number': f"INV-{datetime.now().strftime('%Y%m%d')}-{random.randint(1000, 9999)}"
        }
        
        columns = ', '.join(payment_data.keys())
        placeholders = ', '.join(['?' for _ in payment_data])
        
        cursor.execute(f"""
            INSERT INTO payments ({columns})
            VALUES ({placeholders})
        """, list(payment_data.values()))
    
    def renew_membership(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return
        
        from datetime import datetime, timedelta
        
        current_expiry = datetime.strptime(self.current_member['expiry_date'], '%Y-%m-%d')
        if current_expiry < datetime.now():
            new_expiry = datetime.now() + timedelta(days=30)
        else:
            new_expiry = current_expiry + timedelta(days=30)
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        try:
            cursor.execute("""
                UPDATE members 
                SET expiry_date = ?, status = 'Active'
                WHERE member_id = ?
            """, (new_expiry.strftime('%Y-%m-%d'), self.current_member['member_id']))
            
            conn.commit()
            messagebox.showinfo("Success", "Membership renewed successfully!")
            self.load_members()
            
        except Exception as e:
            conn.rollback()
            messagebox.showerror("Error", f"Failed to renew membership: {str(e)}")
        finally:
            conn.close()
    
    def deactivate_member(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return
        
        if messagebox.askyesno("Confirm Deactivation", 
                              f"Deactivate member {self.current_member['first_name']} {self.current_member['last_name']}?"):
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            try:
                cursor.execute("""
                    UPDATE members 
                    SET status = 'Inactive'
                    WHERE member_id = ?
                """, (self.current_member['member_id'],))
                
                conn.commit()
                messagebox.showinfo("Success", "Member deactivated successfully!")
                self.load_members()
                
            except Exception as e:
                conn.rollback()
                messagebox.showerror("Error", f"Failed to deactivate member: {str(e)}")
            finally:
                conn.close()
    
    def view_attendance(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return

        attendance_window = tk.Toplevel(self.parent)
        attendance_window.title(
            f"Attendance - {self.current_member['first_name']} {self.current_member['last_name']}"
        )
        attendance_window.geometry("850x450")

        zone_name = "N/A"
        member_zone_id = self.current_member.get('zone_id')

        if member_zone_id:
            zone_row = self.db.get_zone_by_id(member_zone_id)
            if zone_row:
                zone_name = zone_row[1]

        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT check_in, check_out, duration_minutes
            FROM attendance
            WHERE member_id = ?
            ORDER BY check_in DESC
            LIMIT 50
        """, (self.current_member['member_id'],))
        records = cursor.fetchall()
        conn.close()

        tree_frame = tk.Frame(attendance_window)
        tree_frame.pack(fill='both', expand=True, padx=10, pady=10)

        columns = ('Check-in', 'Check-out', 'Zone', 'Duration (mins)')
        tree = ttk.Treeview(tree_frame, columns=columns, show='headings', height=15)

        for col in columns:
            tree.heading(col, text=col)
            tree.column(col, width=180, anchor='center')

        scrollbar = ttk.Scrollbar(tree_frame, orient='vertical', command=tree.yview)
        tree.configure(yscrollcommand=scrollbar.set)

        scrollbar.pack(side='right', fill='y')
        tree.pack(fill='both', expand=True)

        total_duration = 0
        visit_count = 0

        for check_in, check_out, duration in records:
            check_out = check_out if check_out else "-"
            duration = duration if duration else 0

            tree.insert('', 'end', values=(
                check_in,
                check_out,
                zone_name,
                duration
            ))

            total_duration += duration
            visit_count += 1

        summary_frame = tk.Frame(attendance_window)
        summary_frame.pack(fill='x', padx=10, pady=10)

        avg_duration = total_duration / visit_count if visit_count else 0

        tk.Label(
            summary_frame,
            text=f"Total Visits: {visit_count} | Average Duration: {avg_duration:.1f} mins",
            font=('Arial', 11, 'bold')
        ).pack()

        button_frame = tk.Frame(attendance_window)
        button_frame.pack(fill='x', padx=10, pady=(0, 10))

        tk.Button(
            button_frame,
            text="Check-in",
            bg="#2ecc71",
            fg="white",
            font=('Arial', 11, 'bold'),
            padx=20,
            pady=8,
            command=self.add_check_in
        ).pack(side='left', padx=10)

        tk.Button(
            button_frame,
            text="Check-out",
            bg="#e67e22",
            fg="white",
            font=('Arial', 11, 'bold'),
            padx=20,
            pady=8,
            command=self.add_check_out
        ).pack(side='left', padx=10)

    def add_check_in(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return

        conn = self.db.get_connection()
        cursor = conn.cursor()

        cursor.execute("""
            SELECT * FROM attendance
            WHERE member_id = ? AND check_out IS NULL
        """, (self.current_member['member_id'],))
        open_record = cursor.fetchone()

        if open_record:
            messagebox.showwarning("Already Checked-in", "Member already has an active check-in")
            conn.close()
            return

        from datetime import datetime
        now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute("""
            INSERT INTO attendance (member_id, check_in)
            VALUES (?, ?)
        """, (self.current_member['member_id'], now))
        conn.commit()
        conn.close()
        messagebox.showinfo("Checked-in", f"{self.current_member['first_name']} checked in at {now}")
        
    def add_check_out(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return

        conn = self.db.get_connection()
        cursor = conn.cursor()

        cursor.execute("""
            SELECT attendance_id, check_in FROM attendance
            WHERE member_id = ? AND check_out IS NULL
            ORDER BY check_in DESC
            LIMIT 1
        """, (self.current_member['member_id'],))
        open_record = cursor.fetchone()

        if not open_record:
            messagebox.showwarning("No Active Check-in", "Member has not checked in yet")
            conn.close()
            return

        from datetime import datetime
        check_out_time = datetime.now()
        check_in_time = datetime.strptime(open_record[1], '%Y-%m-%d %H:%M:%S')
        duration_minutes = int((check_out_time - check_in_time).total_seconds() / 60)

        cursor.execute("""
            UPDATE attendance
            SET check_out = ?, duration_minutes = ?
            WHERE attendance_id = ?
        """, (check_out_time.strftime('%Y-%m-%d %H:%M:%S'), duration_minutes, open_record[0]))

        conn.commit()
        conn.close()

        messagebox.showinfo(
            "Checked-out",
            f"{self.current_member['first_name']} checked out at {check_out_time.strftime('%Y-%m-%d %H:%M:%S')}\n"
            f"Duration: {duration_minutes} mins"
        )

    def generate_invoice(self):
        if not self.current_member:
            messagebox.showinfo("Select Member", "Please select a member first")
            return
        
        from datetime import datetime
        import random
        
        invoice_number = f"INV-{datetime.now().strftime('%Y%m%d')}-{random.randint(1000, 9999)}"
        
        invoice_window = tk.Toplevel(self.parent)
        invoice_window.title(f"Invoice - {invoice_number}")
        invoice_window.geometry("500x600")
        
        # Calculate amount
        amount = 0
        if self.current_member.get('membership_type') == "Premium":
            amount = 5000
        elif self.current_member.get('membership_type') == "Regular":
            amount = 3000
        
        plan = self.current_member.get('subscription_plan', 'Monthly')
        if plan == "Quarterly":
            amount *= 3
        elif plan == "Annual":
            amount *= 12
        
        invoice_text = f"""
        M9 FITNESS
        ----------------------------
        Invoice: {invoice_number}
        Date: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
        
        Member: {self.current_member['first_name']} {self.current_member['last_name']}
        Email: {self.current_member['email']}
        Phone: {self.current_member['phone']}
        
        ----------------------------
        Membership Type: {self.current_member.get('membership_type', 'N/A')}
        Subscription Plan: {self.current_member.get('subscription_plan', 'N/A')}
        Period: {datetime.now().strftime('%Y-%m-%d')} to {self.current_member.get('expiry_date', 'N/A')}
        
        ----------------------------
        Amount: PKR {amount:,.2f}
        
        ----------------------------
        Payment Method: {self.current_member.get('payment_method', 'Cash')}
        Status: PAID
        
        Thank you for choosing M9 Fitness!
        """
        
        text_widget = tk.Text(invoice_window, font=('Courier', 11))
        text_widget.pack(fill='both', expand=True, padx=20, pady=20)
        text_widget.insert('1.0', invoice_text)
        text_widget.config(state='disabled')
        
        tk.Button(invoice_window, text="Save Invoice", bg='#3498db', fg='white',
                 font=('Arial', 11), padx=20, pady=5,
                 command=lambda: self.save_invoice(invoice_text)).pack(pady=10)
    
    def save_invoice(self, invoice_text):
        from datetime import datetime
        filename = f"invoice_{datetime.now().strftime('%Y%m%d_%H%M%S')}.txt"
        
        try:
            with open(filename, 'w') as f:
                f.write(invoice_text)
            messagebox.showinfo("Success", f"Invoice saved as {filename}")
        except Exception as e:
            messagebox.showerror("Error", f"Failed to save invoice: {str(e)}")