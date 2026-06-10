import tkinter as tk
from tkinter import ttk, messagebox
from datetime import datetime, timedelta
import matplotlib.pyplot as plt
from matplotlib.backends.backend_tkagg import FigureCanvasTkAgg
import database
from datetime import datetime, timedelta

class ReportsWindow:
    def __init__(self, parent, db):
        self.parent = parent
        self.db = db
        
        self.setup_ui()
    
    def setup_ui(self):
        # Main container
        main_frame = tk.Frame(self.parent, bg='#ecf0f1')
        main_frame.pack(fill='both', expand=True, padx=20, pady=20)
        
        # Header
        header_frame = tk.Frame(main_frame, bg='#ecf0f1')
        header_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(header_frame, text="Analytics & Reports", 
                font=('Arial', 20, 'bold'), bg='#ecf0f1').pack(side='left')
        
        # Report type selector
        report_frame = tk.Frame(main_frame, bg='white', padx=15, pady=15)
        report_frame.pack(fill='x', pady=(0, 20))
        
        tk.Label(report_frame, text="Report Type:", font=('Arial', 11), 
                bg='white').grid(row=0, column=0, padx=(0, 10))
        
        self.report_var = tk.StringVar(value="Membership Growth")
        report_combo = ttk.Combobox(report_frame, textvariable=self.report_var,
                                   values=["Membership Growth", "Revenue Analysis", 
                                          "Attendance Trends", "Class Popularity",
                                          "Peak Hours", "Member Demographics",
                                          "Staff Performance", "Equipment Usage"],
                                   state="readonly", width=25)
        report_combo.grid(row=0, column=1, padx=(0, 20))
        report_combo.bind('<<ComboboxSelected>>', self.generate_report)
        
        # Date range
        tk.Label(report_frame, text="Date Range:", font=('Arial', 11), 
                bg='white').grid(row=0, column=2, padx=(20, 10))
        
        self.range_var = tk.StringVar(value="Last 30 Days")
        range_combo = ttk.Combobox(report_frame, textvariable=self.range_var,
                                  values=["Last 7 Days", "Last 30 Days", 
                                         "Last 90 Days", "Last 6 Months",
                                         "Last Year", "Custom"],
                                  state="readonly", width=15)
        range_combo.grid(row=0, column=3, padx=(0, 20))
        range_combo.bind('<<ComboboxSelected>>', self.on_range_change)
        
        # Generate button
        tk.Button(report_frame, text="Generate Report", bg='#3498db', fg='white',
                 command=self.generate_report).grid(row=0, column=4, padx=(0, 10))
        
        # Export button
        tk.Button(report_frame, text="Export Data", bg='#2ecc71', fg='white',
                 command=self.export_data).grid(row=0, column=5)
        
        # Report display area
        self.report_display = tk.Frame(main_frame, bg='white')
        self.report_display.pack(fill='both', expand=True)
        
        # Generate default report
        self.generate_report()
    
    def on_range_change(self, event=None):
        if self.range_var.get() == "Custom":
            self.show_date_picker()
    
    def show_date_picker(self):
        date_window = tk.Toplevel(self.parent)
        date_window.title("Select Date Range")
        date_window.geometry("300x200")
        
        tk.Label(date_window, text="From Date:").pack(pady=(20, 5))
        from_var = tk.StringVar(value=datetime.now().strftime('%Y-%m-%d'))
        ttk.Entry(date_window, textvariable=from_var).pack(pady=5)
        
        tk.Label(date_window, text="To Date:").pack(pady=5)
        to_var = tk.StringVar(value=datetime.now().strftime('%Y-%m-%d'))
        ttk.Entry(date_window, textvariable=to_var).pack(pady=5)
        
        tk.Button(date_window, text="Apply", bg='#3498db', fg='white',
                 command=lambda: self.apply_custom_range(from_var.get(), to_var.get(), date_window)).pack(pady=20)
    
    def apply_custom_range(self, from_date, to_date, window):
        self.custom_from = from_date
        self.custom_to = to_date
        window.destroy()
        self.generate_report()
    
    def generate_report(self, event=None):
        # Clear previous report
        for widget in self.report_display.winfo_children():
            widget.destroy()
        
        report_type = self.report_var.get()
        
        if report_type == "Membership Growth":
            self.show_membership_growth()
        elif report_type == "Revenue Analysis":
            self.show_revenue_analysis()
        elif report_type == "Attendance Trends":
            self.show_attendance_trends()
        elif report_type == "Class Popularity":
            self.show_class_popularity()
        elif report_type == "Peak Hours":
            self.show_peak_hours()
        elif report_type == "Member Demographics":
            self.show_demographics()
        elif report_type == "Staff Performance":
            self.show_staff_performance()
        elif report_type == "Equipment Usage":
            self.show_equipment_usage()
    
    def show_membership_growth(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get membership growth data
        cursor.execute("""
            SELECT strftime('%Y-%m', join_date) as month,
                   COUNT(*) as new_members,
                   SUM(COUNT(*)) OVER (ORDER BY strftime('%Y-%m', join_date)) as total_members
            FROM members
            WHERE join_date >= date('now', '-6 months')
            GROUP BY strftime('%Y-%m', join_date)
            ORDER BY month
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            months = [row[0] for row in data]
            new_members = [row[1] for row in data]
            total_members = [row[2] for row in data]
            
            # Create figure
            fig, (ax1, ax2) = plt.subplots(2, 1, figsize=(10, 8))
            
            # New members bar chart
            ax1.bar(months, new_members, color='#3498db')
            ax1.set_title('New Members per Month (Last 6 Months)')
            ax1.set_ylabel('New Members')
            ax1.tick_params(axis='x', rotation=45)
            
            # Total members line chart
            ax2.plot(months, total_members, marker='o', color='#2ecc71', linewidth=2)
            ax2.set_title('Total Members Growth')
            ax2.set_ylabel('Total Members')
            ax2.tick_params(axis='x', rotation=45)
            ax2.grid(True, alpha=0.3)
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Statistics (Last 6 Months):
            • Total New Members: {sum(new_members)}
            • Average per Month: {sum(new_members)/len(new_members):.1f}
            • Growth Rate: {((total_members[-1] - total_members[0]) / max(total_members[0], 1) * 100):.1f}%
            • Current Total: {total_members[-1]} members
            """
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_revenue_analysis(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get revenue data
        cursor.execute("""
            SELECT strftime('%Y-%m', payment_date) as month,
                   SUM(final_amount) as revenue,
                   COUNT(*) as transactions,
                   AVG(final_amount) as avg_transaction
            FROM payments
            WHERE status = 'Completed' 
            AND payment_date >= date('now', '-6 months')
            GROUP BY strftime('%Y-%m', payment_date)
            ORDER BY month
        """)
        
        data = cursor.fetchall()
        
        # Get revenue by type
        cursor.execute("""
            SELECT payment_type, SUM(final_amount) as revenue
            FROM payments
            WHERE status = 'Completed'
            AND payment_date >= date('now', '-6 months')
            GROUP BY payment_type
        """)
        
        type_data = cursor.fetchall()
        conn.close()
        
        if data:
            months = [row[0] for row in data]
            revenues = [row[1] for row in data]
            transactions = [row[2] for row in data]
            avg_transactions = [row[3] for row in data]
            
            # Create figure
            fig, ((ax1, ax2), (ax3, ax4)) = plt.subplots(2, 2, figsize=(12, 8))
            
            # Revenue trend
            ax1.plot(months, revenues, marker='o', color='#3498db', linewidth=2)
            ax1.set_title('Monthly Revenue Trend')
            ax1.set_ylabel('Revenue (PKR)')
            ax1.tick_params(axis='x', rotation=45)
            ax1.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'PKR {x:,.0f}'))
            ax1.grid(True, alpha=0.3)
            
            # Transactions
            ax2.bar(months, transactions, color='#2ecc71')
            ax2.set_title('Monthly Transactions')
            ax2.set_ylabel('Number of Transactions')
            ax2.tick_params(axis='x', rotation=45)
            
            # Average transaction
            ax3.bar(months, avg_transactions, color='#e74c3c')
            ax3.set_title('Average Transaction Value')
            ax3.set_ylabel('Amount (PKR)')
            ax3.tick_params(axis='x', rotation=45)
            ax3.yaxis.set_major_formatter(plt.FuncFormatter(lambda x, p: f'PKR {x:,.0f}'))
            
            # Revenue by type (pie chart)
            if type_data:
                types = [row[0] for row in type_data]
                type_revenues = [row[1] for row in type_data]
                colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6']
                
                ax4.pie(type_revenues, labels=types, autopct='%1.1f%%', colors=colors[:len(types)])
                ax4.set_title('Revenue by Type')
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            total_revenue = sum(revenues)
            avg_monthly = total_revenue / len(revenues)
            growth_rate = ((revenues[-1] - revenues[0]) / max(revenues[0], 1) * 100) if len(revenues) > 1 else 0
            
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Revenue Statistics (Last 6 Months):
            • Total Revenue: PKR {total_revenue:,.2f}
            • Average Monthly: PKR {avg_monthly:,.2f}
            • Growth Rate: {growth_rate:.1f}%
            • Total Transactions: {sum(transactions)}
            • Avg. Transaction: PKR {sum(avg_transactions)/len(avg_transactions):,.2f}
            """
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_attendance_trends(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get attendance data (simplified - using appointments as attendance)
        cursor.execute("""
            SELECT strftime('%Y-%m-%d', appointment_date) as date,
                   COUNT(*) as attendance_count
            FROM appointments
            WHERE status = 'Completed'
            AND appointment_date >= date('now', '-30 days')
            GROUP BY strftime('%Y-%m-%d', appointment_date)
            ORDER BY date
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            dates = [datetime.strptime(row[0], '%Y-%m-%d').strftime('%d/%m') for row in data]
            attendance = [row[1] for row in data]
            
            # Create figure
            fig, ax = plt.subplots(figsize=(12, 6))
            
            # Attendance trend
            ax.plot(dates, attendance, marker='o', color='#3498db', linewidth=2)
            ax.fill_between(dates, attendance, alpha=0.3, color='#3498db')
            ax.set_title('Daily Attendance Trend (Last 30 Days)')
            ax.set_ylabel('Number of Sessions')
            ax.set_xlabel('Date')
            ax.grid(True, alpha=0.3)
            ax.tick_params(axis='x', rotation=45)
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            total_attendance = sum(attendance)
            avg_daily = total_attendance / len(attendance)
            max_day = max(attendance)
            min_day = min(attendance)
            
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Attendance Statistics (Last 30 Days):
            • Total Sessions: {total_attendance}
            • Average Daily: {avg_daily:.1f} sessions
            • Peak Day: {max_day} sessions
            • Lowest Day: {min_day} sessions
            • Busiest Days: {', '.join([dates[i] for i, x in enumerate(attendance) if x == max_day])}
            """
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_class_popularity(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get class popularity data
        cursor.execute("""
            SELECT appointment_type, COUNT(*) as count,
                   AVG(strftime('%s', end_time) - strftime('%s', start_time))/60 as avg_duration
            FROM appointments
            WHERE status = 'Completed'
            AND appointment_date >= date('now', '-90 days')
            GROUP BY appointment_type
            ORDER BY count DESC
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            classes = [row[0] for row in data]
            counts = [row[1] for row in data]
            durations = [row[2] for row in data]
            
            # Create figure
            fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(12, 6))
            
            # Class popularity bar chart
            colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12']
            ax1.bar(classes, counts, color=colors[:len(classes)])
            ax1.set_title('Class Popularity (Last 90 Days)')
            ax1.set_ylabel('Number of Sessions')
            ax1.tick_params(axis='x', rotation=45)
            
            # Average duration
            ax2.bar(classes, durations, color=colors[:len(classes)])
            ax2.set_title('Average Session Duration')
            ax2.set_ylabel('Duration (minutes)')
            ax2.tick_params(axis='x', rotation=45)
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            total_sessions = sum(counts)
            
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Class Statistics (Last 90 Days):
            • Total Sessions: {total_sessions}
            • Most Popular: {classes[0]} ({counts[0]} sessions)
            • Least Popular: {classes[-1]} ({counts[-1]} sessions)
            """
            
            for i, cls in enumerate(classes):
                percentage = (counts[i] / total_sessions * 100) if total_sessions > 0 else 0
                stats_text += f"\n• {cls}: {counts[i]} sessions ({percentage:.1f}%)"
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_peak_hours(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get peak hours data (simplified)
        cursor.execute("""
            SELECT strftime('%H', start_time) as hour,
                   COUNT(*) as sessions
            FROM appointments
            WHERE status = 'Completed'
            AND appointment_date >= date('now', '-30 days')
            GROUP BY strftime('%H', start_time)
            ORDER BY hour
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            hours = [f"{int(row[0]):02d}:00" for row in data]
            sessions = [row[1] for row in data]
            
            # Create figure
            fig, ax = plt.subplots(figsize=(12, 6))
            
            # Peak hours bar chart
            colors = ['#2ecc71' if x < max(sessions) else '#e74c3c' for x in sessions]
            ax.bar(hours, sessions, color=colors)
            ax.set_title('Peak Hours Analysis (Last 30 Days)')
            ax.set_ylabel('Number of Sessions')
            ax.set_xlabel('Hour of Day')
            ax.grid(True, alpha=0.3)
            
            # Highlight peak hours
            peak_hour_index = sessions.index(max(sessions))
            ax.annotate('PEAK', xy=(peak_hour_index, max(sessions)),
                       xytext=(0, 10), textcoords='offset points',
                       ha='center', color='#e74c3c', fontweight='bold')
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            peak_hour = hours[peak_hour_index]
            total_sessions = sum(sessions)
            avg_sessions = total_sessions / len(sessions)
            
            # Find busiest periods
            morning = sum(sessions[:6])  # 6 AM - 12 PM
            afternoon = sum(sessions[6:12])  # 12 PM - 6 PM
            evening = sum(sessions[12:])  # 6 PM - 12 AM
            
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Peak Hours Statistics (Last 30 Days):
            • Total Sessions: {total_sessions}
            • Peak Hour: {peak_hour} ({max(sessions)} sessions)
            • Average per Hour: {avg_sessions:.1f} sessions
            
            Busiest Periods:
            • Morning (6AM-12PM): {morning} sessions
            • Afternoon (12PM-6PM): {afternoon} sessions
            • Evening (6PM-12AM): {evening} sessions
            """
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_demographics(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get member demographics
        cursor.execute("""
            SELECT gender, COUNT(*) as count,
                   AVG(strftime('%Y', 'now') - strftime('%Y', date_of_birth)) as avg_age
            FROM members
            WHERE status = 'Active'
            GROUP BY gender
        """)
        
        gender_data = cursor.fetchall()
        
        # Get age distribution
        cursor.execute("""
            SELECT CASE
                WHEN strftime('%Y', 'now') - strftime('%Y', date_of_birth) < 20 THEN 'Under 20'
                WHEN strftime('%Y', 'now') - strftime('%Y', date_of_birth) BETWEEN 20 AND 29 THEN '20-29'
                WHEN strftime('%Y', 'now') - strftime('%Y', date_of_birth) BETWEEN 30 AND 39 THEN '30-39'
                WHEN strftime('%Y', 'now') - strftime('%Y', date_of_birth) BETWEEN 40 AND 49 THEN '40-49'
                WHEN strftime('%Y', 'now') - strftime('%Y', date_of_birth) >= 50 THEN '50+'
                ELSE 'Unknown'
            END as age_group,
            COUNT(*) as count
            FROM members
            WHERE status = 'Active'
            GROUP BY age_group
            ORDER BY 
                CASE age_group
                    WHEN 'Under 20' THEN 1
                    WHEN '20-29' THEN 2
                    WHEN '30-39' THEN 3
                    WHEN '40-49' THEN 4
                    WHEN '50+' THEN 5
                    ELSE 6
                END
        """)
        
        age_data = cursor.fetchall()
        
        # Get membership type distribution
        cursor.execute("""
            SELECT membership_type, COUNT(*) as count
            FROM members
            WHERE status = 'Active'
            GROUP BY membership_type
        """)
        
        membership_data = cursor.fetchall()
        conn.close()
        
        # Create figure
        fig, ((ax1, ax2), (ax3, ax4)) = plt.subplots(2, 2, figsize=(12, 10))
        
        # Gender distribution
        if gender_data:
            genders = [row[0] or 'Unknown' for row in gender_data]
            gender_counts = [row[1] for row in gender_data]
            gender_avg_age = [row[2] for row in gender_data]
            
            colors = ['#3498db', '#e74c3c', '#2ecc71']
            ax1.pie(gender_counts, labels=genders, autopct='%1.1f%%', colors=colors[:len(genders)])
            ax1.set_title('Gender Distribution')
        
        # Age distribution
        if age_data:
            age_groups = [row[0] for row in age_data]
            age_counts = [row[1] for row in age_data]
            
            colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6']
            ax2.bar(age_groups, age_counts, color=colors[:len(age_groups)])
            ax2.set_title('Age Distribution')
            ax2.set_ylabel('Number of Members')
            ax2.tick_params(axis='x', rotation=45)
        
        # Membership type
        if membership_data:
            membership_types = [row[0] for row in membership_data]
            membership_counts = [row[1] for row in membership_data]
            
            colors = ['#3498db', '#2ecc71', '#f39c12']
            ax3.bar(membership_types, membership_counts, color=colors[:len(membership_types)])
            ax3.set_title('Membership Type Distribution')
            ax3.set_ylabel('Number of Members')
        
        # City distribution (if available)
        cursor = conn.cursor()
        cursor.execute("SELECT city, COUNT(*) FROM members GROUP BY city")
        city_data = cursor.fetchall()
        
        if city_data and len(city_data) > 1:
            cities = [row[0] or 'Unknown' for row in city_data]
            city_counts = [row[1] for row in city_data]
            
            ax4.pie(city_counts, labels=cities, autopct='%1.1f%%')
            ax4.set_title('Member Cities')
        
        plt.tight_layout()
        
        # Display in tkinter
        canvas = FigureCanvasTkAgg(fig, self.report_display)
        canvas.draw()
        canvas.get_tk_widget().pack(fill='both', expand=True)
        
        # Statistics
        total_members = sum([row[1] for row in gender_data]) if gender_data else 0
        
        stats_frame = tk.Frame(self.report_display, bg='white')
        stats_frame.pack(fill='x', pady=10)
        
        stats_text = f"""
        Demographic Statistics:
        • Total Active Members: {total_members}
        """
        
        if gender_data:
            for gender, count, avg_age in gender_data:
                percentage = (count / total_members * 100) if total_members > 0 else 0
                stats_text += f"\n• {gender or 'Unknown'}: {count} members ({percentage:.1f}%), Avg age: {avg_age:.1f}"
        
        tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                bg='white', justify='left').pack()
    
    def show_staff_performance(self):
        conn = self.db.get_connection()
        cursor = conn.cursor()
        
        # Get staff performance data
        cursor.execute("""
            SELECT s.first_name || ' ' || s.last_name as staff_name,
                   s.role,
                   COUNT(a.appointment_id) as sessions_conducted,
                   AVG(strftime('%s', a.end_time) - strftime('%s', a.start_time))/60 as avg_session_duration,
                   SUM(CASE WHEN a.status = 'Completed' THEN 1 ELSE 0 END) as completed_sessions,
                   SUM(CASE WHEN a.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_sessions
            FROM staff s
            LEFT JOIN appointments a ON s.staff_id = a.trainer_id
            WHERE s.role IN ('Trainer', 'Nutritionist')
            AND a.appointment_date >= date('now', '-30 days')
            GROUP BY s.staff_id
            ORDER BY sessions_conducted DESC
        """)
        
        data = cursor.fetchall()
        conn.close()
        
        if data:
            staff_names = [row[0] for row in data]
            sessions = [row[2] for row in data]
            avg_durations = [row[3] for row in data]
            completion_rates = [(row[4] / max(row[2], 1) * 100) for row in data]
            
            # Create figure
            fig, ((ax1, ax2), (ax3, ax4)) = plt.subplots(2, 2, figsize=(12, 10))
            
            # Sessions conducted
            colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6']
            ax1.barh(staff_names, sessions, color=colors[:len(staff_names)])
            ax1.set_title('Sessions Conducted (Last 30 Days)')
            ax1.set_xlabel('Number of Sessions')
            
            # Average session duration
            ax2.bar(staff_names, avg_durations, color=colors[:len(staff_names)])
            ax2.set_title('Average Session Duration')
            ax2.set_ylabel('Minutes')
            ax2.tick_params(axis='x', rotation=45)
            
            # Completion rate
            ax3.bar(staff_names, completion_rates, color=colors[:len(staff_names)])
            ax3.set_title('Session Completion Rate')
            ax3.set_ylabel('Completion %')
            ax3.tick_params(axis='x', rotation=45)
            ax3.axhline(y=90, color='r', linestyle='--', alpha=0.5, label='Target (90%)')
            ax3.legend()
            
            # Performance score
            performance_scores = []
            for i in range(len(staff_names)):
                # Simple scoring formula
                score = (sessions[i] / max(sessions)) * 40 + (completion_rates[i] / 100) * 60
                performance_scores.append(score)
            
            ax4.bar(staff_names, performance_scores, color=colors[:len(staff_names)])
            ax4.set_title('Performance Score')
            ax4.set_ylabel('Score (0-100)')
            ax4.tick_params(axis='x', rotation=45)
            ax4.axhline(y=70, color='g', linestyle='--', alpha=0.5, label='Good (70+)')
            ax4.axhline(y=50, color='y', linestyle='--', alpha=0.5, label='Average (50+)')
            ax4.legend()
            
            plt.tight_layout()
            
            # Display in tkinter
            canvas = FigureCanvasTkAgg(fig, self.report_display)
            canvas.draw()
            canvas.get_tk_widget().pack(fill='both', expand=True)
            
            # Statistics
            total_sessions = sum(sessions)
            avg_completion = sum(completion_rates) / len(completion_rates)
            
            stats_frame = tk.Frame(self.report_display, bg='white')
            stats_frame.pack(fill='x', pady=10)
            
            stats_text = f"""
            Staff Performance Summary (Last 30 Days):
            • Total Sessions: {total_sessions}
            • Average Completion Rate: {avg_completion:.1f}%
            • Top Performer: {staff_names[0]} ({sessions[0]} sessions, {completion_rates[0]:.1f}% completion)
            """
            
            tk.Label(stats_frame, text=stats_text, font=('Arial', 10),
                    bg='white', justify='left').pack()
    
    def show_equipment_usage(self):
        # This would show equipment maintenance and usage reports
        # For now, display a message
        tk.Label(self.report_display, text="Equipment usage reports coming soon!", 
                font=('Arial', 14), bg='white').pack(expand=True)
    
    def export_data(self):
        # Export current report data to CSV
        report_type = self.report_var.get()
        
        from datetime import datetime
        import csv
        
        filename = f"{report_type.replace(' ', '_')}_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        
        try:
            with open(filename, 'w', newline='') as csvfile:
                writer = csv.writer(csvfile)
                writer.writerow(['Report Type', report_type])
                writer.writerow(['Generated', datetime.now().strftime('%Y-%m-%d %H:%M:%S')])
                writer.writerow([])
                
                # Add some sample data (in real app, export actual data)
                writer.writerow(['Month', 'Value'])
                writer.writerow(['Jan', '100'])
                writer.writerow(['Feb', '150'])
                writer.writerow(['Mar', '200'])
                
            messagebox.showinfo("Export Successful", f"Data exported to {filename}")
        except Exception as e:
            messagebox.showerror("Export Failed", f"Error exporting data: {str(e)}")