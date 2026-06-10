import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import database
from models.member import Member

class PaymentManagement:
    def __init__(self, parent, db):
        self.parent = parent
        self.db = db
        self.current_payment = None
        
        self.setup_ui()
        self.load_payments()
    
    def setup_ui(self):
        # Main container
        main_frame = tk.Frame(self.parent, bg='#ecf0f1')
        main_frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Header
        header_frame = tk.Frame(main_frame, bg='#ecf0f1')
        header_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(header_frame, text="Payment Management", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(side='left')
        
        # Add payment button
        add_btn = tk.Button(header_frame, text="➕ Record Payment", 
                           font=('Arial', 11, 'bold'), bg='#2ecc71', fg='white',
                           padx=20, pady=8, cursor='hand2', command=self.record_payment)
        add_btn.pack(side='right', padx=(0, 10))
        
        # Filters
        filter_frame = tk.Frame(main_frame, bg='white', padx=15, pady=15)
        filter_frame.pack(fill='x', pady=(0, 20))
        
        # Month filter
        tk.Label(filter_frame, text="Month:", font=('Arial', 11), 
                bg='white').grid(row=0, column=0, padx=(0, 10))
        
        months = ['All'] + [datetime(2000, m, 1).strftime('%B') for m in range(1, 13)]
        self.month_var = tk.StringVar(value=datetime.now().strftime('%B'))
        month_combo = ttk.Combobox(filter_frame, textvariable=self.month_var,
                                  values=months, state="readonly", width=12)
        month_combo.grid(row=0, column=1, padx=(0, 20))
        
        # Year filter
        tk.Label(filter_frame, text="Year:", font=('Arial', 11), 
                bg='white').grid(row=0, column=2, padx=(20, 10))
        
        current_year = datetime.now().year
        years = ['All'] + list(range(current_year - 2, current_year + 3))
        self.year_var = tk.StringVar(value=current_year)
        year_combo = ttk.Combobox(filter_frame, textvariable=self.year_var,
                                 values=years, state="readonly", width=8)
        year_combo.grid(row=0, column=3, padx=(0, 20))
        
        # Type filter
        tk.Label(filter_frame, text="Type:", font=('Arial', 11), 
                bg='white').grid(row=0, column=4, padx=(20, 10))
        
        self.type_var = tk.StringVar(value="All")
        type_combo = ttk.Combobox(filter_frame, textvariable=self.type_var,
                                 values=["All", "Membership", "Session", "Personal Training", "Other"],
                                 state="readonly", width=15)
        type_combo.grid(row=0, column=5, padx=(0, 20))
        
        # Load button
        tk.Button(filter_frame, text="Apply Filters", bg='#3498db', fg='white',
                 command=self.load_payments).grid(row=0, column=6, padx=(0, 10))
        
        # Summary frame
        summary_frame = tk.Frame(main_frame, bg='#f8f9fa', padx=15, pady=10)
        summary_frame.pack(fill='x', pady=(0, 20))
        
        self.total_label = tk.Label(summary_frame, text="Total Revenue: PKR 0", 
                                   font=('Arial', 12, 'bold'), bg='#f8f9fa')
        self.total_label.pack(side='left', padx=20)
        
        self.monthly_label = tk.Label(summary_frame, text="This Month: PKR 0", 
                                     font=('Arial', 12), bg='#f8f9fa')
        self.monthly_label.pack(side='left', padx=20)
        
        # Treeview for payments
        self.create_payments_tree(main_frame)
        
        # Action buttons
        action_frame = tk.Frame(main_frame, bg='#ecf0f1')
        action_frame.pack(fill='x', pady=20)
        
        buttons = [
            ("🧾 View Invoice", self.view_invoice, '#3498db'),
            ("✏️ Edit Payment", self.edit_payment, '#f39c12'),
            ("🗑️ Delete", self.delete_payment, '#e74c3c'),
            ("📊 Generate Report", self.generate_report, '#2ecc71'),
        ]
        
        for text, command, color in buttons:
            btn = tk.Button(action_frame, text=text, bg=color, fg='white',
                           font=('Arial', 10), padx=15, pady=8,
                           cursor='hand2', command=command)
            btn.pack(side='left', padx=5)
    
    def create_payments_tree(self, parent):
        tree_frame = tk.Frame(parent)
        tree_frame.pack(fill='both', expand=True)
        
        # Scrollbars
        tree_scroll_y = ttk.Scrollbar(tree_frame)
        tree_scroll_y.pack(side='right', fill='y')
        
        tree_scroll_x = ttk.Scrollbar(tree_frame, orient='horizontal')
        tree_scroll_x.pack(side='bottom', fill='x')
        
        # Treeview
        columns = ('Invoice', 'Member', 'Date', 'Type', 'Amount', 'Discount', 'Final', 'Method', 'Status')
        self.tree = ttk.Treeview(tree_frame, columns=columns, 
                                yscrollcommand=tree_scroll_y.set,
                                xscrollcommand=tree_scroll_x.set,
                                selectmode='browse', height=15)
        
        tree_scroll_y.config(command=self.tree.yview)
        tree_scroll_x.config(command=self.tree.xview)
        
        # Configure columns
        col_widths = [120, 150, 100, 120, 100, 80, 100, 100, 80]
        for col, width in zip(columns, col_widths):
            self.tree.column(col, anchor='center', width=width)
            self.tree.heading(col, text=col)
        
        self.tree.pack(fill='both', expand=True)
        self.tree.bind('<<TreeviewSelect>>', self.on_payment_select)
    
    def load_payments(self):
        # Clear existing items
        for item in self.tree.get_children():
            self.tree.delete(item)
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Build query based on filters
        query = """
            SELECT p.invoice_number, 
                   m.first_name || ' ' || m.last_name as member_name,
                   p.payment_date, p.payment_type, p.amount, 
                   p.discount_applied, p.final_amount,
                   p.payment_method, p.status
            FROM payments p
            JOIN members m ON p.member_id = m.member_id
            WHERE 1=1
        """
        params = []
        
        month_filter = self.month_var.get()
        year_filter = self.year_var.get()
        type_filter = self.type_var.get()
        
        if month_filter != "All" and year_filter != "All":
            month_num = datetime.strptime(month_filter, '%B').month
            query += " AND strftime('%m', payment_date) = ? AND strftime('%Y', payment_date) = ?"
            params.extend([f"{month_num:02d}", str(year_filter)])
        elif year_filter != "All":
            query += " AND strftime('%Y', payment_date) = ?"
            params.append(str(year_filter))
        
        if type_filter != "All":
            query += " AND p.payment_type = ?"
            params.append(type_filter)
        
        query += " ORDER BY p.payment_date DESC"
        
        cursor.execute(query, params)
        payments = cursor.fetchall()
        
        # Calculate totals
        cursor.execute("SELECT SUM(final_amount) FROM payments WHERE status='Completed'")
        total_revenue = cursor.fetchone()[0] or 0
        
        current_month = datetime.now().strftime('%Y-%m')
        cursor.execute("""
            SELECT SUM(final_amount) FROM payments 
            WHERE strftime('%Y-%m', payment_date) = ? AND status='Completed'
        """, (current_month,))
        monthly_revenue = cursor.fetchone()[0] or 0
        
        conn.close()
        
        # Update summary labels
        self.total_label.config(text=f"Total Revenue: PKR {total_revenue:,.2f}")
        self.monthly_label.config(text=f"This Month: PKR {monthly_revenue:,.2f}")
        
        # Insert into treeview
        for payment in payments:
            formatted_payment = (
                payment[0],
                payment[1],
                payment[2],
                payment[3],
                f"PKR {payment[4]:,.2f}",
                f"PKR {payment[5]:,.2f}",
                f"PKR {payment[6]:,.2f}",
                payment[7],
                payment[8]
            )
            self.tree.insert('', 'end', values=formatted_payment)
    
    def on_payment_select(self, event):
        selection = self.tree.selection()
        if selection:
            item = self.tree.item(selection[0])
            invoice_number = item['values'][0]
            self.load_payment_details(invoice_number)
    
    def load_payment_details(self, invoice_number):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM payments WHERE invoice_number = ?", (invoice_number,))
        payment_data = cursor.fetchone()
        conn.close()
        
        if payment_data:
            columns = [desc[0] for desc in cursor.description]
            self.current_payment = dict(zip(columns, payment_data))
    
    def record_payment(self):
        self.show_payment_form()
        
    def show_payment_form(self, edit_mode=False):
        form_window = tk.Toplevel(self.parent)
        form_window.title("Edit Payment" if edit_mode else "Record Payment")
        form_window.geometry("500x520")
        form_window.configure(bg='#ecf0f1')

        form_window.transient(self.parent)
        form_window.grab_set()

        form_frame = tk.Frame(form_window, bg='#ecf0f1', padx=30, pady=30)
        form_frame.pack(fill='both', expand=True)

        tk.Label(
            form_frame,
            text="Edit Payment" if edit_mode else "Record New Payment",
            font=('Arial', 18, 'bold'),
            bg='#ecf0f1'
        ).pack(anchor='w', pady=(0, 20))

        # ------------------ Fields frame (GRID) ------------------
        fields_frame = tk.Frame(form_frame, bg='#ecf0f1')
        fields_frame.pack(fill='both', expand=True)

        # Get active members
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "SELECT member_id, first_name || ' ' || last_name FROM members WHERE status='Active'"
        )
        members = cursor.fetchall()
        conn.close()

        fields = [
            ("Member:", "member_id", "combo", [(m[0], m[1]) for m in members]),
            ("Payment Type:", "payment_type", "combo",
            ["Membership", "Session", "Personal Training", "Other"]),
            ("Amount (PKR):", "amount", "entry"),
            ("Discount (PKR):", "discount_applied", "entry", "0"),
            ("Payment Method:", "payment_method", "combo", ["Cash", "Card"]),
            ("Subscription Period:", "subscription_period", "combo",
            ["Monthly", "Quarterly", "Annual", "One-time"]),
            ("Date:", "payment_date", "date")
        ]

        self.form_vars = {}
        row = 0

        for label_text, field_name, field_type, *options in fields:
            tk.Label(
                fields_frame,
                text=label_text,
                font=('Arial', 11),
                bg='#ecf0f1'
            ).grid(row=row, column=0, sticky='w', pady=6)

            if field_type == "combo":
                var = tk.StringVar()

                display_values = (
                    [opt[1] for opt in options[0]]
                    if options and isinstance(options[0][0], tuple)
                    else options[0]
                )

                combo = ttk.Combobox(
                    fields_frame,
                    textvariable=var,
                    values=display_values,
                    state="readonly",
                    width=30
                )
                combo.grid(row=row, column=1, pady=6, padx=(10, 0))

                if options and isinstance(options[0][0], tuple):
                    self.form_vars[f"{field_name}_mapping"] = {
                        opt[1]: opt[0] for opt in options[0]
                    }

                self.form_vars[field_name] = var

            elif field_type == "entry":
                var = tk.StringVar(value=options[0] if options else "")
                ttk.Entry(
                    fields_frame,
                    textvariable=var,
                    width=30
                ).grid(row=row, column=1, pady=6, padx=(10, 0))
                self.form_vars[field_name] = var

            elif field_type == "date":
                var = tk.StringVar(value=datetime.now().strftime('%Y-%m-%d'))
                ttk.Entry(
                    fields_frame,
                    textvariable=var,
                    width=30
                ).grid(row=row, column=1, pady=6, padx=(10, 0))

                tk.Button(
                    fields_frame,
                    text="📅",
                    command=lambda v=var: self.pick_date(v)
                ).grid(row=row, column=2, padx=5)

                self.form_vars[field_name] = var

            row += 1

        # ------------------ Prefill if edit ------------------
        if edit_mode:
            self.fill_payment_form()

        # ------------------ Calculate section ------------------
        calc_frame = tk.Frame(form_frame, bg='#ecf0f1')
        calc_frame.pack(pady=12)

        tk.Button(
            calc_frame,
            text="Calculate Final Amount",
            bg='#3498db',
            fg='white',
            command=self.calculate_final_amount
        ).pack(side='left', padx=5)

        self.final_amount_var = tk.StringVar(value="0.00")

        tk.Label(
            calc_frame,
            text="Final Amount: PKR",
            font=('Arial', 11),
            bg='#ecf0f1'
        ).pack(side='left', padx=(20, 5))

        tk.Label(
            calc_frame,
            textvariable=self.final_amount_var,
            font=('Arial', 11, 'bold'),
            bg='#ecf0f1',
            fg='#2ecc71'
        ).pack(side='left')

        # ------------------ Buttons ------------------
        button_frame = tk.Frame(form_frame, bg='#ecf0f1')
        button_frame.pack(pady=30)

        tk.Button(
            button_frame,
            text="Save",
            bg="#2ecc71",
            fg="white",
            font=('Arial', 11, 'bold'),
            padx=30,
            pady=10,
            command=lambda: self.save_payment(form_window, edit_mode)  # pass edit_mode
        ).pack(side='left', padx=10)



        tk.Button(
            button_frame,
            text="Cancel",
            bg='#95a5a6',
            fg='white',
            font=('Arial', 11),
            padx=30,
            pady=10,
            command=form_window.destroy
        ).pack(side='left', padx=10)

    def fill_payment_form(self):
        if not self.current_payment:
            return

        for field, widget in self.form_vars.items():
            if field.endswith('_mapping'):
                continue

            value = self.current_payment.get(field, "")

            if isinstance(widget, tk.StringVar):
                widget.set(str(value))

    def pick_date(self, var):
        var.set(datetime.now().strftime('%Y-%m-%d'))
    
    def calculate_final_amount(self):
        try:
            amount = float(self.form_vars['amount'].get() or 0)
            discount = float(self.form_vars.get('discount_applied', tk.StringVar(value="0")).get() or 0)
            final_amount = max(0, amount - discount)
            self.final_amount_var.set(f"{final_amount:,.2f}")
        except ValueError:
            messagebox.showerror("Error", "Please enter valid numbers for amount and discount")
    
    def save_payment(self, window, edit_mode=False):

        if edit_mode and not self.current_payment:
            messagebox.showerror("Error", "No payment selected for editing!")
            return

        # Collect form data
        payment_data = {}

        for field_name, widget in self.form_vars.items():
            if field_name.endswith('_mapping'):
                continue

            if isinstance(widget, tk.StringVar):
                value = widget.get()
                if field_name == 'member_id':
                    mapping_key = f"{field_name}_mapping"
                    if mapping_key in self.form_vars:
                        payment_data[field_name] = self.form_vars[mapping_key].get(value, value)
                    else:
                        payment_data[field_name] = value
                else:
                    payment_data[field_name] = value

            elif isinstance(widget, tk.Text):
                payment_data['notes'] = widget.get('1.0', 'end-1c')

        # Validate required fields
        required = ['member_id', 'payment_type', 'amount']
        for field in required:
            if not payment_data.get(field):
                messagebox.showerror("Error", f"{field.replace('_', ' ').title()} is required!")
                return

        # Calculate final amount
        try:
            amount = float(payment_data['amount'])
            discount = float(payment_data.get('discount_applied', 0) or 0)
            payment_data['final_amount'] = amount - discount
        except ValueError:
            messagebox.showerror("Error", "Invalid amount values")
            return

        # Database operation
        conn = self.db.get_connection()
        cursor = conn.cursor()

        try:
            if edit_mode:
                # UPDATE existing record
                payment_id = self.current_payment['payment_id']
                columns = ', '.join([f"{k}=?" for k in payment_data.keys()])
                cursor.execute(f"""
                    UPDATE payments
                    SET {columns}
                    WHERE payment_id=?
                """, list(payment_data.values()) + [payment_id])
                conn.commit()
                messagebox.showinfo("Success", "Payment updated successfully!")
            else:
                # INSERT new record
                payment_data['invoice_number'] = f"INV-{datetime.now().strftime('%Y%m%d%H%M%S')}"
                payment_data['status'] = 'Completed'
                columns = ', '.join(payment_data.keys())
                placeholders = ', '.join(['?' for _ in payment_data])
                cursor.execute(f"""
                    INSERT INTO payments ({columns})
                    VALUES ({placeholders})
                """, list(payment_data.values()))
                conn.commit()
                messagebox.showinfo("Success", "Payment recorded successfully!")

                # Update member's expiry if membership
                if payment_data['payment_type'] == 'Membership':
                    self.update_member_expiry(payment_data['member_id'], payment_data['subscription_period'])

            self.load_payments()
            window.destroy()

        except Exception as e:
            conn.rollback()
            messagebox.showerror("Database Error", f"Error saving payment: {str(e)}")
        finally:
            conn.close()

    
    def update_member_expiry(self, member_id, period):
        from datetime import datetime, timedelta
        
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get current expiry
        cursor.execute("SELECT expiry_date FROM members WHERE member_id = ?", (member_id,))
        result = cursor.fetchone()
        
        if result:
            current_expiry = result[0]
            if current_expiry:
                base_date = datetime.strptime(current_expiry, '%Y-%m-%d')
            else:
                base_date = datetime.now()
            
            # Add period
            if period == 'Monthly':
                new_expiry = base_date + timedelta(days=30)
            elif period == 'Quarterly':
                new_expiry = base_date + timedelta(days=90)
            elif period == 'Annual':
                new_expiry = base_date + timedelta(days=365)
            else:
                new_expiry = base_date + timedelta(days=30)
            
            # Update member
            cursor.execute("""
                UPDATE members 
                SET expiry_date = ?, status = 'Active'
                WHERE member_id = ?
            """, (new_expiry.strftime('%Y-%m-%d'), member_id))
            
            conn.commit()
        
        conn.close()
    
    def view_invoice(self):
        if not self.current_payment:
            messagebox.showinfo("Select Payment", "Please select a payment first")
            return
        
        # Display invoice details
        invoice_window = tk.Toplevel(self.parent)
        invoice_window.title(f"Invoice - {self.current_payment['invoice_number']}")
        invoice_window.geometry("500x400")
        
        # Get member details
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT first_name, last_name, email, phone 
            FROM members 
            WHERE member_id = ?
        """, (self.current_payment['member_id'],))
        member = cursor.fetchone()
        conn.close()
        
        # Display invoice
        invoice_text = f"""
        M9 FITNESS - INVOICE
        =====================
        Invoice: {self.current_payment['invoice_number']}
        Date: {self.current_payment['payment_date']}
        
        Member: {member[0]} {member[1]}
        Email: {member[2]}
        Phone: {member[3]}
        
        =====================
        Payment Type: {self.current_payment['payment_type']}
        Amount: PKR {self.current_payment['amount']:,.2f}
        Discount: PKR {self.current_payment['discount_applied']:,.2f}
        Final Amount: PKR {self.current_payment['final_amount']:,.2f}
        
        Payment Method: {self.current_payment['payment_method']}
        Status: {self.current_payment['status']}
        
        Thank you for your payment!
        """
        
        text_widget = tk.Text(invoice_window, font=('Courier', 11))
        text_widget.pack(fill='both', expand=True, padx=20, pady=20)
        text_widget.insert('1.0', invoice_text)
        text_widget.config(state='disabled')
    
    def edit_payment(self):
        if not self.current_payment:
            messagebox.showinfo("Select Payment", "Please select a payment first")
            return

        self.show_payment_form(edit_mode=True)

    
    def delete_payment(self):
        if not self.current_payment:
            messagebox.showinfo("Select Payment", "Please select a payment first")
            return
        
        if messagebox.askyesno("Delete Payment", 
                              f"Delete payment {self.current_payment['invoice_number']}?"):
            conn = self.db.get_connection()
            cursor = conn.cursor()
            
            try:
                cursor.execute("DELETE FROM payments WHERE payment_id = ?", 
                             (self.current_payment['payment_id'],))
                
                conn.commit()
                messagebox.showinfo("Success", "Payment deleted successfully!")
                self.load_payments()
                
            except Exception as e:
                conn.rollback()
                messagebox.showerror("Error", f"Failed to delete payment: {str(e)}")
            finally:
                conn.close()
    
    def generate_report(self):
        # Generate payment report
        from datetime import datetime
        import matplotlib.pyplot as plt
        from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
        
        report_window = tk.Toplevel(self.parent)
        report_window.title("Payment Report")
        report_window.geometry("800x600")
        
        # Get payment data for last 6 months
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        months = []
        revenues = []
        
        for i in range(5, -1, -1):
            date = datetime.now() - timedelta(days=30*i)
            month_key = date.strftime('%Y-%m')
            month_name = date.strftime('%b %Y')
            
            cursor.execute("""
                SELECT SUM(final_amount) FROM payments 
                WHERE strftime('%Y-%m', payment_date) = ? AND status='Completed'
            """, (month_key,))
            
            revenue = cursor.fetchone()[0] or 0
            months.append(month_name)
            revenues.append(revenue)
        
        conn.close()
        
        # Create chart
        fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(10, 5))
        
        # Bar chart
        ax1.bar(months, revenues, color='#3498db')
        ax1.set_title('Monthly Revenue (Last 6 Months)')
        ax1.set_ylabel('Amount (PKR)')
        ax1.tick_params(axis='x', rotation=45)
        ax1.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'PKR {x:,.0f}'))
        
        # Pie chart by payment type
        conn = self.db.get_connection()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT payment_type, SUM(final_amount) 
            FROM payments 
            WHERE status='Completed'
            GROUP BY payment_type
        """)
        
        type_data = cursor.fetchall()
        conn.close()
        
        if type_data:
            types = [row[0] for row in type_data]
            amounts = [row[1] for row in type_data]
            colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12']
            
            ax2.pie(amounts, labels=types, autopct='%1.1f%%', colors=colors[:len(types)])
            ax2.set_title('Revenue by Payment Type')
        
        plt.tight_layout()
        
        # Embed chart in tkinter window
        canvas = FigureCanvasTkAgg(fig, report_window)
        canvas.draw()
        canvas.get_tk_widget().pack(fill='both', expand=True, padx=20, pady=20)
        
        # Export button
        tk.Button(report_window, text="Export as PDF", bg='#3498db', fg='white',
                 command=lambda: self.export_report(months, revenues)).pack(pady=10)
    
    def export_report(self, months, revenues):
        from datetime import datetime
        
        report_text = f"""
        M9 FITNESS - PAYMENT REPORT
        Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
        
        Monthly Revenue:
        {'='*40}
        """
        
        for month, revenue in zip(months, revenues):
            report_text += f"{month}: PKR {revenue:,.2f}\n"
        
        report_text += f"""
        {'='*40}
        Total (6 months): PKR {sum(revenues):,.2f}
        Average monthly: PKR {sum(revenues)/len(revenues):,.2f}
        """
        
        filename = f"payment_report_{datetime.now().strftime('%Y%m%d_%H%M%S')}.txt"
        
        try:
            with open(filename, 'w') as f:
                f.write(report_text)
            messagebox.showinfo("Success", f"Report saved as {filename}")
        except Exception as e:
            messagebox.showerror("Error", f"Failed to save report: {str(e)}")