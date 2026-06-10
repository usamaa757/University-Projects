# ---------- FULL BRANCH & ZONE MANAGEMENT ----------
import tkinter as tk
from tkinter import ttk, messagebox

class BranchZoneManagement:
    def __init__(self, parent, db):
        self.parent = parent
        self.db = db
        self.selected_branch_id = None
        self.selected_zone_id = None
        self.build_ui()

    def build_ui(self):
        container = tk.Frame(self.parent, bg='#ecf0f1')
        container.pack(fill='both', expand=True, padx=20, pady=20)

        tk.Label(container, text="Branch & Zone Management",
                 font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(anchor='w', pady=10)

        # ---------- BRANCHES ----------
        branch_frame = tk.LabelFrame(container, text="Branches", bg='white', padx=10, pady=10)
        branch_frame.pack(fill='x', pady=10)

        self.branch_tree = ttk.Treeview(
            branch_frame,
            columns=("id", "name", "city", "contact"),
            show="headings", height=6
        )
        for col in ("id", "name", "city", "contact"):
            self.branch_tree.heading(col, text=col.title())
            self.branch_tree.column(col, width=100)
        self.branch_tree.pack(fill='x', pady=5)
        self.branch_tree.bind("<<TreeviewSelect>>", self.on_branch_select)

        btn_frame = tk.Frame(branch_frame, bg='white')
        btn_frame.pack(pady=5)
        tk.Button(btn_frame, text="Add Branch", bg='#2ecc71', fg='white', width=12,
                  command=self.add_branch_popup).pack(side='left', padx=5)
        tk.Button(btn_frame, text="Edit Branch", bg='#f39c12', fg='white', width=12,
                  command=self.edit_branch_popup).pack(side='left', padx=5)
        tk.Button(btn_frame, text="Delete Branch", bg='#e74c3c', fg='white', width=12,
                  command=self.delete_branch).pack(side='left', padx=5)

        # ---------- ZONES ----------
        zone_frame = tk.LabelFrame(container, text="Workout Zones", bg='white', padx=10, pady=10)
        zone_frame.pack(fill='both', expand=True, pady=10)

        self.zone_tree = ttk.Treeview(
            zone_frame,
            columns=("id", "name", "type", "status"),
            show="headings", height=6
        )
        for col in ("id", "name", "type", "status"):
            self.zone_tree.heading(col, text=col.title())
            self.zone_tree.column(col, width=100)
        self.zone_tree.pack(fill='both', expand=True, pady=5)
        self.zone_tree.bind("<<TreeviewSelect>>", self.on_zone_select)

        zone_btn_frame = tk.Frame(zone_frame, bg='white')
        zone_btn_frame.pack(pady=5)
        tk.Button(zone_btn_frame, text="Add Zone", bg='#2ecc71', fg='white', width=12,
                  command=self.add_zone_popup).pack(side='left', padx=5)
        tk.Button(zone_btn_frame, text="Edit Zone", bg='#f39c12', fg='white', width=12,
                  command=self.edit_zone_popup).pack(side='left', padx=5)
        tk.Button(zone_btn_frame, text="Delete Zone", bg='#e74c3c', fg='white', width=12,
                  command=self.delete_zone).pack(side='left', padx=5)

        self.load_branches()

    # ---------- DATA ----------
    def load_branches(self):
        self.branch_tree.delete(*self.branch_tree.get_children())
        for b in self.db.get_branches():
            self.branch_tree.insert("", "end", values=b)
        self.selected_branch_id = None
        self.load_zones()

    def on_branch_select(self, event):
        selected = self.branch_tree.selection()
        if not selected:
            self.selected_branch_id = None
            return
        values = self.branch_tree.item(selected[0], "values")
        self.selected_branch_id = values[0]
        self.load_zones()

    def load_zones(self):
        self.zone_tree.delete(*self.zone_tree.get_children())
        if not self.selected_branch_id:
            return
        for z in self.db.get_zones_by_branch(self.selected_branch_id):
            self.zone_tree.insert("", "end", values=z)
        self.selected_zone_id = None

    def on_zone_select(self, event):
        selected = self.zone_tree.selection()
        if not selected:
            self.selected_zone_id = None
            return
        values = self.zone_tree.item(selected[0], "values")
        self.selected_zone_id = values[0]

    # ---------- BRANCH CRUD ----------
    def add_branch_popup(self):
        self.branch_popup("Add Branch")

    def edit_branch_popup(self):
        if not self.selected_branch_id:
            messagebox.showinfo("Select Branch", "Please select a branch first")
            return
        self.branch_popup("Edit Branch", self.selected_branch_id)

    def delete_branch(self):
        if not self.selected_branch_id:
            messagebox.showinfo("Select Branch", "Please select a branch first")
            return
        if messagebox.askyesno("Delete Branch", "Are you sure? This will delete all associated zones!"):
            self.db.delete_branch(self.selected_branch_id)
            self.load_branches()
            messagebox.showinfo("Deleted", "Branch deleted successfully!")

    def branch_popup(self, title, branch_id=None):
        win = tk.Toplevel(self.parent)
        win.title(title)
        win.geometry("450x300")
        win.configure(bg="#ecf0f1")

        tk.Label(win, text=title, font=('Arial', 16, 'bold'), bg="#ecf0f1").pack(pady=10)

        form_frame = tk.Frame(win, bg="#ecf0f1", padx=20, pady=10)
        form_frame.pack(fill='both', expand=True)

        fields = {}
        labels = ("Name", "City", "Contact", "Email")
        values = self.db.get_branch_by_id(branch_id) if branch_id else ["" for _ in labels]

        for i, lbl in enumerate(labels):
            tk.Label(form_frame, text=lbl+":", font=('Arial', 11, 'bold'), bg="#ecf0f1").grid(row=i, column=0, sticky='w', pady=5)
            e = ttk.Entry(form_frame, width=30)
            e.grid(row=i, column=1, pady=5, padx=5)
            e.insert(0, values[i+1] if branch_id else "")
            fields[lbl] = e

        tk.Button(win, text="Save", bg='#2ecc71', fg='white', width=12, font=('Arial', 12, 'bold'),
                  command=lambda: self.save_branch(fields, win, branch_id)).pack(pady=15)

    def save_branch(self, fields, win, branch_id=None):
        name = fields["Name"].get()
        city = fields["City"].get()
        contact = fields["Contact"].get()
        email = fields["Email"].get()

        if branch_id:
            self.db.update_branch(branch_id, name, city, contact, email)
            messagebox.showinfo("Updated", "Branch updated successfully!")
        else:
            self.db.add_branch(name, city, contact, email)
            messagebox.showinfo("Added", "Branch added successfully!")

        self.load_branches()
        win.destroy()

    # ---------- ZONE CRUD ----------
    def add_zone_popup(self):
        self.zone_popup("Add Zone")

    def edit_zone_popup(self):
        if not self.selected_zone_id:
            messagebox.showinfo("Select Zone", "Please select a zone first")
            return
        self.zone_popup("Edit Zone", self.selected_zone_id)

    def delete_zone(self):
        if not self.selected_zone_id:
            messagebox.showinfo("Select Zone", "Please select a zone first")
            return
        if messagebox.askyesno("Delete Zone", "Are you sure?"):
            self.db.delete_zone(self.selected_zone_id)
            self.load_zones()
            messagebox.showinfo("Deleted", "Zone deleted successfully!")

    def zone_popup(self, title, zone_id=None):
        if not self.selected_branch_id:
            messagebox.showinfo("Select Branch", "Select a branch first")
            return

        win = tk.Toplevel(self.parent)
        win.title(title)
        win.geometry("400x250")
        win.configure(bg="#ecf0f1")

        tk.Label(win, text=title, font=('Arial', 16, 'bold'), bg="#ecf0f1").pack(pady=10)

        form_frame = tk.Frame(win, bg="#ecf0f1", padx=20, pady=10)
        form_frame.pack(fill='both', expand=True)

        tk.Label(form_frame, text="Zone Name:", font=('Arial', 11, 'bold'), bg="#ecf0f1").grid(row=0, column=0, sticky='w', pady=5)
        name_entry = ttk.Entry(form_frame, width=30)
        name_entry.grid(row=0, column=1, pady=5, padx=5)

        tk.Label(form_frame, text="Zone Type:", font=('Arial', 11, 'bold'), bg="#ecf0f1").grid(row=1, column=0, sticky='w', pady=5)
        ztype_combo = ttk.Combobox(form_frame, values=["Cardio", "Strength", "Yoga"], width=28, state='readonly')
        ztype_combo.grid(row=1, column=1, pady=5, padx=5)
        ztype_combo.set("Cardio")

        # If editing, load existing values
        if zone_id:
            zone = self.db.get_zone_by_id(zone_id)
            name_entry.insert(0, zone[1])
            ztype_combo.set(zone[2])

        tk.Button(win, text="Save", bg='#2ecc71', fg='white', width=12, font=('Arial', 12, 'bold'),
                  command=lambda: self.save_zone(name_entry, ztype_combo, win, zone_id)).pack(pady=15)

    def save_zone(self, name_entry, ztype_combo, win, zone_id=None):
        name = name_entry.get()
        ztype = ztype_combo.get()
        if zone_id:
            self.db.update_zone(zone_id, name, ztype)
            messagebox.showinfo("Updated", "Zone updated successfully!")
        else:
            self.db.add_zone(self.selected_branch_id, name, ztype)
            messagebox.showinfo("Added", "Zone added successfully!")

        self.load_zones()
        win.destroy()
