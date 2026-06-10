import tkinter as tk
from gui.login_window import LoginWindow
from gui.main_dashboard import MainDashboard

class GymManagementSystem:
    def __init__(self):
        self.root = tk.Tk()
        self.show_login()

    def show_login(self):
        # Clear screen
        for widget in self.root.winfo_children():
            widget.destroy()

        LoginWindow(self.root, self.on_login_success)

    def on_login_success(self, user_data):
        # Clear login UI
        for widget in self.root.winfo_children():
            widget.destroy()

        MainDashboard(
            self.root,
            user_data=user_data,
            on_logout=self.show_login 
        )

    def run(self):
        self.root.mainloop()


if __name__ == "__main__":
    app = GymManagementSystem()
    app.run()
