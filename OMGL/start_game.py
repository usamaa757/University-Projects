import tkinter as tk
from tkinter import ttk

def start_game():
    """Placeholder function to start the game."""
    print("Starting the game...")
    # Add your game logic here

# Create the main window
root = tk.Tk()
root.title("Online Maze Game for Learning")

# Configure the window background color
root.configure(bg="#f0f0f0")  # Light gray background

# Create a frame for the content
content_frame = ttk.Frame(root, padding="20", style="Content.TFrame")
content_frame.pack(expand=True, fill=tk.BOTH, padx=50, pady=50)

# Configure the frame style
style = ttk.Style()
style.configure("Content.TFrame", background="white", relief="solid", borderwidth=1)

# Title label
title_label = ttk.Label(content_frame, text="Online Maze Game for Learning", font=("Helvetica", 24, "bold"), background="white")
title_label.pack(pady=(20, 10))

# Welcome label
welcome_label = ttk.Label(content_frame, text="Welcome to OMGL", font=("Helvetica", 18, "bold"), background="white")
welcome_label.pack(pady=(10, 5))

# Description label
description_label = ttk.Label(content_frame, 
                              text="Navigate through the maze, solve programming challenges to earn keys, and unlock gates to reach the end!", 
                              font=("Helvetica", 12), background="white", wraplength=500)
description_label.pack(pady=(5, 20))

# Start game button
start_button = ttk.Button(content_frame, text="Start Game", command=start_game, padding=(20, 10))
start_button.pack(pady=(20, 20))

# Configure the button style (optional)
style.configure("TButton", font=("Helvetica", 12))

# Center the window on the screen
root.eval('tk::PlaceWindow %s center' % root.winfo_pathname(root.winfo_id()))

# Start the GUI event loop
root.mainloop()