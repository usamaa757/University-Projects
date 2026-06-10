import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import database


class AppointmentManagement:
    def __init__(self, parent, db, user_data):
        self.parent = parent
        self.db = db

        self.user_data = user_data
        self.current_user_id = user_data["id"]
        self.user_role = user_data["role"]

        self.current_appointment = None

        self.setup_ui()
        self.load_appointments()

    def setup_ui(self):
        # Main container
        main_frame = tk.Frame(self.parent, bg="#ecf0f1")
        main_frame.pack(fill="both", expand=True, padx=20, pady=20)

        # Header
        header_frame = tk.Frame(main_frame, bg="#ecf0f1")
        header_frame.pack(fill="x", pady=(0, 10))

        tk.Label(
            header_frame,
            text="Appointment Management",
            font=("Arial", 20, "bold"),
            bg="#ecf0f1",
        ).pack(side="left")

        # Add new appointment button
        if self.user_role in ["Manager", "Trainer"]:
            add_btn = tk.Button(
                header_frame,
                text="➕ New Appointment",
                font=("Arial", 11, "bold"),
                bg="#2ecc71",
                fg="white",
                padx=15,
                pady=6,
                cursor="hand2",
                command=self.add_new_appointment,
            )
            add_btn.pack(side="right", padx=(0, 10))

        # Calendar and filters
        filter_frame = tk.Frame(main_frame, bg="white", padx=15, pady=15)
        filter_frame.pack(fill="x", pady=(0, 15))

        # Date selection
        tk.Label(filter_frame, text="Date:", font=("Arial", 11), bg="white").grid(
            row=0, column=0, padx=(0, 5)
        )

        self.date_var = tk.StringVar(value="")
        date_entry = ttk.Entry(filter_frame, textvariable=self.date_var, width=12)
        date_entry.grid(row=0, column=1, padx=(0, 10))

        tk.Button(
            filter_frame,
            text="Today",
            bg="#3498db",
            fg="white",
            font=("Arial", 10),
            padx=10,
            pady=3,
            command=self.set_today,
        ).grid(row=0, column=2, padx=(0, 10))

        tk.Button(
            filter_frame,
            text="Load",
            bg="#3498db",
            fg="white",
            font=("Arial", 10),
            padx=10,
            pady=3,
            command=self.load_appointments,
        ).grid(row=0, column=3, padx=(0, 10))

        # Status filter
        tk.Label(filter_frame, text="Status:", font=("Arial", 11), bg="white").grid(
            row=0, column=4, padx=(10, 5)
        )

        self.status_var = tk.StringVar(value="All")
        status_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.status_var,
            values=["All", "Scheduled", "Completed", "Cancelled", "No-show"],
            state="readonly",
            width=10,
        )
        status_combo.grid(row=0, column=5, padx=(0, 5))
        status_combo.bind("<<ComboboxSelected>>", self.load_appointments)

        # Type filter
        tk.Label(filter_frame, text="Type:", font=("Arial", 11), bg="white").grid(
            row=0, column=6, padx=(10, 5)
        )

        self.type_var = tk.StringVar(value="All")
        type_combo = ttk.Combobox(
            filter_frame,
            textvariable=self.type_var,
            values=[
                "All",
                "Personal Training",
                "Group Class",
                "Nutrition Consultation",
            ],
            state="readonly",
            width=12,
        )
        type_combo.grid(row=0, column=7, padx=(0, 5))
        type_combo.bind("<<ComboboxSelected>>", self.load_appointments)

        # Calendar view
        self.create_calendar_view(main_frame)

    def create_calendar_view(self, parent):
        # Create notebook for different views
        self.notebook = ttk.Notebook(parent)
        self.notebook.pack(fill="both", expand=True)

        # ---------------- Day View ----------------
        day_frame = tk.Frame(self.notebook, bg="#ecf0f1")
        self.notebook.add(day_frame, text="Day View")

        # Time slots from 6 AM to 10 PM
        time_slots = []
        for hour in range(6, 22):
            time_slots.append(f"{hour:02d}:00")
            time_slots.append(f"{hour:02d}:30")

        # Create time grid
        time_grid = tk.Frame(day_frame, bg="#ecf0f1")
        time_grid.pack(fill="both", expand=True, padx=10, pady=10)

        # Time labels and slot frames
        self.day_slots = {}
        for i, time in enumerate(time_slots):
            tk.Label(time_grid, text=time, font=("Arial", 9), bg="#f8f9fa").grid(
                row=i, column=0, sticky="nsew", padx=5, pady=2
            )

            slot_frame = tk.Frame(
                time_grid, bg="white", relief="ridge", bd=1, height=40
            )
            slot_frame.grid(row=i, column=1, sticky="nsew", padx=5, pady=2)
            slot_frame.grid_propagate(False)

            self.day_slots[time] = slot_frame
            time_grid.rowconfigure(i, weight=1)

        time_grid.columnconfigure(1, weight=1)

        # ---------------- Week View ----------------
        self.create_weekly_view()  # Create week view as separate tab

        # ---------------- List View ----------------
        list_frame = tk.Frame(self.notebook, bg="#ecf0f1")
        self.notebook.add(list_frame, text="List View")
        self.create_appointments_tree(list_frame)

        # Add action buttons in list view (with proper wrapping for smaller screens)
        if self.user_role in ["Trainer", "Manager"]:
            action_frame = tk.Frame(list_frame, bg="#ecf0f1")
            action_frame.pack(fill="x", pady=10)

            buttons = [
                ("✅ Complete", self.complete_appointment, "#2ecc71"),
                ("❌ Cancel", self.cancel_appointment, "#e74c3c"),
                ("📅 Reschedule", self.reschedule_appointment, "#f39c12"),
            ]

            for idx, (text, command, color) in enumerate(buttons):
                tk.Button(
                    action_frame,
                    text=text,
                    bg=color,
                    fg="white",
                    font=("Arial", 10),
                    padx=5,
                    pady=8,
                    cursor="hand2",
                    command=command,
                ).grid(
                    row=0, column=idx, padx=5, pady=5, sticky="ew"
                )  # All in row 0

            # Make all 4 columns expand equally
            for i in range(4):
                action_frame.columnconfigure(i, weight=1)

    def create_weekly_view(self):
        """Create weekly calendar view as a separate tab in notebook"""
        week_frame = tk.Frame(self.notebook, bg="#ecf0f1")
        self.notebook.add(week_frame, text="Week View")

        # Navigation
        nav_frame = tk.Frame(week_frame, bg="#ecf0f1", pady=5)
        nav_frame.pack(fill="x")

        self.current_week_start = datetime.now() - timedelta(
            days=datetime.now().weekday()
        )

        tk.Button(
            nav_frame,
            text="◀ Previous Week",
            command=self.previous_week,
            bg="#3498db",
            fg="white",
            font=("Arial", 9),
        ).pack(side="left", padx=5)

        tk.Button(
            nav_frame,
            text="This Week",
            command=self.this_week,
            bg="#2ecc71",
            fg="white",
            font=("Arial", 9),
        ).pack(side="left", padx=5)

        tk.Button(
            nav_frame,
            text="Next Week ▶",
            command=self.next_week,
            bg="#3498db",
            fg="white",
            font=("Arial", 9),
        ).pack(side="left", padx=5)

        # Week calendar grid
        week_calendar = tk.Frame(week_frame, bg="#ecf0f1")
        week_calendar.pack(fill="both", expand=True, padx=10, pady=10)

        days = [
            "Time",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday",
        ]
        for i, day in enumerate(days):
            tk.Label(
                week_calendar,
                text=day,
                font=("Arial", 10, "bold"),
                bg="#2c3e50",
                fg="white",
                padx=5,
                pady=5,
            ).grid(row=0, column=i, sticky="nsew", padx=1, pady=1)

        # Time slots
        time_slots = []
        for hour in range(6, 22):
            time_slots.append(f"{hour:02d}:00")
            time_slots.append(f"{hour:02d}:30")

        self.week_slots = {}
        for i, time in enumerate(time_slots):
            tk.Label(
                week_calendar,
                text=time,
                font=("Arial", 9),
                bg="#f8f9fa",
                padx=5,
                pady=2,
            ).grid(row=i + 1, column=0, sticky="nsew", padx=1, pady=1)

            for day in range(1, 8):
                slot_frame = tk.Frame(
                    week_calendar, bg="white", relief="ridge", bd=1, height=40
                )
                slot_frame.grid(row=i + 1, column=day, sticky="nsew", padx=1, pady=1)
                slot_frame.grid_propagate(False)
                self.week_slots[(day, time)] = slot_frame

            week_calendar.rowconfigure(i + 1, weight=1)

        for col in range(len(days)):
            week_calendar.columnconfigure(col, weight=1)

    # ========== DAILY/WEEKLY VIEW POPULATION METHODS ==========

    def _populate_daily_view(self):
        """Populate the daily view with appointments"""
        # Clear existing appointments from daily view
        for time_slot, slot_frame in self.day_slots.items():
            for widget in slot_frame.winfo_children():
                widget.destroy()

        # Get appointments for selected date
        selected_date = self.date_var.get()
        conn = self.db.get_connection()
        cursor = conn.cursor()

        query = """
            SELECT 
                a.appointment_id,
                m.first_name || ' ' || m.last_name AS member_name,
                s.first_name || ' ' || s.last_name AS trainer_name,
                a.appointment_type,
                a.start_time,
                a.end_time,
                a.status
            FROM appointments a
            LEFT JOIN members m ON a.member_id = m.member_id
            LEFT JOIN staff s ON a.trainer_id = s.staff_id
            WHERE a.appointment_date = ?
        """

        params = [selected_date]

        if self.user_role == "Trainer":
            query += " AND a.trainer_id = ?"
            params.append(self.current_user_id)

        # Apply status filter if not "All"
        if self.status_var.get() != "All":
            query += " AND a.status = ?"
            params.append(self.status_var.get())

        # Apply type filter if not "All"
        if self.type_var.get() != "All":
            query += " AND a.appointment_type = ?"
            params.append(self.type_var.get())

        cursor.execute(query, params)
        appointments = cursor.fetchall()
        conn.close()

        # Place appointments in time slots
        for appointment in appointments:
            (
                appt_id,
                member_name,
                trainer_name,
                appt_type,
                start_time,
                end_time,
                status,
            ) = appointment

            # Find which time slots this appointment occupies
            start_hour = int(start_time.split(":")[0])
            start_min = int(start_time.split(":")[1])
            end_hour = int(end_time.split(":")[0])
            end_min = int(end_time.split(":")[1])

            # Convert to slot indices (each 30-minute slot)
            start_slot = (start_hour - 6) * 2 + (start_min // 30)
            duration_slots = ((end_hour - start_hour) * 2) + (
                (end_min - start_min) // 30
            )

            # Create appointment label for the first slot
            if start_slot < len(self.day_slots):
                time_key = list(self.day_slots.keys())[start_slot]
                slot_frame = self.day_slots[time_key]

                # Color based on status
                colors = {
                    "Scheduled": "#3498db",
                    "Completed": "#2ecc71",
                    "Cancelled": "#e74c3c",
                    "No-show": "#95a5a6",
                }

                appt_label = tk.Label(
                    slot_frame,
                    text=f"{member_name}\n{appt_type}",
                    bg=colors.get(status, "#3498db"),
                    fg="white",
                    font=("Arial", 8),
                    relief="raised",
                    cursor="hand2",
                )
                appt_label.pack(fill="both", expand=True)

                # Bind click event
                appt_label.bind(
                    "<Button-1>",
                    lambda e, appt_id=appt_id: self.load_appointment_details(appt_id),
                )

    def _populate_weekly_view(self):
        """Populate weekly view with appointments"""
        # Clear existing appointments
        for slot_frame in self.week_slots.values():
            for widget in slot_frame.winfo_children():
                widget.destroy()

        # Calculate dates for the week
        week_dates = []
        for i in range(7):
            current_date = self.current_week_start + timedelta(days=i)
            week_dates.append(current_date.strftime("%Y-%m-%d"))

        # Fetch appointments for the week
        conn = self.db.get_connection()
        cursor = conn.cursor()

        query = """
            SELECT 
                a.appointment_id,
                a.appointment_date,
                m.first_name || ' ' || m.last_name AS member_name,
                s.first_name || ' ' || s.last_name AS trainer_name,
                a.appointment_type,
                a.start_time,
                a.end_time,
                a.status
            FROM appointments a
            LEFT JOIN members m ON a.member_id = m.member_id
            LEFT JOIN staff s ON a.trainer_id = s.staff_id
            WHERE a.appointment_date BETWEEN ? AND ?
        """

        params = [week_dates[0], week_dates[-1]]

        if self.user_role == "Trainer":
            query += " AND a.trainer_id = ?"
            params.append(self.current_user_id)

        # Apply status filter if not "All"
        if self.status_var.get() != "All":
            query += " AND a.status = ?"
            params.append(self.status_var.get())

        # Apply type filter if not "All"
        if self.type_var.get() != "All":
            query += " AND a.appointment_type = ?"
            params.append(self.type_var.get())

        query += " ORDER BY a.appointment_date, a.start_time"

        cursor.execute(query, params)
        appointments = cursor.fetchall()
        conn.close()

        # Place appointments in weekly grid
        for appointment in appointments:
            (
                appt_id,
                appt_date,
                member_name,
                trainer_name,
                appt_type,
                start_time,
                end_time,
                status,
            ) = appointment

            # Find day index (0=Monday, 6=Sunday)
            appt_datetime = datetime.strptime(appt_date, "%Y-%m-%d")
            day_index = appt_datetime.weekday() + 1  # +1 for time column

            # Find time slot
            start_hour = int(start_time.split(":")[0])
            start_min = int(start_time.split(":")[1])

            # Convert to slot row (starting from row 1)
            slot_row = (start_hour - 6) * 2 + (start_min // 30) + 1

            if (day_index, start_time) in self.week_slots:
                slot_frame = self.week_slots[(day_index, start_time)]

                # Color based on status
                colors = {
                    "Scheduled": "#3498db",
                    "Completed": "#2ecc71",
                    "Cancelled": "#e74c3c",
                    "No-show": "#95a5a6",
                }

                appt_label = tk.Label(
                    slot_frame,
                    text=f"{member_name}\n{appt_type}",
                    bg=colors.get(status, "#3498db"),
                    fg="white",
                    font=("Arial", 8),
                    relief="raised",
                    cursor="hand2",
                )
                appt_label.pack(fill="both", expand=True)

                # Bind click event
                appt_label.bind(
                    "<Button-1>",
                    lambda e, appt_id=appt_id: self.load_appointment_details(appt_id),
                )

    def previous_week(self):
        """Navigate to previous week"""
        self.current_week_start -= timedelta(days=7)
        self._populate_weekly_view()

    def next_week(self):
        """Navigate to next week"""
        self.current_week_start += timedelta(days=7)
        self._populate_weekly_view()

    def this_week(self):
        """Navigate to current week"""
        self.current_week_start = datetime.now() - timedelta(
            days=datetime.now().weekday()
        )
        self._populate_weekly_view()

    # ========== TREEVIEW AND LIST VIEW METHODS ==========

    def create_appointments_tree(self, parent):
        container = tk.Frame(parent)
        container.pack(fill="both", expand=True)

        # Treeview area
        tree_frame = tk.Frame(container)
        tree_frame.pack(fill="both", expand=True, padx=10, pady=(5, 0))

        # Scrollbars
        tree_scroll_y = ttk.Scrollbar(tree_frame)
        tree_scroll_y.pack(side="right", fill="y")

        tree_scroll_x = ttk.Scrollbar(tree_frame, orient="horizontal")
        tree_scroll_x.pack(side="bottom", fill="x")

        # Treeview - Adjust column widths for smaller screen
        columns = ("ID", "Member", "Trainer", "Type", "Date", "Time", "Status", "Notes")
        self.tree = ttk.Treeview(
            tree_frame,
            columns=columns,
            yscrollcommand=tree_scroll_y.set,
            xscrollcommand=tree_scroll_x.set,
            selectmode="browse",
            height=15,  # Reduced height
        )

        tree_scroll_y.config(command=self.tree.yview)
        tree_scroll_x.config(command=self.tree.xview)

        # Configure columns with smaller widths
        column_widths = [50, 120, 100, 120, 90, 80, 80, 100]  # Adjusted widths
        for i, col in enumerate(columns):
            self.tree.column(col, anchor="center", width=column_widths[i])
            self.tree.heading(col, text=col)

        self.tree.pack(fill="both", expand=True)
        self.tree.bind("<<TreeviewSelect>>", self.on_appointment_select)

    def load_appointments(self, event=None):
        # Clear treeview
        for item in self.tree.get_children():
            self.tree.delete(item)

        conn = self.db.get_connection()
        cursor = conn.cursor()

        query = """
            SELECT 
                a.appointment_id, 
                m.first_name || ' ' || m.last_name AS member_name,
                s.first_name || ' ' || s.last_name AS trainer_name,
                a.appointment_type, 
                a.appointment_date, 
                a.start_time || '-' || a.end_time AS time_slot,
                a.status, 
                a.notes
            FROM appointments a
            LEFT JOIN members m ON a.member_id = m.member_id
            LEFT JOIN staff s ON a.trainer_id = s.staff_id
            WHERE 1=1
        """

        params = []

        # 🔐 TRAINER CAN SEE ONLY HIS APPOINTMENTS
        if self.user_role == "Trainer":
            query += " AND a.trainer_id = ?"
            params.append(self.current_user_id)

        # Date filter
        date_filter = self.date_var.get()
        if date_filter:
            query += " AND a.appointment_date = ?"
            params.append(date_filter)

        # Status filter
        status_filter = self.status_var.get()
        if status_filter != "All":
            query += " AND a.status = ?"
            params.append(status_filter)

        # Type filter
        type_filter = self.type_var.get()
        if type_filter != "All":
            query += " AND a.appointment_type = ?"
            params.append(type_filter)

        query += " ORDER BY a.appointment_date DESC, a.start_time"

        cursor.execute(query, params)
        appointments = cursor.fetchall()
        conn.close()

        for appt in appointments:
            self.tree.insert("", "end", values=appt)

        # Refresh daily and weekly views if they exist
        if hasattr(self, "day_slots"):
            self._populate_daily_view()

        if hasattr(self, "week_slots"):
            self._populate_weekly_view()

    def on_appointment_select(self, event):
        selection = self.tree.selection()
        if selection:
            item = self.tree.item(selection[0])
            appointment_id = item["values"][0]
            self.load_appointment_details(appointment_id)

    def load_appointment_details(self, appointment_id):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT * FROM appointments WHERE appointment_id = ?", (appointment_id,)
        )
        appt_data = cursor.fetchone()
        conn.close()

        if appt_data:
            columns = [desc[0] for desc in cursor.description]
            self.current_appointment = dict(zip(columns, appt_data))

    # ========== APPOINTMENT ACTION METHODS ==========

    def complete_appointment(self):
        if not self.current_appointment:
            messagebox.showinfo(
                "Select Appointment", "Please select an appointment first"
            )
            return

        if messagebox.askyesno(
            "Complete Appointment", "Mark this appointment as completed?"
        ):
            conn = self.db.get_connection()
            cursor = conn.cursor()

            try:
                cursor.execute(
                    """
                    UPDATE appointments 
                    SET status = 'Completed'
                    WHERE appointment_id = ?
                """,
                    (self.current_appointment["appointment_id"],),
                )

                conn.commit()
                messagebox.showinfo("Success", "Appointment marked as completed!")
                self.load_appointments()

            except Exception as e:
                conn.rollback()
                messagebox.showerror(
                    "Error", f"Failed to complete appointment: {str(e)}"
                )
            finally:
                conn.close()

    def cancel_appointment(self):
        if not self.current_appointment:
            messagebox.showinfo(
                "Select Appointment", "Please select an appointment first"
            )
            return

        if messagebox.askyesno("Cancel Appointment", "Cancel this appointment?"):
            conn = self.db.get_connection()
            cursor = conn.cursor()

            try:
                cursor.execute(
                    """
                    UPDATE appointments 
                    SET status = 'Cancelled'
                    WHERE appointment_id = ?
                """,
                    (self.current_appointment["appointment_id"],),
                )

                conn.commit()
                messagebox.showinfo("Success", "Appointment cancelled!")
                self.load_appointments()

            except Exception as e:
                conn.rollback()
                messagebox.showerror("Error", f"Failed to cancel appointment: {str(e)}")
            finally:
                conn.close()



    def reschedule_appointment(self):
        selection = self.tree.selection()
        if not selection:
            messagebox.showinfo(
                "Select Appointment", "Please select an appointment first"
            )
            return

        item = self.tree.item(selection[0])
        appointment_id = item["values"][0]

        self.load_appointment_details(appointment_id)  # Sets self.current_appointment

        if self.current_appointment:
            self.show_appointment_form(reschedule=True)

    # ========== APPOINTMENT FORM METHODS ==========

    def add_new_appointment(self):
        self.show_appointment_form()

    def get_members_for_trainer(self):
        """Get members assigned to the same branch and zone as the trainer"""
        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Get trainer's branch and zone
        cursor.execute(
            """
            SELECT branch_id, zone_id 
            FROM staff 
            WHERE staff_id = ?
            """,
            (self.current_user_id,),
        )
        trainer_info = cursor.fetchone()

        if not trainer_info:
            conn.close()
            return []

        trainer_branch, trainer_zone = trainer_info

        # Get members from same branch and zone
        cursor.execute(
            """
            SELECT member_id, first_name || ' ' || last_name
            FROM members
            WHERE branch_id = ? AND zone_id = ? AND status='Active'
            ORDER BY first_name, last_name
            """,
            (trainer_branch, trainer_zone),
        )
        members = cursor.fetchall()
        conn.close()

        return members

    def show_appointment_form(self, reschedule=False):
        form_window = tk.Toplevel(self.parent)
        form_window.title("Reschedule Appointment" if reschedule else "New Appointment")
        form_window.geometry("500x550")  # Reduced height
        form_window.configure(bg="#ecf0f1")

        form_window.transient(self.parent)
        form_window.grab_set()

        # ---------------- Outer frame ----------------
        form_frame = tk.Frame(form_window, bg="#ecf0f1", padx=20, pady=15)
        form_frame.pack(fill="both", expand=True)

        tk.Label(
            form_frame,
            text="Reschedule Appointment" if reschedule else "Schedule Appointment",
            font=("Arial", 16, "bold"),  # Smaller font
            bg="#ecf0f1",
        ).pack(anchor="w", pady=(0, 15))

        fields_frame = tk.Frame(form_frame, bg="#ecf0f1")
        fields_frame.pack(fill="both", expand=True)

        # ---------------- Initialize form_vars ONCE ----------------
        self.form_vars = {}

        # ---------------- Fetch data ----------------
        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Members - FILTERED FOR TRAINER
        if self.user_role == "Trainer":
            if reschedule and self.current_appointment:
                # For reschedule, only show the member of this appointment
                cursor.execute(
                    """
                    SELECT member_id, first_name || ' ' || last_name
                    FROM members
                    WHERE member_id = ?
                    """,
                    (self.current_appointment.get("member_id"),),
                )
                members = cursor.fetchall()
            else:
                # For new appointment, show members from same branch and zone
                members = self.get_members_for_trainer()
        else:
            # Manager sees all active members
            cursor.execute(
                """
                SELECT member_id, first_name || ' ' || last_name
                FROM members
                WHERE status='Active'
                """
            )
            members = cursor.fetchall()

        # Trainers
        if self.user_role == "Trainer":
            # Trainer can only see themselves
            cursor.execute(
                """
                SELECT staff_id, first_name || ' ' || last_name
                FROM staff
                WHERE staff_id = ?
                """,
                (self.current_user_id,),
            )
        else:
            # Manager sees all trainers
            cursor.execute(
                """
                SELECT staff_id, first_name || ' ' || last_name
                FROM staff
                WHERE role='Trainer' AND status='Active'
                """
            )
        trainers = cursor.fetchall()

        conn.close()

        # ---------------- Define form fields ----------------
        fields = [
            ("Member:", "member_id", "combo", [(m[0], m[1]) for m in members]),
            ("Trainer:", "trainer_id", "combo", [(t[0], t[1]) for t in trainers]),
            (
                "Type:",
                "appointment_type",
                "combo",
                ["Personal Training", "Group Class", "Nutrition Consultation"],
            ),
            ("Date:", "appointment_date", "date", []),
            ("Start Time:", "start_time", "combo", self.generate_time_slots()),
            ("End Time:", "end_time", "combo", self.generate_time_slots()),
            ("Notes:", "notes", "text", []),
        ]

        # ---------------- Build fields ----------------
        row = 0
        for label_text, field_name, field_type, options in fields:
            tk.Label(
                fields_frame,
                text=label_text,
                font=("Arial", 10),
                bg="#ecf0f1",  # Smaller font
            ).grid(row=row, column=0, sticky="w", pady=4)

            if field_type == "combo":
                var = tk.StringVar()
                display_values = [
                    opt[1] if isinstance(opt, tuple) else opt for opt in options
                ]

                if reschedule and field_name == "member_id":
                    # For reschedule, member field is read-only
                    combo = ttk.Combobox(
                        fields_frame,
                        textvariable=var,
                        values=display_values,
                        state="readonly",
                        width=28,  # Smaller width
                    )
                    combo.config(state="disabled")
                else:
                    combo = ttk.Combobox(
                        fields_frame,
                        textvariable=var,
                        values=display_values,
                        state="readonly",
                        width=28,  # Smaller width
                    )

                combo.grid(row=row, column=1, pady=4, padx=(8, 0))

                # Map display name → ID
                if options and isinstance(options[0], tuple):
                    self.form_vars[f"{field_name}_mapping"] = {
                        opt[1]: opt[0] for opt in options
                    }

                self.form_vars[field_name] = var

            elif field_type == "date":
                var = tk.StringVar()
                entry = ttk.Entry(
                    fields_frame, textvariable=var, width=28
                )  # Smaller width
                entry.grid(row=row, column=1, pady=4, padx=(8, 0))
                tk.Button(
                    fields_frame,
                    text="📅",
                    font=("Arial", 8),
                    command=lambda v=var: self.pick_date(v),
                ).grid(row=row, column=2, padx=(4, 0))
                self.form_vars[field_name] = var

            elif field_type == "text":
                text = tk.Text(fields_frame, height=2, width=25)  # Smaller text area
                text.grid(row=row, column=1, pady=4, padx=(8, 0))
                self.form_vars[field_name] = text

            row += 1

        # ---------------- Pre-fill if rescheduling ----------------
        if reschedule and self.current_appointment:
            self.prefill_appointment_form()

        # ---------------- Add Cancel/Complete buttons for reschedule ----------------
        if reschedule:
            status_frame = tk.Frame(form_frame, bg="#ecf0f1")
            status_frame.pack(pady=8)

            # Current appointment status
            if self.current_appointment:
                status_text = self.current_appointment.get("status", "Unknown")
                tk.Label(
                    status_frame,
                    text=f"Current Status: {status_text}",
                    font=("Arial", 10, "bold"),  # Smaller font
                    bg="#ecf0f1",
                    fg="#e74c3c" if status_text == "Cancelled" else "#2ecc71",
                ).pack(pady=3)

            # Action buttons for reschedule form
            action_frame = tk.Frame(form_frame, bg="#ecf0f1")
            action_frame.pack(pady=8)

            if self.user_role in ["Trainer", "Manager"]:
                if self.current_appointment and self.current_appointment.get(
                    "status"
                ) not in ["Completed", "Cancelled"]:
                    tk.Button(
                        action_frame,
                        text="✅ Complete",
                        bg="#2ecc71",
                        fg="white",
                        font=("Arial", 9),
                        padx=10,
                        pady=5,
                        cursor="hand2",
                        command=lambda: [
                            self.complete_appointment(),
                            form_window.destroy(),
                        ],
                    ).pack(side="left", padx=3)

                    tk.Button(
                        action_frame,
                        text="❌ Cancel",
                        bg="#e74c3c",
                        fg="white",
                        font=("Arial", 9),
                        padx=10,
                        pady=5,
                        cursor="hand2",
                        command=lambda: [
                            self.cancel_appointment(),
                            form_window.destroy(),
                        ],
                    ).pack(side="left", padx=3)

        # ---------------- Buttons ----------------
        button_frame = tk.Frame(form_frame, bg="#ecf0f1")
        button_frame.pack(pady=15)

        tk.Button(
            button_frame,
            text="Save",
            bg="#2ecc71",
            fg="white",
            font=("Arial", 10, "bold"),
            padx=25,
            pady=8,
            command=lambda: self.save_appointment(form_window, reschedule),
        ).pack(side="left", padx=8)

        tk.Button(
            button_frame,
            text="Cancel",
            bg="#95a5a6",
            fg="white",
            font=("Arial", 10),
            padx=25,
            pady=8,
            command=form_window.destroy,
        ).pack(side="left", padx=8)

    def prefill_appointment_form(self):
        """Pre-fill form with current appointment data"""
        if not self.current_appointment:
            return

        # Get member and trainer names for comboboxes
        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Get member name
        cursor.execute(
            """
            SELECT first_name || ' ' || last_name 
            FROM members 
            WHERE member_id = ?
        """,
            (self.current_appointment.get("member_id"),),
        )
        member_name = cursor.fetchone()

        # Get trainer name
        cursor.execute(
            """
            SELECT first_name || ' ' || last_name 
            FROM staff 
            WHERE staff_id = ?
        """,
            (self.current_appointment.get("trainer_id"),),
        )
        trainer_name = cursor.fetchone()

        conn.close()

        # Fill the form fields
        for field_name, widget in self.form_vars.items():
            if field_name.endswith("_mapping"):
                continue

            value = self.current_appointment.get(field_name, "")

            if isinstance(widget, tk.StringVar):
                # For combo boxes, set display name from mapping
                if field_name in ["member_id", "trainer_id"]:
                    mapping_key = f"{field_name}_mapping"
                    if mapping_key in self.form_vars:
                        mapping = self.form_vars[mapping_key]  # Display → ID
                        # Reverse mapping: ID → Display
                        reverse_mapping = {str(v): k for k, v in mapping.items()}
                        widget.set(reverse_mapping.get(str(value), ""))
                else:
                    widget.set(str(value) if value is not None else "")

            elif isinstance(widget, tk.Text):
                widget.delete("1.0", tk.END)
                if value:
                    widget.insert("1.0", str(value))

    def save_appointment(self, window, reschedule=False):
        appointment_data = {}

        # Gather data from form
        for field_name, widget in self.form_vars.items():
            if field_name.endswith("_mapping"):
                continue

            if isinstance(widget, tk.StringVar):
                value = widget.get()

                # Convert display names to IDs for member and trainer
                if field_name in ["member_id", "trainer_id"]:
                    mapping_key = f"{field_name}_mapping"
                    if mapping_key in self.form_vars:
                        mapping = self.form_vars[mapping_key]
                        # Find ID from display name
                        for display_name, id_value in mapping.items():
                            if display_name == value:
                                appointment_data[field_name] = id_value
                                break
                else:
                    appointment_data[field_name] = value

            elif isinstance(widget, tk.Text):
                appointment_data[field_name] = widget.get("1.0", "end-1c").strip()

        # Validation
        required = [
            "member_id",
            "trainer_id",
            "appointment_type",
            "appointment_date",
            "start_time",
            "end_time",
        ]
        for field in required:
            if not appointment_data.get(field):
                messagebox.showerror(
                    "Error", f"{field.replace('_', ' ').title()} is required!"
                )
                return

        # Conflict check (skip for the same appointment when rescheduling)
        if reschedule and self.current_appointment:
            # Check if date/time changed
            date_changed = appointment_data[
                "appointment_date"
            ] != self.current_appointment.get("appointment_date")
            time_changed = appointment_data[
                "start_time"
            ] != self.current_appointment.get("start_time") or appointment_data[
                "end_time"
            ] != self.current_appointment.get(
                "end_time"
            )
            trainer_changed = appointment_data[
                "trainer_id"
            ] != self.current_appointment.get("trainer_id")

            # Only check conflict if date, time, or trainer changed
            if date_changed or time_changed or trainer_changed:
                if not self.check_time_conflict(
                    appointment_data,
                    exclude_id=self.current_appointment["appointment_id"],
                ):
                    return
        else:
            # For new appointments, always check conflict
            if not self.check_time_conflict(appointment_data):
                return

        appointment_data["status"] = "Scheduled"

        conn = self.db.get_connection()
        cursor = conn.cursor()

        try:
            if reschedule and self.current_appointment:
                # UPDATE existing appointment
                columns = [f"{k} = ?" for k in appointment_data.keys()]
                values = list(appointment_data.values())
                values.append(self.current_appointment["appointment_id"])

                cursor.execute(
                    f"""
                    UPDATE appointments
                    SET {', '.join(columns)}
                    WHERE appointment_id = ?
                """,
                    values,
                )

                messagebox.showinfo("Success", "Appointment rescheduled successfully!")
            else:
                # INSERT new appointment
                columns = ", ".join(appointment_data.keys())
                placeholders = ", ".join(["?" for _ in appointment_data])
                cursor.execute(
                    f"""
                    INSERT INTO appointments ({columns})
                    VALUES ({placeholders})
                """,
                    list(appointment_data.values()),
                )

                messagebox.showinfo("Success", "Appointment scheduled successfully!")

            conn.commit()
            self.load_appointments()
            window.destroy()

        except Exception as e:
            conn.rollback()
            messagebox.showerror("Database Error", str(e))
        finally:
            conn.close()

    def check_time_conflict(self, appointment_data, exclude_id=None):
        conn = self.db.get_connection()
        cursor = conn.cursor()

        # Get all existing appointments for the trainer on the selected date
        query = """
            SELECT start_time, end_time 
            FROM appointments 
            WHERE trainer_id = ? 
            AND appointment_date = ?
            AND status != 'Cancelled'
        """

        params = [appointment_data["trainer_id"], appointment_data["appointment_date"]]

        if exclude_id:
            query += " AND appointment_id != ?"
            params.append(exclude_id)

        cursor.execute(query, params)
        existing_appointments = cursor.fetchall()
        conn.close()

        # Convert new appointment times to comparable format
        new_start = appointment_data["start_time"]
        new_end = appointment_data["end_time"]

        # Check for overlaps with existing appointments
        for existing_start, existing_end in existing_appointments:
            # Check if new appointment overlaps with existing one
            # Overlap occurs if:
            # 1. New start is between existing start and end
            # 2. New end is between existing start and end
            # 3. New appointment completely contains existing appointment
            if new_start < existing_end and new_end > existing_start:
                messagebox.showerror(
                    "Time Conflict",
                    f"Trainer is already booked from {existing_start} to {existing_end} on {appointment_data['appointment_date']}!",
                )
                return False

        return True

    # ========== UTILITY METHODS ==========

    def generate_time_slots(self):
        slots = []
        for hour in range(6, 22):
            for minute in ["00", "30"]:
                slots.append(f"{hour:02d}:{minute}")
        return slots

    def pick_date(self, var):
        var.set(datetime.now().strftime("%Y-%m-%d"))

    def set_today(self):
        self.date_var.set(datetime.now().strftime("%Y-%m-%d"))
        self.load_appointments()
