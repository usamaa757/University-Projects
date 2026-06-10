import tkinter as tk
from tkinter import ttk, messagebox
from database import Database
from styles import Styles

class AdminPanel:
    def __init__(self, parent, username):
        self.parent = parent
        self.username = username
        self.db = Database()
        self.styles = Styles()
        self.setup_ui()
    
    def setup_ui(self):
        # Notebook for tabs
        self.notebook = ttk.Notebook(self.parent)
        self.notebook.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        # Create tabs
        self.user_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        self.item_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        self.view_tab = tk.Frame(self.notebook, bg=self.styles.LIGHT)
        
        self.notebook.add(self.user_tab, text="User Management")
        self.notebook.add(self.item_tab, text="Item Management")
        self.notebook.add(self.view_tab, text="View Items")
        
        self.setup_user_tab()
        self.setup_item_tab()
        self.setup_update_form()
        self.setup_view_tab()
        
    
    # ===================== USER TAB =====================
    def setup_user_tab(self):
        add_frame = tk.LabelFrame(self.user_tab, text="Add New User", 
                                  font=self.styles.HEADING_FONT,
                                  bg=self.styles.LIGHT,
                                  padx=10, pady=10)
        add_frame.pack(fill=tk.X, padx=10, pady=10)
        
        tk.Label(add_frame, text="Username:", bg=self.styles.LIGHT).grid(row=0, column=0, sticky=tk.W, pady=5)
        self.new_username = tk.Entry(add_frame, width=25, **self.styles.ENTRY_STYLE)
        self.new_username.grid(row=0, column=1, padx=5, pady=5)
        
        tk.Label(add_frame, text="Password:", bg=self.styles.LIGHT).grid(row=1, column=0, sticky=tk.W, pady=5)
        self.new_password = tk.Entry(add_frame, width=25, show="*", **self.styles.ENTRY_STYLE)
        self.new_password.grid(row=1, column=1, padx=5, pady=5)
        
        tk.Label(add_frame, text="User Type:", bg=self.styles.LIGHT).grid(row=2, column=0, sticky=tk.W, pady=5)
        self.user_type = ttk.Combobox(add_frame, values=["admin", "user"], width=23, state="readonly")
        self.user_type.grid(row=2, column=1, padx=5, pady=5)
        self.user_type.set("user")
        
        tk.Button(add_frame, text="Add User", command=self.add_user,
                  **self.styles.BTN_PRIMARY).grid(row=3, column=0, columnspan=2, pady=10)
        
        # Users List
        list_frame = tk.LabelFrame(self.user_tab, text="Existing Users",
                                   font=self.styles.HEADING_FONT,
                                   bg=self.styles.LIGHT,
                                   padx=10, pady=10)
        list_frame.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)
        
        columns = ("ID", "Username", "User Type", "Created At")
        self.user_tree = ttk.Treeview(list_frame, columns=columns, show="headings", height=10)
        for col in columns:
            self.user_tree.heading(col, text=col)
            self.user_tree.column(col, width=100)
        
        scrollbar = ttk.Scrollbar(list_frame, orient=tk.VERTICAL, command=self.user_tree.yview)
        self.user_tree.configure(yscrollcommand=scrollbar.set)
        
        self.user_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y)
        
        self.user_tree.bind("<Double-1>", self.on_user_select)

        
        self.load_users()
   # ===================== USER ACTIONS =====================
    def add_user(self):
        username = self.new_username.get().strip()
        password = self.new_password.get().strip()
        user_type = self.user_type.get()
        
        if not username or not password:
            messagebox.showwarning("Warning", "Please fill all fields")
            return
        
        if self.db.execute_query(
            "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)",
            (username, password, user_type)
        ):
            messagebox.showinfo("Success", "User added successfully!")
            self.new_username.delete(0, tk.END)
            self.new_password.delete(0, tk.END)
            self.load_users()
        else:
            messagebox.showerror("Error", "Failed to add user")

    def on_user_select(self, event):
        selected = self.user_tree.selection()
        if not selected:
            return
        
        values = self.user_tree.item(selected[0])['values']
        user_id = values[0]
        username = values[1]
        user_type = values[2]

        # Create popup window
        popup = tk.Toplevel(self.parent)
        popup.title(f"Update/Delete User - {username}")
        popup.geometry("400x300")  # increased width and height
        popup.resizable(False, False)

        # Username
        tk.Label(popup, text="Username:").pack(pady=5)
        username_entry = tk.Entry(popup, width=30)
        username_entry.pack()
        username_entry.insert(0, username)

        # Password (optional)
        tk.Label(popup, text="Password (optional):").pack(pady=5)
        password_entry = tk.Entry(popup, width=30, show="*")
        password_entry.pack()

        # User Type
        tk.Label(popup, text="User Type:").pack(pady=5)
        user_type_combo = ttk.Combobox(popup, values=["admin", "user"], state="readonly", width=28)
        user_type_combo.pack()
        user_type_combo.set(user_type)

        def update_user_popup():
            new_username = username_entry.get().strip()
            new_user_type = user_type_combo.get()
            new_password = password_entry.get().strip()

            if not new_username:
                messagebox.showwarning("Warning", "Username cannot be empty")
                return

            if new_password:  # update password only if entered
                query = "UPDATE users SET username=?, user_type=?, password=? WHERE id=?"
                params = (new_username, new_user_type, new_password, user_id)
            else:
                query = "UPDATE users SET username=?, user_type=? WHERE id=?"
                params = (new_username, new_user_type, user_id)

            if self.db.execute_query(query, params):
                messagebox.showinfo("Success", "User updated successfully!")
                self.load_users()
                popup.destroy()
            else:
                messagebox.showerror("Error", "Failed to update user")

        def delete_user_popup():
            if username == self.username:
                messagebox.showwarning("Warning", "Cannot delete your own account!")
                return
            if messagebox.askyesno("Confirm", f"Delete user '{username}'?"):
                if self.db.execute_query("DELETE FROM users WHERE id=?", (user_id,)):
                    messagebox.showinfo("Success", "User deleted successfully!")
                    self.load_users()
                    popup.destroy()
                else:
                    messagebox.showerror("Error", "Failed to delete user")

        # Buttons
        btn_frame = tk.Frame(popup)
        btn_frame.pack(pady=15)

        tk.Button(btn_frame, text="Update", command=update_user_popup,
                bg="#4CAF50", fg="white", width=12).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Delete", command=delete_user_popup,
                bg="#f44336", fg="white", width=12).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Cancel", command=popup.destroy,
                bg="#2196F3", fg="white", width=12).pack(side=tk.LEFT, padx=5)


        def delete_user_popup():
            if username == self.username:
                messagebox.showwarning("Warning", "Cannot delete your own account!")
                return
            if messagebox.askyesno("Confirm", f"Delete user '{username}'?"):
                if self.db.execute_query("DELETE FROM users WHERE id=?", (user_id,)):
                    messagebox.showinfo("Success", "User deleted successfully!")
                    self.load_users()
                    popup.destroy()
                else:
                    messagebox.showerror("Error", "Failed to delete user")

    # ===================== ITEM TAB =====================
    def setup_item_tab(self):
        add_frame = tk.LabelFrame(self.item_tab, text="Add Clothing Item",
                                  font=self.styles.HEADING_FONT,
                                  bg=self.styles.LIGHT,
                                  padx=10, pady=10)
        add_frame.pack(fill=tk.X, padx=10, pady=10)
        
        fields = [
            ("Name:", "item_name"),
            ("Description:", "item_desc"),
            ("Target Group:", "target_group"),
            ("Season:", "season"),
            ("Purchase Price:", "purchase_price"),
            ("Sale Price:", "sale_price"),
            ("Initial Stock:", "stock")
        ]
        
        self.add_entries = {}
        
        for i, (label, key) in enumerate(fields):
            tk.Label(add_frame, text=label, bg=self.styles.LIGHT).grid(row=i, column=0, sticky=tk.W, pady=5)
            
            if label == "Target Group:":
                entry = ttk.Combobox(add_frame, values=["Men", "Women", "Kids"], width=27, state="readonly")
            elif label == "Season:":
                entry = ttk.Combobox(add_frame, values=["Summer", "Winter", "All"], width=27, state="readonly")
            else:
                entry = tk.Entry(add_frame, width=30, **self.styles.ENTRY_STYLE)
            
            entry.grid(row=i, column=1, padx=5, pady=5)
            self.add_entries[key] = entry
        
        btn_frame = tk.Frame(add_frame, bg=self.styles.LIGHT)
        btn_frame.grid(row=len(fields), column=0, columnspan=2, pady=10)
        
        tk.Button(btn_frame, text="Add Item", command=self.add_item,
                  **self.styles.BTN_PRIMARY).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Clear", command=self.clear_add_form,
                  **self.styles.BTN_SECONDARY).pack(side=tk.LEFT, padx=5)
    
    # ===================== UPDATE/DELETE FORM =====================
    def setup_update_form(self):
        self.update_frame = tk.LabelFrame(self.item_tab, text="Update/Delete Item",
                                          font=self.styles.HEADING_FONT,
                                          bg=self.styles.LIGHT,
                                          padx=10, pady=10)
        self.update_frame.pack(fill=tk.X, padx=10, pady=10)
        self.update_frame.pack_forget()  # hide initially
        
        fields = [
            ("Name:", "item_name"),
            ("Description:", "item_desc"),
            ("Target Group:", "target_group"),
            ("Season:", "season"),
            ("Purchase Price:", "purchase_price"),
            ("Sale Price:", "sale_price"),
            ("Stock:", "stock")
        ]
        
        self.update_entries = {}
        
        for i, (label, key) in enumerate(fields):
            tk.Label(self.update_frame, text=label, bg=self.styles.LIGHT).grid(row=i, column=0, sticky=tk.W, pady=5)
            
            if label == "Target Group:":
                entry = ttk.Combobox(self.update_frame, values=["Men", "Women", "Kids"], width=27, state="readonly")
            elif label == "Season:":
                entry = ttk.Combobox(self.update_frame, values=["Summer", "Winter", "All"], width=27, state="readonly")
            else:
                entry = tk.Entry(self.update_frame, width=30, **self.styles.ENTRY_STYLE)
            
            entry.grid(row=i, column=1, padx=5, pady=5)
            self.update_entries[key] = entry
        
        btn_frame = tk.Frame(self.update_frame, bg=self.styles.LIGHT)
        btn_frame.grid(row=len(fields), column=0, columnspan=2, pady=10)
        
        tk.Button(btn_frame, text="Update Item", command=self.update_item,
                  **self.styles.BTN_PRIMARY).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Delete Item", command=self.delete_item,
                  **self.styles.BTN_DANGER).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Cancel", command=self.hide_update_form,
                  **self.styles.BTN_SECONDARY).pack(side=tk.LEFT, padx=5)
    
    # ===================== VIEW ITEMS TAB =====================
   
    
    def setup_view_tab(self):
        # ===== SEARCH FRAME =====
        search_frame = tk.Frame(self.view_tab, bg=self.styles.LIGHT)
        search_frame.pack(fill=tk.X, padx=10, pady=5)

        # Search by Name
        tk.Label(search_frame, text="Name:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.search_name_entry = tk.Entry(search_frame, width=20, **self.styles.ENTRY_STYLE)
        self.search_name_entry.pack(side=tk.LEFT, padx=5)

        # Filter by Target Group
        tk.Label(search_frame, text="Target Group:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.search_target_group = ttk.Combobox(search_frame, values=["", "Men", "Women", "Kids"], width=10, state="readonly")
        self.search_target_group.pack(side=tk.LEFT, padx=5)
        self.search_target_group.set("")

        # Filter by Season
        tk.Label(search_frame, text="Season:", bg=self.styles.LIGHT).pack(side=tk.LEFT, padx=5)
        self.search_season = ttk.Combobox(search_frame, values=["", "Summer", "Winter", "All"], width=10, state="readonly")
        self.search_season.pack(side=tk.LEFT, padx=5)
        self.search_season.set("")

        # Buttons
        tk.Button(search_frame, text="Search", command=self.search_item, **self.styles.BTN_PRIMARY).pack(side=tk.LEFT, padx=5)
        tk.Button(search_frame, text="Reset", command=self.reset_search, **self.styles.BTN_SECONDARY).pack(side=tk.LEFT, padx=5)

        # Bind Enter key to search
        self.search_name_entry.bind("<Return>", lambda event: self.search_item())

        # ===== TREEVIEW =====
        columns = ("ID", "Name", "Target Group", "Season", "Purchase Price", 
                "Sale Price", "Stock", "Description")
        self.item_tree = ttk.Treeview(self.view_tab, columns=columns, show="headings", height=15)

        col_widths = [50, 150, 80, 80, 100, 80, 60, 200]
        for col, width in zip(columns, col_widths):
            self.item_tree.heading(col, text=col)
            self.item_tree.column(col, width=width)

        scrollbar = ttk.Scrollbar(self.view_tab, orient=tk.VERTICAL, command=self.item_tree.yview)
        self.item_tree.configure(yscrollcommand=scrollbar.set)

        self.item_tree.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=10, pady=10)
        scrollbar.pack(side=tk.RIGHT, fill=tk.Y, pady=10)

        self.item_tree.bind("<Double-1>", self.on_item_select)

        self.load_items()

    def search_item(self, reset=False):
        if reset:
            # Reset all search fields
            self.search_name_entry.delete(0, tk.END)
            self.search_target_group.set("")
            self.search_season.set("")
        
        # Get search parameters
        name = self.search_name_entry.get().strip()
        target_group = self.search_target_group.get().strip()
        season = self.search_season.get().strip()
        
        # Clear current Treeview items
        for item in self.item_tree.get_children():
            self.item_tree.delete(item)
        
        # Build query based on search parameters
        query = """
            SELECT id, name, target_group, season, purchase_price, sale_price, stock, description
            FROM clothing_items
            WHERE 1=1
        """
        params = []
        
        if name:
            query += " AND name LIKE ?"
            params.append(f"%{name}%")
        if target_group:
            query += " AND target_group = ?"
            params.append(target_group)
        if season:
            query += " AND season = ?"
            params.append(season)
        
        query += " ORDER BY id DESC"
        
        items = self.db.fetch_all(query, tuple(params))
        
        for item in items:
            self.item_tree.insert("", tk.END, values=item)


    def reset_search(self):
        # Clear search fields and reload all items
        self.search_name_entry.delete(0, tk.END)
        self.search_target_group.set("")
        self.search_season.set("")
        
        # Reload all items
        self.load_items()


    def add_item(self):
        try:
            data = {
                'name': self.add_entries['item_name'].get().strip(),
                'description': self.add_entries['item_desc'].get().strip(),
                'target_group': self.add_entries['target_group'].get(),
                'season': self.add_entries['season'].get(),
                'purchase_price': float(self.add_entries['purchase_price'].get()),
                'sale_price': float(self.add_entries['sale_price'].get()),
                'stock': int(self.add_entries['stock'].get())
            }
        except ValueError:
            messagebox.showerror("Error", "Please enter valid numeric values for prices and stock")
            return
        
        if not data['name']:
            messagebox.showwarning("Warning", "Item name is required")
            return
        
        query = """
            INSERT INTO clothing_items 
            (name, description, target_group, season, purchase_price, sale_price, stock)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        """
        params = tuple(data.values())
        
        if self.db.execute_query(query, params):
            messagebox.showinfo("Success", "Item added successfully!")
            self.clear_add_form()
            self.load_items()
        else:
            messagebox.showerror("Error", "Failed to add item")
    
    
    def on_item_select(self, event):
        selected = self.item_tree.selection()
        if not selected:
            return
        
        values = self.item_tree.item(selected[0])['values']
        
        # Create a new window
        self.update_window = tk.Toplevel(self.parent)
        self.update_window.title(f"Update/Delete Item: {values[1]}")
        self.update_window.geometry("400x350")
        self.update_window.resizable(False, False)
        
        fields = [
            ("Name:", "item_name"),
            ("Description:", "item_desc"),
            ("Target Group:", "target_group"),
            ("Season:", "season"),
            ("Purchase Price:", "purchase_price"),
            ("Sale Price:", "sale_price"),
            ("Stock:", "stock")
        ]
        
        self.update_entries = {}
        
        for i, (label, key) in enumerate(fields):
            tk.Label(self.update_window, text=label).grid(row=i, column=0, sticky=tk.W, padx=10, pady=5)
            
            if label == "Target Group:":
                entry = ttk.Combobox(self.update_window, values=["Men", "Women", "Kids"], width=25, state="readonly")
            elif label == "Season:":
                entry = ttk.Combobox(self.update_window, values=["Summer", "Winter", "All"], width=25, state="readonly")
            else:
                entry = tk.Entry(self.update_window, width=30)
            
            entry.grid(row=i, column=1, padx=10, pady=5)
            self.update_entries[key] = entry
        
        # Fill the fields with selected item values
        self.update_entries['item_name'].insert(0, values[1])
        self.update_entries['item_desc'].insert(0, values[7] if len(values) > 7 else "")
        self.update_entries['target_group'].set(values[2])
        self.update_entries['season'].set(values[3])
        self.update_entries['purchase_price'].insert(0, values[4])
        self.update_entries['sale_price'].insert(0, values[5])
        self.update_entries['stock'].insert(0, values[6])
        
        # Buttons at the bottom
        btn_frame = tk.Frame(self.update_window)
        btn_frame.grid(row=len(fields), column=0, columnspan=2, pady=15)
        
        tk.Button(btn_frame, text="Update Item", command=lambda: self.update_item(values[0]),
                bg="#4CAF50", fg="white", width=12).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Delete Item", command=lambda: self.delete_item(values[0]),
                bg="#f44336", fg="white", width=12).pack(side=tk.LEFT, padx=5)
        tk.Button(btn_frame, text="Cancel", command=self.update_window.destroy,
                bg="#2196F3", fg="white", width=12).pack(side=tk.LEFT, padx=5)

            
    def update_item(self, item_id):
        try:
            data = {
                'name': self.update_entries['item_name'].get().strip(),
                'description': self.update_entries['item_desc'].get().strip(),
                'target_group': self.update_entries['target_group'].get(),
                'season': self.update_entries['season'].get(),
                'purchase_price': float(self.update_entries['purchase_price'].get()),
                'sale_price': float(self.update_entries['sale_price'].get()),
                'stock': int(self.update_entries['stock'].get()),
                'id': item_id
            }
        except ValueError:
            messagebox.showerror("Error", "Please enter valid numeric values")
            return
        
        query = """
            UPDATE clothing_items 
            SET name=?, description=?, target_group=?, season=?, 
                purchase_price=?, sale_price=?, stock=?, updated_at=CURRENT_TIMESTAMP
            WHERE id=?
        """
        
        params = (data['name'], data['description'], data['target_group'], data['season'],
                data['purchase_price'], data['sale_price'], data['stock'], data['id'])
        
        if self.db.execute_query(query, params):
            messagebox.showinfo("Success", "Item updated successfully!")
            self.update_window.destroy()
            self.load_items()
        else:
            messagebox.showerror("Error", "Failed to update item")


    def delete_item(self, item_id):
        item_name = self.item_tree.item(self.item_tree.selection()[0])['values'][1]
        
        if messagebox.askyesno("Confirm", f"Delete item '{item_name}'?"):
            if self.db.execute_query("DELETE FROM clothing_items WHERE id = ?", (item_id,)):
                messagebox.showinfo("Success", "Item deleted successfully!")
                self.update_window.destroy()
                self.load_items()
            else:
                messagebox.showerror("Error", "Failed to delete item")



    def hide_update_form(self):
        self.update_frame.pack_forget()

    
    def clear_add_form(self):
        for entry in self.add_entries.values():
            if isinstance(entry, tk.Entry):
                entry.delete(0, tk.END)
            elif isinstance(entry, ttk.Combobox):
                entry.set("")

    
    def clear_update_form(self):
        for entry in self.update_entries.values():
            if isinstance(entry, tk.Entry):
                entry.delete(0, tk.END)
            elif isinstance(entry, ttk.Combobox):
                entry.set("")
    
    # ===================== LOAD DATA =====================
    def load_users(self):
        for item in self.user_tree.get_children():
            self.user_tree.delete(item)
        
        users = self.db.fetch_all("SELECT id, username, user_type, created_at FROM users")
        for user in users:
            self.user_tree.insert("", tk.END, values=user)
    
    def load_items(self):
        for item in self.item_tree.get_children():
            self.item_tree.delete(item)
        
        items = self.db.fetch_all("""
            SELECT id, name, target_group, season, purchase_price, sale_price, stock, description
            FROM clothing_items
            ORDER BY id DESC
        """)
        for item in items:
            self.item_tree.insert("", tk.END, values=item)
    
    