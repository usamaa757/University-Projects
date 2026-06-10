import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import database
from models.staff import Staff
import hashlib
import hashlib
def hash_password(password):
    return hashlib.sha256(password.encode()).hexdigest()
print({hash_password("abc")})


class StaffDashboard:
    def __init__(self, parent, db):
        self.parent = parent
        self.db = db
        self.current_staff = None
        self.form_vars = {}
        self.schedule_vars = {}

        self.setup_ui()
        self.load_staff()

    # ========== UTILITY METHODS ==========

    def hash_password(self, password: str) -> str:
        """Hash password using SHA-256"""
        return hashlib.sha256(password.encode("utf-8")).hexdigest()

    def pick_date(self, var):
        """Set date picker to today's date"""
        var.set(datetime.now().strftime("%Y-%m-%d"))

    # ========== UI SETUP METHODS ==========

    def setup_ui(self):
        """Setup main dashboard UI"""
        # Main container
        main_frame = tk.Frame(self.parent, bg="#ecf0f1")
        main_frame.pack(fill="both", expand=True, padx=20, pady=20)

        # Header section
        self.create_header(main_frame)

        # Filter section
        self.create_filters(main_frame)

        # Treeview for staff listing
        self.create_staff_tree(main_frame)

        # Action buttons
        self.create_action_buttons(main_frame)

    def create_header(self, parent):
        """Create dashboard header"""
        header_frame = tk.Frame(parent, bg="#ecf0f1")
        header_frame.pack(fill="x", pady=(0, 20))

        tk.Label(
            header_frame,
            text="Staff Management",
            font=("Arial", 20, "bold"),
            bg="#ecf0f1",
        ).pack(side="left")

        # Add staff button
        add_btn = tk.Button(
            header_frame,
            text="➕ Add Staff",
            font=("Arial", 11, "bold"),
            bg="#2ecc71",
            fg="white",
            padx=20,
            pady=8,
            cursor="hand2",
            command=self.add_staff,
        )
        add_btn.pack(side="right", padx=(0, 10))

    def create_filters(self, parent):
        """Create search and filter controls"""
        filter_frame = tk.Frame(parent, bg="white", padx=15, pady=15)
        filter_frame.pack(fill="x", pady=(0, 20))

        # Search field
        tk.Label(filter_frame, text="Search:", font=("Arial", 11), bg="white").grid(
            row=0, column=0, padx=(0, 10)
        )

        self.search_var = tk.StringVar()
        search_entry = ttk.Entry(filter_frame, textvariable=self.search_var, width=30)
        search_entry.grid(row=0, column=1, padx=(0, 20))
        search_entry.bind("<KeyRelease>", self.on_search)

        # Role filter
        tk.Label(filter_frame, text="Role:", font=("Arial", 11), bg="white").grid(
            row=0, column=2, padx=(20, 10)
        )

        self.role_var = tk.StringVar(value="All")
        role_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.role_var,
            values=["All", "Manager", "Trainer", "Attendant", "Nutritionist"],
            state="readonly",
            width=15,
        )
        role_combo.grid(row=0, column=3, padx=(0, 10))
        role_combo.bind("<<ComboboxSelected>>", self.load_staff)

    def create_staff_tree(self, parent):
        """Create staff listing treeview"""
        tree_frame = tk.Frame(parent)
        tree_frame.pack(fill="both", expand=True)

        # Scrollbars
        tree_scroll_y = ttk.Scrollbar(tree_frame)
        tree_scroll_y.pack(side="right", fill="y")

        tree_scroll_x = ttk.Scrollbar(tree_frame, orient="horizontal")
        tree_scroll_x.pack(side="bottom", fill="x")

        # Treeview setup
        columns = (
            "ID",
            "Name",
            "Role",
            "Email",
            "Phone",
            "Hire Date",
            "Salary",
            "Status",
        )
        self.tree = ttk.Treeview(
            tree_frame,
            columns=columns,
            yscrollcommand=tree_scroll_y.set,
            xscrollcommand=tree_scroll_x.set,
            selectmode="browse",
            height=15,
        )

        tree_scroll_y.config(command=self.tree.yview)
        tree_scroll_x.config(command=self.tree.xview)

        # Configure columns
        col_widths = [50, 150, 100, 150, 100, 100, 100, 80]
        for col, width in zip(columns, col_widths):
            self.tree.column(col, anchor="center", width=width)
            self.tree.heading(col, text=col)

        self.tree.pack(fill="both", expand=True)
        self.tree.bind("<<TreeviewSelect>>", self.on_staff_select)

    def create_action_buttons(self, parent):
        """Create action buttons bar"""
        action_frame = tk.Frame(parent, bg="#ecf0f1")
        action_frame.pack(fill="x", pady=20)

        buttons = [
            ("👁️ View Details", self.view_staff_details, "#3498db"),
            ("✏️ Edit", self.edit_staff, "#f39c12"),
            ("💰 Payroll", self.process_payroll, "#2ecc71"),
            ("📅 Schedule", self.manage_schedule, "#9b59b6"),
            ("❌ Deactivate", self.deactivate_staff, "#e74c3c"),
        ]

        for text, command, color in buttons:
            btn = tk.Button(
                action_frame,
                text=text,
                bg=color,
                fg="white",
                font=("Arial", 10),
                padx=15,
                pady=8,
                cursor="hand2",
                command=command,
            )
            btn.pack(side="left", padx=5)

    # ========== DATA LOADING METHODS ==========

    def load_staff(self, event=None):
        """Load staff data into treeview"""
        # Clear existing items
        for item in self.tree.get_children():
            self.tree.delete(item)

        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Build query
        query = """
            SELECT staff_id, first_name || ' ' || last_name, role, email, 
                   phone, hire_date, salary, status
            FROM staff
            WHERE 1=1
        """
        params = []

        # Apply search filter
        search_term = self.search_var.get()
        if search_term:
            query += " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)"
            search_pattern = f"%{search_term}%"
            params.extend(
                [search_pattern, search_pattern, search_pattern, search_pattern]
            )

        # Apply role filter
        role_filter = self.role_var.get()
        if role_filter != "All":
            query += " AND role = ?"
            params.append(role_filter)

        query += " ORDER BY hire_date DESC"

        cursor.execute(query, params)
        staff = cursor.fetchall()
        conn.close()

        # Insert into treeview
        for s in staff:
            formatted_staff = (
                s[0],
                s[1],
                s[2],
                s[3],
                s[4],
                s[5],
                s[6],
                s[7],
            )
            self.tree.insert("", "end", values=formatted_staff)

    def load_staff_details(self, staff_id):
        """Load details of selected staff member"""
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM staff WHERE staff_id = ?", (staff_id,))
        staff_data = cursor.fetchone()
        conn.close()

        if staff_data:
            columns = [desc[0] for desc in cursor.description]
            self.current_staff = dict(zip(columns, staff_data))

    # ========== EVENT HANDLERS ==========

    def on_search(self, event=None):
        """Handle search field changes"""
        self.load_staff()

    def on_staff_select(self, event):
        """Handle staff selection in treeview"""
        selection = self.tree.selection()
        if selection:
            item = self.tree.item(selection[0])
            staff_id = item["values"][0]
            self.load_staff_details(staff_id)

    # ========== STAFF FORM METHODS ==========

    def show_staff_form(self, edit_mode=False, view_only=False):
        """Show staff form for add/edit/view"""
        form_window = tk.Toplevel(self.parent)
        form_window.title(
            "Staff Details" if view_only else "Edit Staff" if edit_mode else "Add Staff"
        )
        form_window.geometry("520x620")
        form_window.configure(bg="#ecf0f1")

        form_window.transient(self.parent)
        form_window.grab_set()

        # Form container
        form_frame = tk.Frame(form_window, bg="#ecf0f1", padx=30, pady=30)
        form_frame.pack(fill="both", expand=True)

        # Form header
        tk.Label(
            form_frame,
            text="Staff Information",
            font=("Arial", 18, "bold"),
            bg="#ecf0f1",
        ).pack(anchor="w", pady=(0, 20))

        # Create form fields
        self.create_staff_form_fields(form_frame)

        # Fill form if editing/viewing
        if self.current_staff and (edit_mode or view_only):
            self.fill_staff_form()
            if hasattr(self, "branch_combo") and self.branch_combo:
                self.on_branch_change()

        # Disable fields for view-only mode
        if view_only:
            self.disable_form_fields(form_frame)

        # Add form buttons
        self.add_form_buttons(form_frame, view_only, edit_mode, form_window)

    def create_staff_form_fields(self, parent):
        """Create staff form fields"""
        fields_frame = tk.Frame(parent, bg="#ecf0f1")
        fields_frame.pack(fill="both", expand=True)

        # Load branches
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT branch_name FROM gym_branches ORDER BY branch_name")
        branch_names = [row[0] for row in cursor.fetchall()]
        conn.close()

        self.form_vars = {}
        self.branch_combo = None
        self.zone_combo = None

        # Field definitions
        fields = [
            ("First Name:", "first_name", "entry"),
            ("Last Name:", "last_name", "entry"),
            (
                "Role:",
                "role",
                "combo",
                ["Manager", "Trainer", "Attendant", "Nutritionist"],
            ),
            ("Branch:", "branch_id", "combo", branch_names),
            ("Zone:", "zone_id", "combo", []),
            ("Email:", "email", "entry"),
            ("Password:", "password", "entry"),
            ("Phone:", "phone", "entry"),
            ("Hire Date:", "hire_date", "date"),
            ("Salary (PKR):", "salary", "entry"),
            ("Specialization:", "specialization", "entry"),
            ("Status:", "status", "combo", ["Active", "Inactive", "On Leave"]),
        ]

        # Create fields
        for row, (label_text, field_name, field_type, *options) in enumerate(fields):
            # Label
            tk.Label(
                fields_frame, text=label_text, font=("Arial", 11), bg="#ecf0f1"
            ).grid(row=row, column=0, sticky="w", pady=6)

            # Field
            if field_type == "entry":
                var = tk.StringVar()
                entry = ttk.Entry(fields_frame, textvariable=var, width=30)

                if field_name == "password":
                    entry.config(show="*")

                entry.grid(row=row, column=1, pady=6, padx=(10, 0))
                self.form_vars[field_name] = var

            elif field_type == "combo":
                var = tk.StringVar()
                combo = ttk.Combobox(
                    fields_frame,
                    textvariable=var,
                    values=options[0] if options else [],
                    state="readonly",
                    width=28,
                )
                combo.grid(row=row, column=1, pady=6, padx=(10, 0))
                self.form_vars[field_name] = var

                # Store references for branch and zone combos
                if field_name == "branch_id":
                    self.branch_combo = combo
                    combo.bind("<<ComboboxSelected>>", self.on_branch_change)
                elif field_name == "zone_id":
                    self.zone_combo = combo

            elif field_type == "date":
                var = tk.StringVar(value=datetime.now().strftime("%Y-%m-%d"))
                ttk.Entry(fields_frame, textvariable=var, width=30).grid(
                    row=row, column=1, pady=6, padx=(10, 0)
                )
                tk.Button(
                    fields_frame, text="📅", command=lambda v=var: self.pick_date(v)
                ).grid(row=row, column=2, padx=5)
                self.form_vars[field_name] = var

    def on_branch_change(self, event=None):
        """Update zones when branch changes"""
        branch_name = self.form_vars["branch_id"].get()
        if not branch_name:
            return

        branch_id = self.db.get_branch_id_by_name(branch_name)
        if not branch_id:
            return

        zones = self.db.get_zones_by_branch(branch_id)
        zone_names = [z[1] for z in zones]

        if self.zone_combo:
            self.zone_combo["values"] = zone_names
            self.form_vars["zone_id"].set(zone_names[0] if zone_names else "")

    def fill_staff_form(self):
        """Fill form with current staff data"""
        staff = self.current_staff
        if not staff:
            return

        # Get branch and zone names
        conn = self.db.get_connection()
        cursor = conn.cursor()

        cursor.execute(
            "SELECT branch_name FROM gym_branches WHERE branch_id=?",
            (staff["branch_id"],),
        )
        branch_row = cursor.fetchone()
        if branch_row:
            self.form_vars["branch_id"].set(branch_row[0])

        cursor.execute(
            "SELECT zone_name FROM workout_zones WHERE zone_id=?", (staff["zone_id"],)
        )
        zone_row = cursor.fetchone()
        if zone_row:
            self.form_vars["zone_id"].set(zone_row[0])

        conn.close()

        # Fill other fields
        for field, var in self.form_vars.items():
            if field not in ("branch_id", "zone_id", "password") and isinstance(
                var, tk.StringVar
            ):
                var.set(str(staff.get(field, "")))

    def disable_form_fields(self, parent):
        """Disable all form fields for view-only mode"""
        for child in parent.winfo_children():
            if isinstance(child, tk.Frame):
                self.disable_form_fields(child)
            elif isinstance(child, (ttk.Entry, ttk.Combobox)):
                child.config(state="disabled")

    def add_form_buttons(self, parent, view_only, edit_mode, window):
        """Add form action buttons"""
        button_frame = tk.Frame(parent, bg="#ecf0f1")
        button_frame.pack(pady=30)

        if not view_only:
            tk.Button(
                button_frame,
                text="Save",
                bg="#2ecc71",
                fg="white",
                font=("Arial", 11, "bold"),
                padx=30,
                pady=10,
                command=lambda: self.save_staff(edit_mode, window),
            ).pack(side="left", padx=10)

        tk.Button(
            button_frame,
            text="Close",
            bg="#95a5a6",
            fg="white",
            font=("Arial", 11),
            padx=30,
            pady=10,
            command=window.destroy,
        ).pack(side="left", padx=10)

    def save_staff(self, edit_mode, window):
        """Save staff data to database"""
        staff_data = {}

        # Collect form data
        for field, var in self.form_vars.items():
            staff_data[field] = var.get()

        # Get branch and zone IDs
        branch_id = self.db.get_branch_id_by_name(staff_data["branch_id"])
        zone_id = self.db.get_zone_id_by_name_and_branch(
            staff_data["zone_id"], branch_id
        )

        staff_data["branch_id"] = branch_id
        staff_data["zone_id"] = zone_id

        # Hash password if provided
        if "password" in staff_data and staff_data["password"]:
            staff_data["password_hash"] = self.hash_password(staff_data["password"])
        del staff_data["password"]  # Remove plain text password

        # Validation
        required = ["first_name", "last_name", "role", "email"]
        for field in required:
            if not staff_data.get(field):
                messagebox.showerror(
                    "Error", f"{field.replace('_', ' ').title()} is required!"
                )
                return

        # Save to database
        conn = self.db.get_connection()
        cursor = conn.cursor()

        try:
            if edit_mode:
                staff_id = self.current_staff["staff_id"]
                set_clause = ", ".join(f"{k}=?" for k in staff_data.keys())
                values = list(staff_data.values()) + [staff_id]

                cursor.execute(
                    f"UPDATE staff SET {set_clause} WHERE staff_id=?", values
                )
            else:
                staff_data["schedule"] = "{}"
                cols = ", ".join(staff_data.keys())
                qs = ", ".join("?" for _ in staff_data)
                cursor.execute(
                    f"INSERT INTO staff ({cols}) VALUES ({qs})",
                    list(staff_data.values()),
                )

            conn.commit()
            self.load_staff()
            window.destroy()
            messagebox.showinfo("Success", "Staff saved successfully!")

        except Exception as e:
            conn.rollback()
            messagebox.showerror("Database Error", str(e))
        finally:
            conn.close()

    # ========== ACTION METHODS ==========

    def add_staff(self):
        """Open form to add new staff"""
        self.show_staff_form()

    def view_staff_details(self):
        """View staff details in read-only mode"""
        if not self.current_staff:
            messagebox.showinfo("Select Staff", "Please select a staff member first")
            return
        self.show_staff_form(view_only=True)

    def edit_staff(self):
        """Edit selected staff member"""
        if not self.current_staff:
            messagebox.showinfo("Select Staff", "Please select a staff member first")
            return
        self.show_staff_form(edit_mode=True)

    def process_payroll(self):
        """Process payroll for selected staff"""
        if not self.current_staff:
            messagebox.showinfo("Select Staff", "Please select a staff member first")
            return

        # Calculate payroll
        salary = float(self.current_staff.get("salary", 0))
        tax = salary * 0.05  # 5% tax
        net_salary = salary - tax

        # Create payroll window
        payroll_window = tk.Toplevel(self.parent)
        payroll_window.title(
            f"Payroll - {self.current_staff['first_name']} {self.current_staff['last_name']}"
        )
        payroll_window.geometry("400x300")

        # Payroll statement text
        payroll_text = f"""
        PAYROLL STATEMENT
        {'='*30}
        Employee: {self.current_staff['first_name']} {self.current_staff['last_name']}
        Role: {self.current_staff['role']}
        Period: {datetime.now().strftime('%B %Y')}
        
        {'='*30}
        Basic Salary: PKR {salary:,.2f}
        Tax (5%): PKR {tax:,.2f}
        
        {'='*30}
        NET SALARY: PKR {net_salary:,.2f}
        
        {'='*30}
        Date: {datetime.now().strftime('%Y-%m-%d')}
        """

        # Display text
        text_widget = tk.Text(payroll_window, font=("Courier", 11))
        text_widget.pack(fill="both", expand=True, padx=20, pady=20)
        text_widget.insert("1.0", payroll_text)
        text_widget.config(state="disabled")

        # Mark as paid button
        tk.Button(
            payroll_window,
            text="Mark as Paid",
            bg="#2ecc71",
            fg="white",
            command=lambda: self.mark_paid(payroll_window),
        ).pack(pady=10)

    def mark_paid(self, window):
        """Mark payroll as paid"""
        messagebox.showinfo("Success", "Payroll marked as paid!")
        window.destroy()

    def manage_schedule(self):
        """Manage staff schedule"""
        if not self.current_staff:
            messagebox.showinfo("Select Staff", "Please select a staff member first")
            return

        schedule_window = tk.Toplevel(self.parent)
        schedule_window.title(
            f"Schedule - {self.current_staff['first_name']} {self.current_staff['last_name']}"
        )
        schedule_window.geometry("700x400")

        # Title
        tk.Label(
            schedule_window, text="Weekly Schedule", font=("Arial", 16, "bold")
        ).pack(pady=10)

        # Schedule grid
        grid_frame = tk.Frame(schedule_window)
        grid_frame.pack(padx=10, pady=10)

        days = [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday",
        ]

        # Day headers
        for i, day in enumerate(days):
            tk.Label(grid_frame, text=day, font=("Arial", 11, "bold")).grid(
                row=0, column=i + 1, padx=5, pady=5
            )

        # Time labels
        tk.Label(grid_frame, text="Start").grid(row=1, column=0)
        tk.Label(grid_frame, text="End").grid(row=2, column=0)

        # Time entries
        self.schedule_vars = {}
        for i, day in enumerate(days):
            start_var = tk.StringVar(value="09:00")
            end_var = tk.StringVar(value="17:00")

            ttk.Entry(grid_frame, textvariable=start_var, width=8).grid(
                row=1, column=i + 1, padx=5
            )

            ttk.Entry(grid_frame, textvariable=end_var, width=8).grid(
                row=2, column=i + 1, padx=5
            )

            self.schedule_vars[day] = (start_var, end_var)

        # Save button
        tk.Button(
            schedule_window,
            text="Save Schedule",
            bg="#2ecc71",
            fg="white",
            command=lambda: self.save_schedule(schedule_window),
        ).pack(pady=20)

    def save_schedule(self, window):
        """Save schedule to database"""
        messagebox.showinfo("Success", "Schedule saved successfully!")
        window.destroy()

    def deactivate_staff(self):
        """Deactivate selected staff member"""
        if not self.current_staff:
            messagebox.showinfo("Select Staff", "Please select a staff member first")
            return

        if messagebox.askyesno(
            "Confirm Deactivation",
            f"Deactivate {self.current_staff['first_name']} {self.current_staff['last_name']}?",
        ):
            conn = self.db.get_connection()
            cursor = conn.cursor()

            try:
                cursor.execute(
                    """
                    UPDATE staff 
                    SET status = 'Inactive'
                    WHERE staff_id = ?
                """,
                    (self.current_staff["staff_id"],),
                )

                conn.commit()
                messagebox.showinfo("Success", "Staff deactivated successfully!")
                self.load_staff()

            except Exception as e:
                conn.rollback()
                messagebox.showerror("Error", f"Failed to deactivate staff: {str(e)}")
            finally:
                conn.close()
