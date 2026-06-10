import tkinter as tk
from tkinter import ttk, messagebox
from database import Database
from styles import Styles
from datetime import datetime

class SalePurchase:
    def __init__(self, parent, username, user_type):
        self.parent = parent
        self.username = username
        self.user_type = user_type
        self.db = Database()
        self.styles = Styles()
        self.selected_item_id = None
        self.setup_ui()
    
    def setup_ui(self):
        # Notebook for tabs
        self.notebook = ttk.Notebook(self.parent)
        self.notebook.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Create tabs
        self.sale_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        self.purchase_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        self.history_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        
        self.notebook.add(self.sale_tab, text="Make Sale")
        self.notebook.add(self.purchase_tab, text="Make Purchase")
        self.notebook.add(self.history_tab, text="Transaction History")
        
        self.setup_sale_tab()
        self.setup_purchase_tab()
        self.setup_history_tab()
        self.load_items_for_selection()
    
    def setup_sale_tab(self):
        # Item selection frame
        select_frame = tk.LabelFrame(self.sale_tab, text="Select Item",
                                    font=self.styles.HEADING_FONT,
                                    bg=self.styles.LIGHT,
                                    padx=10, pady=10)
        select_frame.pack(fill=tk.X, padx=10, pady=10)
        
        # Item combobox
        tk.Label(select_frame, text="Item:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.sale_item_combo = ttk.Combobox(select_frame, width=40, state="readonly")
        self.sale_item_combo.pack(side=tk.LEFT, padx=5)
        self.sale_item_combo.bind('<<ComboboxSelected>>', self.on_sale_item_select)
        
        # Item details frame
        details_frame = tk.LabelFrame(self.sale_tab, text="Item Details",
                                     font=self.styles.HEADING_FONT,
                                     bg=self.styles.LIGHT,
                                     padx=10, pady=10)
        details_frame.pack(fill=tk.X, padx=10, pady=10)
        
        # Details labels
        self.sale_details = {
            'stock': tk.StringVar(),
            'price': tk.StringVar(),
            'available': tk.StringVar(value="0")
        }
        
        tk.Label(details_frame, text="Available Stock:", bg=self.styles.LIGHT).grid(row=0, column=0, sticky=tk.W, pady=5)
        tk.Label(details_frame, textvariable=self.sale_details['stock'], bg=self.styles.LIGHT,
                font=self.styles.NORMAL_FONT, fg=self.styles.PRIMARY).grid(row=0, column=1, sticky=tk.W, pady=5)
        
        tk.Label(details_frame, text="Sale Price:", bg=self.styles.LIGHT).grid(row=1, column=0, sticky=tk.W, pady=5)
        tk.Label(details_frame, textvariable=self.sale_details['price'], bg=self.styles.LIGHT,
                font=self.styles.NORMAL_FONT, fg=self.styles.PRIMARY).grid(row=1, column=1, sticky=tk.W, pady=5)
        
        # Quantity frame
        qty_frame = tk.LabelFrame(self.sale_tab, text="Sale Quantity",
                                 font=self.styles.HEADING_FONT,
                                 bg=self.styles.LIGHT,
                                 padx=10, pady=10)
        qty_frame.pack(fill=tk.X, padx=10, pady=10)
        
        tk.Label(qty_frame, text="Quantity:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.sale_qty = tk.Spinbox(qty_frame, from_=1, to=1000, width=10, **self.styles.ENTRY_STYLE)
        self.sale_qty.pack(side=tk.LEFT, padx=5)
        
        # Calculate button
        tk.Button(qty_frame, text="Calculate Total", command=self.calculate_sale_total,
                 **self.styles.BTN_SECONDARY).pack(side=tk.LEFT, padx=20)
        
        # Total amount
        tk.Label(qty_frame, text="Total Amount:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.sale_total = tk.Label(qty_frame, text="0.00", bg=self.styles.LIGHT,
                                  font=self.styles.NORMAL_FONT, fg=self.styles.SUCCESS)
        self.sale_total.pack(side=tk.LEFT, padx=5)
        
        # Sale button
        tk.Button(self.sale_tab, text="Complete Sale", command=self.make_sale,
                 **self.styles.BTN_PRIMARY).pack(pady=20)
    
    def setup_purchase_tab(self):
        # Item selection frame
        select_frame = tk.LabelFrame(self.purchase_tab, text="Select Item",
                                    font=self.styles.HEADING_FONT,
                                    bg=self.styles.LIGHT,
                                    padx=10, pady=10)
        select_frame.pack(fill=tk.X, padx=10, pady=10)
        
        tk.Label(select_frame, text="Item:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.purchase_item_combo = ttk.Combobox(select_frame, width=40, state="readonly")
        self.purchase_item_combo.pack(side=tk.LEFT, padx=5)
        self.purchase_item_combo.bind('<<ComboboxSelected>>', self.on_purchase_item_select)
        
        # Purchase details frame
        details_frame = tk.LabelFrame(self.purchase_tab, text="Purchase Details",
                                     font=self.styles.HEADING_FONT,
                                     bg=self.styles.LIGHT,
                                     padx=10, pady=10)
        details_frame.pack(fill=tk.X, padx=10, pady=10)
        
        # Purchase price
        tk.Label(details_frame, text="Purchase Price:", bg=self.styles.LIGHT).grid(row=0, column=0, sticky=tk.W, pady=5)
        self.purchase_price_entry = tk.Entry(details_frame, width=20, **self.styles.ENTRY_STYLE)
        self.purchase_price_entry.grid(row=0, column=1, padx=5, pady=5)
        
        # Quantity
        tk.Label(details_frame, text="Quantity:", bg=self.styles.LIGHT).grid(row=1, column=0, sticky=tk.W, pady=5)
        self.purchase_qty = tk.Spinbox(details_frame, from_=1, to=1000, width=10, **self.styles.ENTRY_STYLE)
        self.purchase_qty.grid(row=1, column=1, padx=5, pady=5)
        
        # Calculate button
        tk.Button(details_frame, text="Calculate Total", command=self.calculate_purchase_total,
                 **self.styles.BTN_SECONDARY).grid(row=2, column=0, columnspan=2, pady=10)
        
        # Total amount
        tk.Label(details_frame, text="Total Amount:", bg=self.styles.LIGHT).grid(row=3, column=0, sticky=tk.W, pady=5)
        self.purchase_total = tk.Label(details_frame, text="0.00", bg=self.styles.LIGHT,
                                      font=self.styles.NORMAL_FONT, fg=self.styles.SUCCESS)
        self.purchase_total.grid(row=3, column=1, sticky=tk.W, pady=5)
        
        # Purchase button
        tk.Button(self.purchase_tab, text="Complete Purchase", command=self.make_purchase,
                 **self.styles.BTN_PRIMARY).pack(pady=20)
    
    def setup_history_tab(self):
        # Date filter frame
        filter_frame = tk.Frame(self.history_tab, bg=self.styles.LIGHT)
        filter_frame.pack(fill=tk.X, padx=10, pady=10)
        
        tk.Label(filter_frame, text="Filter by Date:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        
        # Start date
        tk.Label(filter_frame, text="From:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.start_date = tk.Entry(filter_frame, width=12, **self.styles.ENTRY_STYLE)
        self.start_date.pack(side=tk.LEFT, padx=5)
        self.start_date.insert(0, datetime.now().strftime("%Y-%m-01"))
        
        # End date
        tk.Label(filter_frame, text="To:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.end_date = tk.Entry(filter_frame, width=12, **self.styles.ENTRY_STYLE)
        self.end_date.pack(side=tk.LEFT, padx=5)
        self.end_date.insert(0, datetime.now().strftime("%Y-%m-%d"))
        
        # Filter button
        tk.Button(filter_frame, text="Filter", command=self.load_transactions,
                 **self.styles.BTN_SECONDARY).pack(side=tk.LEFT, padx=20)
        
        # Notebook for sales/purchases history
        history_notebook = ttk.Notebook(self.history_tab)
        history_notebook.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Sales history tab
        sales_history_frame = tk.Frame(history_notebook, bg=self.styles.LIGHT)
        history_notebook.add(sales_history_frame, text="Sales History")
        
        # Sales treeview
        sales_columns = ("ID", "Item", "Quantity", "Price", "Total", "Date", "Sold By")
        self.sales_tree = ttk.Treeview(sales_history_frame, columns=sales_columns, show="headings", height=10)
        
        for col in sales_columns:
            self.sales_tree.heading(col, text=col)
            self.sales_tree.column(col, width=100)
        
        sales_scrollbar = ttk.Scrollbar(sales_history_frame, orient=tk.VERTICAL, command=self.sales_tree.yview)
        self.sales_tree.configure(yscrollcommand=sales_scrollbar.set)
        
        self.sales_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        sales_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        # Purchases history tab
        purchases_history_frame = tk.Frame(history_notebook, bg=self.styles.LIGHT)
        history_notebook.add(purchases_history_frame, text="Purchases History")
        
        # Purchases treeview
        purchase_columns = ("ID", "Item", "Quantity", "Price", "Total", "Date", "Purchased By")
        self.purchases_tree = ttk.Treeview(purchases_history_frame, columns=purchase_columns, show="headings", height=10)
        
        for col in purchase_columns:
            self.purchases_tree.heading(col, text=col)
            self.purchases_tree.column(col, width=100)
        
        purchase_scrollbar = ttk.Scrollbar(purchases_history_frame, orient=tk.VERTICAL, command=self.purchases_tree.yview)
        self.purchases_tree.configure(yscrollcommand=purchase_scrollbar.set)
        
        self.purchases_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        purchase_scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        self.load_transactions()
    
    def load_items_for_selection(self):
        items = self.db.fetch_all("SELECT id, name, stock FROM clothing_items WHERE stock > 0 ORDER BY name")
        
        item_list = [f"{item[0]} - {item[1]} (Stock: {item[2]})" for item in items]
        self.sale_item_combo['values'] = item_list
        
        all_items = self.db.fetch_all("SELECT id, name FROM clothing_items ORDER BY name")
        all_item_list = [f"{item[0]} - {item[1]}" for item in all_items]
        self.purchase_item_combo['values'] = all_item_list
    
    def on_sale_item_select(self, event):
        selection = self.sale_item_combo.get()
        if selection:
            item_id = int(selection.split(' - ')[0])
            self.selected_item_id = item_id
            
            # Get item details
            item = self.db.fetch_one(
                "SELECT name, sale_price, stock FROM clothing_items WHERE id = ?",
                (item_id,)
            )
            
            if item:
                self.sale_details['stock'].set(str(item[2]))
                self.sale_details['price'].set(f"${item[1]:.2f}")
                self.sale_qty.delete(0, tk.END)
                self.sale_qty.insert(0, "1")
                self.calculate_sale_total()
    
    def on_purchase_item_select(self, event):
        selection = self.purchase_item_combo.get()
        if selection:
            item_id = int(selection.split(' - ')[0])
            self.selected_item_id = item_id
            
            # Get current purchase price
            item = self.db.fetch_one(
                "SELECT purchase_price FROM clothing_items WHERE id = ?",
                (item_id,)
            )
            
            if item:
                self.purchase_price_entry.delete(0, tk.END)
                self.purchase_price_entry.insert(0, str(item[0]))
                self.purchase_qty.delete(0, tk.END)
                self.purchase_qty.insert(0, "1")
                self.calculate_purchase_total()
    
    def calculate_sale_total(self):
        try:
            qty = int(self.sale_qty.get())
            price_text = self.sale_details['price'].get().replace('$', '')
            price = float(price_text) if price_text else 0
            total = qty * price
            self.sale_total.config(text=f"${total:.2f}")
            
            # Check stock
            stock = int(self.sale_details['stock'].get())
            if qty > stock:
                self.sale_total.config(fg=self.styles.DANGER, text=f"${total:.2f} (Insufficient stock!)")
            else:
                self.sale_total.config(fg=self.styles.SUCCESS, text=f"${total:.2f}")
        except ValueError:
            self.sale_total.config(text="Invalid input")
    
    def calculate_purchase_total(self):
        try:
            qty = int(self.purchase_qty.get())
            price = float(self.purchase_price_entry.get())
            total = qty * price
            self.purchase_total.config(text=f"${total:.2f}")
        except ValueError:
            self.purchase_total.config(text="Invalid input")
    
    def make_sale(self):
        if not self.selected_item_id:
            messagebox.showwarning("Warning", "Please select an item")
            return
        
        try:
            qty = int(self.sale_qty.get())
            stock = int(self.sale_details['stock'].get())
            
            if qty <= 0:
                messagebox.showwarning("Warning", "Quantity must be greater than 0")
                return
            
            if qty > stock:
                messagebox.showerror("Error", "Insufficient stock!")
                return
            
            # Get sale price
            price_text = self.sale_details['price'].get().replace('$', '')
            sale_price = float(price_text) if price_text else 0
            total_amount = qty * sale_price
            
            # Insert sale record
            query = """
                INSERT INTO sales (item_id, quantity, sale_price, total_amount, sale_date, sold_by)
                VALUES (?, ?, ?, ?, DATE('now'), ?)
            """
            
            if self.db.execute_query(query, (self.selected_item_id, qty, sale_price, total_amount, self.username)):
                # Update stock
                self.db.execute_query(
                    "UPDATE clothing_items SET stock = stock - ? WHERE id = ?",
                    (qty, self.selected_item_id)
                )
                
                messagebox.showinfo("Success", f"Sale completed successfully!\nTotal: ${total_amount:.2f}")
                self.load_items_for_selection()
                self.load_transactions()
                
                # Reset form
                self.sale_item_combo.set('')
                self.sale_details['stock'].set('')
                self.sale_details['price'].set('')
                self.sale_qty.delete(0, tk.END)
                self.sale_qty.insert(0, "1")
                self.sale_total.config(text="0.00")
                self.selected_item_id = None
            else:
                messagebox.showerror("Error", "Failed to complete sale")
                
        except ValueError:
            messagebox.showerror("Error", "Invalid input")
    
    def make_purchase(self):
        if not self.selected_item_id:
            messagebox.showwarning("Warning", "Please select an item")
            return
        
        try:
            qty = int(self.purchase_qty.get())
            purchase_price = float(self.purchase_price_entry.get())
            
            if qty <= 0 or purchase_price <= 0:
                messagebox.showwarning("Warning", "Quantity and price must be greater than 0")
                return
            
            total_amount = qty * purchase_price
            
            # Insert purchase record
            query = """
                INSERT INTO purchases (item_id, quantity, purchase_price, total_amount, purchase_date, purchased_by)
                VALUES (?, ?, ?, ?, DATE('now'), ?)
            """
            
            if self.db.execute_query(query, (self.selected_item_id, qty, purchase_price, total_amount, self.username)):
                # Update stock and purchase price
                self.db.execute_query(
                    "UPDATE clothing_items SET stock = stock + ?, purchase_price = ? WHERE id = ?",
                    (qty, purchase_price, self.selected_item_id)
                )
                
                messagebox.showinfo("Success", f"Purchase completed successfully!\nTotal: ${total_amount:.2f}")
                self.load_items_for_selection()
                self.load_transactions()
                
                # Reset form
                self.purchase_item_combo.set('')
                self.purchase_price_entry.delete(0, tk.END)
                self.purchase_qty.delete(0, tk.END)
                self.purchase_qty.insert(0, "1")
                self.purchase_total.config(text="0.00")
                self.selected_item_id = None
            else:
                messagebox.showerror("Error", "Failed to complete purchase")
                
        except ValueError:
            messagebox.showerror("Error", "Invalid input")
    
    def load_transactions(self):
        # Clear trees
        for tree in [self.sales_tree, self.purchases_tree]:
            for item in tree.get_children():
                tree.delete(item)
        
        # Load sales
        start_date = self.start_date.get()
        end_date = self.end_date.get()
        
        sales_query = """
            SELECT s.id, i.name, s.quantity, s.sale_price, s.total_amount, s.sale_date, s.sold_by
            FROM sales s
            JOIN clothing_items i ON s.item_id = i.id
            WHERE s.sale_date BETWEEN ? AND ?
            ORDER BY s.sale_date DESC, s.id DESC
        """
        
        sales = self.db.fetch_all(sales_query, (start_date, end_date))
        for sale in sales:
            formatted_sale = (sale[0], sale[1], sale[2], f"${sale[3]:.2f}", 
                            f"${sale[4]:.2f}", sale[5], sale[6])
            self.sales_tree.insert("", tk.END, values=formatted_sale)
        
        # Load purchases
        purchases_query = """
            SELECT p.id, i.name, p.quantity, p.purchase_price, p.total_amount, p.purchase_date, p.purchased_by
            FROM purchases p
            JOIN clothing_items i ON p.item_id = i.id
            WHERE p.purchase_date BETWEEN ? AND ?
            ORDER BY p.purchase_date DESC, p.id DESC
        """
        
        purchases = self.db.fetch_all(purchases_query, (start_date, end_date))
        for purchase in purchases:
            formatted_purchase = (purchase[0], purchase[1], purchase[2], f"${purchase[3]:.2f}", 
                                f"${purchase[4]:.2f}", purchase[5], purchase[6])
            self.purchases_tree.insert("", tk.END, values=formatted_purchase)