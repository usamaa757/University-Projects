class Styles:
    # Colors
    PRIMARY = "#2C3E50"
    SECONDARY = "#34495E"
    ACCENT = "#1ABC9C"
    SUCCESS = "#27AE60"
    WARNING = "#F39C12"
    DANGER = "#E74C3C"
    LIGHT = "#ECF0F1"
    DARK = "#2C3E50"
    
    # Fonts
    TITLE_FONT = ("Helvetica", 20, "bold")
    HEADING_FONT = ("Helvetica", 14, "bold")
    NORMAL_FONT = ("Helvetica", 11)
    SMALL_FONT = ("Helvetica", 10)
    
    # Button styles
    BTN_PRIMARY = {
        "bg": ACCENT,
        "fg": "white",
        "font": NORMAL_FONT,
        "bd": 0,
        "padx": 20,
        "pady": 8,
        "activebackground": "#16A085",
        "cursor": "hand2"
    }
    
    BTN_SECONDARY = {
        "bg": SECONDARY,
        "fg": "white",
        "font": NORMAL_FONT,
        "bd": 0,
        "padx": 15,
        "pady": 6,
        "activebackground": PRIMARY,
        "cursor": "hand2"
    }
    
    BTN_DANGER = {
        "bg": DANGER,
        "fg": "white",
        "font": NORMAL_FONT,
        "bd": 0,
        "padx": 15,
        "pady": 6,
        "activebackground": "#C0392B",
        "cursor": "hand2"
    }
    
    # Entry styles
    ENTRY_STYLE = {
        "font": NORMAL_FONT,
        "bd": 2,
        "relief": "flat",
        "highlightthickness": 1,
        "highlightcolor": ACCENT,
        "highlightbackground": "#BDC3C7"
    }
    
    # Label styles
    LABEL_HEADING = {
        "font": HEADING_FONT,
        "fg": PRIMARY,
        "bg": LIGHT
    }
    
    LABEL_NORMAL = {
        "font": NORMAL_FONT,
        "fg": DARK,
        "bg": LIGHT
    }
    
    # Frame styles
    FRAME_STYLE = {
        "bg": LIGHT,
        "bd": 1,
        "relief": "solid"
    }
    
    CARD_STYLE = {
        "bg": "white",
        "bd": 1,
        "relief": "solid",
        "padx": 10,
        "pady": 10
    }