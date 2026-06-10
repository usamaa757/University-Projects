import tkinter as tk
from tkinter import ttk, messagebox
from database import Database
from styles import Styles
from datetime import datetime, timedelta
import calendar

class Reports:
    def __init__(self, parent, username):
        self.parent = parent
        self.username = username
        self.db = Database()
        self.styles = Styles()
        self.setup_ui()
    
    def setup_ui(self):
        # Main container
        main_frame = tk.Frame(self.parent, bg=self.styles.LIGHT)
        main_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Report type selection
        type_frame = tk.LabelFrame(main_frame, text="Select Report Type",
                                  font=self.styles.HEADING_FONT,
                                  bg=self.styles.LIGHT,
                                  padx=10, pady=10)
        type_frame.pack(fill=tk.X, pady=10)
        
        self.report_type = tk.StringVar(value="daily")
        
        report_types = [
            ("Daily Report", "daily"),
            ("Weekly Report", "weekly"),
            ("Monthly Report", "monthly"),
            ("Yearly Report", "yearly"),
            ("Stock Report", "stock"),
            ("Profit Report", "profit")
        ]
        
        for i, (text, value) in enumerate(report_types):
            tk.Radiobutton(type_frame, text=text, variable=self.report_type,
                          value=value, bg=self.styles.LIGHT,
                          command=self.on_report_type_change).grid(row=i//3, column=i%3, padx=10, pady=5, sticky=tk.W)
        
        # Date selection frame
        self.date_frame = tk.LabelFrame(main_frame, text="Select Date",
                                       font=self.styles.HEADING_FONT,
                                       bg=self.styles.LIGHT,
                                       padx=10, pady=10)
        self.date_frame.pack(fill=tk.X, pady=10)
        
        # Year selection
        tk.Label(self.date_frame, text="Year:", bg=self.styles.LIGHT).grid(row=0, column=0, padx=5, pady=5, sticky=tk.W)
        self.year_var = tk.StringVar(value=str(datetime.now().year))
        self.year_combo = ttk.Combobox(self.date_frame, textvariable=self.year_var, width=10, state="readonly")
        self.year_combo['values'] = [str(year) for year in range(2020, 2031)]
        self.year_combo.grid(row=0, column=1, padx=5, pady=5)
        self.year_combo.bind('<<ComboboxSelected>>', self.on_year_change)
        
        # Month selection
        tk.Label(self.date_frame, text="Month:", bg=self.styles.LIGHT).grid(row=0, column=2, padx=5, pady=5, sticky=tk.W)
        self.month_var = tk.StringVar(value=str(datetime.now().month))
        self.month_combo = ttk.Combobox(self.date_frame, textvariable=self.month_var, width=10, state="readonly")
        self.month_combo['values'] = [str(i) for i in range(1, 13)]
        self.month_combo.grid(row=0, column=3, padx=5, pady=5)
        self.month_combo.bind('<<ComboboxSelected>>', self.on_month_change)
        
        # Day selection
        tk.Label(self.date_frame, text="Day:", bg=self.styles.LIGHT).grid(row=0, column=4, padx=5, pady=5, sticky=tk.W)
        self.day_var = tk.StringVar(value=str(datetime.now().day))
        self.day_combo = ttk.Combobox(self.date_frame, textvariable=self.day_var, width=10, state="readonly")
        self.day_combo.grid(row=0, column=5, padx=5, pady=5)
        self.update_days()
        
        # Generate button
        tk.Button(main_frame, text="Generate Report", command=self.generate_report,
                 **self.styles.BTN_PRIMARY).pack(pady=10)
        
        # Report display area
        report_frame = tk.LabelFrame(main_frame, text="Report",
                                    font=self.styles.HEADING_FONT,
                                    bg=self.styles.LIGHT,
                                    padx=10, pady=10)
        report_frame.pack(fill=tk.BOTH, expand=True, pady=10)
        
        # Text widget for report display
        self.report_text = tk.Text(report_frame, height=20, width=80,
                                  font=("Courier", 10), bg="white", relief="solid")
        scrollbar = tk.Scrollbar(report_frame, command=self.report_text.yview)
        self.report_text.configure(yscrollcommand=scrollbar.set)
        
        self.report_text.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        # Print button
        tk.Button(main_frame, text="Print Report", command=self.print_report,
                 **self.styles.BTN_SECONDARY).pack(pady=5)
    
    def on_report_type_change(self):
        report_type = self.report_type.get()
        if report_type == "stock":
            # Hide date selection for stock report
            self.date_frame.pack_forget()
        else:
            self.date_frame.pack(fill=tk.X, pady=10, before=self.parent.winfo_children()[-2])
    
    def on_year_change(self, event=None):
        self.update_days()
    
    def on_month_change(self, event=None):
        self.update_days()
    
    def update_days(self):
        try:
            year = int(self.year_var.get())
            month = int(self.month_var.get())
            days_in_month = calendar.monthrange(year, month)[1]
            self.day_combo['values'] = [str(day) for day in range(1, days_in_month + 1)]
            
            current_day = int(self.day_var.get())
            if current_day > days_in_month:
                self.day_var.set(str(days_in_month))
        except:
            pass
    
    def generate_report(self):
        report_type = self.report_type.get()
        self.report_text.delete(1.0, tk.END)
        
        try:
            if report_type == "stock":
                self.generate_stock_report()
            elif report_type == "profit":
                self.generate_profit_report()
            else:
                self.generate_date_based_report(report_type)
        except Exception as e:
            messagebox.showerror("Error", f"Failed to generate report: {str(e)}")
    
    def generate_stock_report(self):
        # Get all items
        items = self.db.fetch_all("""
            SELECT name, target_group, season, stock, purchase_price, sale_price
            FROM clothing_items
            ORDER BY target_group, season, name
        """)
        
        report = "=" * 80 + "\n"
        report += "STOCK REPORT\n".center(80) + "\n"
        report += f"Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
        report += "=" * 80 + "\n\n"
        
        total_value = 0
        low_stock_items = []
        
        # Group by target group
        groups = {"Men": [], "Women": [], "Kids": []}
        
        for item in items:
            name, target_group, season, stock, purchase_price, sale_price = item
            item_value = stock * purchase_price
            total_value += item_value
            
            groups[target_group].append(item)
            
            if stock < 10:  # Low stock threshold
                low_stock_items.append((name, stock))
        
        # Print by group
        for group_name, group_items in groups.items():
            if group_items:
                report += f"\n{group_name.upper()}:\n"
                report += "-" * 80 + "\n"
                report += f"{'Item Name':<30} {'Season':<10} {'Stock':<10} {'Purchase Price':<15} {'Sale Price':<10}\n"
                report += "-" * 80 + "\n"
                
                for item in group_items:
                    name, _, season, stock, purchase_price, sale_price = item
                    report += f"{name[:30]:<30} {season:<10} {stock:<10} ${purchase_price:<14.2f} ${sale_price:<9.2f}\n"
        
        # Summary
        report += "\n" + "=" * 80 + "\n"
        report += "SUMMARY\n"
        report += "-" * 80 + "\n"
        report += f"Total Items in Stock: {len(items)}\n"
        report += f"Total Stock Value: ${total_value:.2f}\n"
        
        if low_stock_items:
            report += "\nLOW STOCK ITEMS (Below 10 units):\n"
            for item_name, stock in low_stock_items:
                report += f"  • {item_name}: {stock} units\n"
        
        report += "=" * 80 + "\n"
        
        self.report_text.insert(1.0, report)
    
    def generate_profit_report(self):
        try:
            year = int(self.year_var.get())
            month = int(self.month_var.get())
            day = int(self.day_var.get())
            
            date_str = f"{year:04d}-{month:02d}-{day:02d}"
            
            # Get sales for the date
            sales_query = """
                SELECT SUM(s.total_amount), SUM(s.quantity * i.purchase_price), COUNT(*)
                FROM sales s
                JOIN clothing_items i ON s.item_id = i.id
                WHERE s.sale_date = ?
            """
            
            sales_result = self.db.fetch_one(sales_query, (date_str,))
            
            if sales_result and sales_result[0]:
                total_sales = sales_result[0] or 0
                total_cost = sales_result[1] or 0
                total_transactions = sales_result[2] or 0
                profit = total_sales - total_cost
                profit_margin = (profit / total_sales * 100) if total_sales > 0 else 0
            else:
                total_sales = total_cost = profit = profit_margin = total_transactions = 0
            
            report = "=" * 80 + "\n"
            report += "PROFIT REPORT\n".center(80) + "\n"
            report += f"For Date: {date_str}\n"
            report += f"Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
            report += "=" * 80 + "\n\n"
            
            report += f"{'Metric':<30} {'Amount':<20}\n"
            report += "-" * 50 + "\n"
            report += f"{'Total Sales':<30} ${total_sales:<19.2f}\n"
            report += f"{'Total Cost':<30} ${total_cost:<19.2f}\n"
            report += f"{'Gross Profit':<30} ${profit:<19.2f}\n"
            report += f"{'Profit Margin':<30} {profit_margin:<19.2f}%\n"
            report += f"{'Total Transactions':<30} {total_transactions:<20}\n"
            
            # Get top selling items
            top_items_query = """
                SELECT i.name, SUM(s.quantity), SUM(s.total_amount)
                FROM sales s
                JOIN clothing_items i ON s.item_id = i.id
                WHERE s.sale_date = ?
                GROUP BY i.name
                ORDER BY SUM(s.total_amount) DESC
                LIMIT 5
            """
            
            top_items = self.db.fetch_all(top_items_query, (date_str,))
            
            if top_items:
                report += "\n" + "-" * 80 + "\n"
                report += "TOP SELLING ITEMS:\n"
                report += "-" * 80 + "\n"
                report += f"{'Item Name':<30} {'Quantity':<15} {'Revenue':<15}\n"
                report += "-" * 80 + "\n"
                
                for item in top_items:
                    report += f"{item[0][:30]:<30} {item[1]:<15} ${item[2]:<14.2f}\n"
            
            report += "=" * 80 + "\n"
            
            self.report_text.insert(1.0, report)
            
        except Exception as e:
            messagebox.showerror("Error", f"Invalid date: {str(e)}")
    
    def generate_date_based_report(self, report_type):
        try:
            year = int(self.year_var.get())
            month = int(self.month_var.get())
            day = int(self.day_var.get())
            
            if report_type == "daily":
                date_str = f"{year:04d}-{month:02d}-{day:02d}"
                title = f"DAILY REPORT - {date_str}"
                sales_where = "s.sale_date = ?"
                purchases_where = "p.purchase_date = ?"
                param = (date_str,)
            elif report_type == "weekly":
                # Calculate week start and end
                date = datetime(year, month, day)
                start_date = date - timedelta(days=date.weekday())
                end_date = start_date + timedelta(days=6)
                title = f"WEEKLY REPORT - Week of {start_date.strftime('%Y-%m-%d')} to {end_date.strftime('%Y-%m-%d')}"
                sales_where = "s.sale_date BETWEEN ? AND ?"
                purchases_where = "p.purchase_date BETWEEN ? AND ?"
                param = (start_date.strftime('%Y-%m-%d'), end_date.strftime('%Y-%m-%d'))
            elif report_type == "monthly":
                title = f"MONTHLY REPORT - {year:04d}-{month:02d}"
                sales_where = "strftime('%Y-%m', s.sale_date) = ?"
                purchases_where = "strftime('%Y-%m', p.purchase_date) = ?"
                param = (f"{year:04d}-{month:02d}",)
            else:  # yearly
                title = f"YEARLY REPORT - {year}"
                sales_where = "strftime('%Y', s.sale_date) = ?"
                purchases_where = "strftime('%Y', p.purchase_date) = ?"
                param = (str(year),)
            
            # Get sales data
            sales_query = f"""
                SELECT SUM(s.quantity), SUM(s.total_amount), COUNT(*)
                FROM sales s
                WHERE {sales_where}
            """
            
            sales_result = self.db.fetch_one(sales_query, param)
            
            # Get purchases data
            purchases_query = f"""
                SELECT SUM(p.quantity), SUM(p.total_amount), COUNT(*)
                FROM purchases p
                WHERE {purchases_where}
            """
            
            purchases_result = self.db.fetch_one(purchases_query, param)
            
            total_sold = sales_result[0] or 0 if sales_result else 0
            total_sales_amount = sales_result[1] or 0 if sales_result else 0
            total_sales_count = sales_result[2] or 0 if sales_result else 0
            
            total_purchased = purchases_result[0] or 0 if purchases_result else 0
            total_purchases_amount = purchases_result[1] or 0 if purchases_result else 0
            total_purchases_count = purchases_result[2] or 0 if purchases_result else 0
            
            # Get current stock
            stock_query = "SELECT SUM(stock) FROM clothing_items"
            current_stock = self.db.fetch_one(stock_query)[0] or 0
            
            report = "=" * 80 + "\n"
            report += title.center(80) + "\n"
            report += f"Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n"
            report += "=" * 80 + "\n\n"
            
            report += "SALES SUMMARY:\n"
            report += "-" * 50 + "\n"
            report += f"Total Items Sold: {total_sold}\n"
            report += f"Total Sales Amount: ${total_sales_amount:.2f}\n"
            report += f"Number of Sales Transactions: {total_sales_count}\n"
            report += f"Average Sale Value: ${(total_sales_amount/total_sales_count if total_sales_count > 0 else 0):.2f}\n\n"
            
            report += "PURCHASES SUMMARY:\n"
            report += "-" * 50 + "\n"
            report += f"Total Items Purchased: {total_purchased}\n"
            report += f"Total Purchases Amount: ${total_purchases_amount:.2f}\n"
            report += f"Number of Purchase Transactions: {total_purchases_count}\n\n"
            
            report += "INVENTORY STATUS:\n"
            report += "-" * 50 + "\n"
            report += f"Current Total Stock: {current_stock}\n"
            report += f"Net Stock Change: {total_purchased - total_sold}\n\n"
            
            # Get top categories
            if report_type in ["monthly", "yearly"]:
                categories_query = f"""
                    SELECT i.target_group, SUM(s.quantity), SUM(s.total_amount)
                    FROM sales s
                    JOIN clothing_items i ON s.item_id = i.id
                    WHERE {sales_where}
                    GROUP BY i.target_group
                    ORDER BY SUM(s.total_amount) DESC
                """
                
                categories = self.db.fetch_all(categories_query, param)
                
                if categories:
                    report += "SALES BY CATEGORY:\n"
                    report += "-" * 50 + "\n"
                    for cat in categories:
                        report += f"{cat[0]}: {cat[1]} items, ${cat[2]:.2f}\n"
                    report += "\n"
            
            report += "=" * 80 + "\n"
            
            self.report_text.insert(1.0, report)
            
        except Exception as e:
            messagebox.showerror("Error", f"Invalid date or error generating report: {str(e)}")
    
    def print_report(self):
        # For now, just show a message. In real application, implement actual printing.
        messagebox.showinfo("Print", "Report printing functionality would be implemented here.")