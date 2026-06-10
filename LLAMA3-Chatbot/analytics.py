import pandas as pd
import sqlite3
import matplotlib.pyplot as plt
import seaborn as sns
import os
from datetime import datetime

class Analytics:
    def __init__(self, db_path="chatbot.db"):
        self.db_path = db_path
        os.makedirs("static/plots", exist_ok=True)
        plt.style.use('seaborn-v0_8-darkgrid')
    
    def get_data(self, query):
        conn = sqlite3.connect(self.db_path)
        df = pd.read_sql_query(query, conn)
        conn.close()
        return df
    
    def generate_daily_stats(self):
        """Generate daily usage statistics"""
        query = """
            SELECT 
                date(timestamp) as date,
                COUNT(*) as total_messages,
                COUNT(DISTINCT session_id) as active_sessions,
                AVG(response_time) as avg_response_time
            FROM messages
            WHERE timestamp >= date('now', '-7 days')
            GROUP BY date(timestamp)
            ORDER BY date
        """
        
        df = self.get_data(query)
        
        if len(df) > 0:
            fig, axes = plt.subplots(2, 1, figsize=(10, 8))
            
            axes[0].bar(df['date'], df['total_messages'], color='skyblue', edgecolor='navy')
            axes[0].set_title('Daily Messages', fontsize=14, fontweight='bold')
            axes[0].set_ylabel('Number of Messages')
            axes[0].tick_params(axis='x', rotation=45)
            
            axes[1].plot(df['date'], df['avg_response_time'], marker='o', color='green', linewidth=2)
            axes[1].set_title('Average Response Time (seconds)', fontsize=14, fontweight='bold')
            axes[1].set_ylabel('Response Time (s)')
            axes[1].tick_params(axis='x', rotation=45)
            
            plt.tight_layout()
            plt.savefig('static/plots/daily_stats.png', dpi=100, bbox_inches='tight')
            plt.close()
            
            return df.to_dict('records')
        return []
    
    def generate_feedback_analytics(self):
        """Analyze user feedback"""
        query = """
            SELECT 
                f.rating,
                f.correctness,
                COUNT(*) as count
            FROM feedback f
            GROUP BY f.rating, f.correctness
        """
        
        df = self.get_data(query)
        
        if len(df) > 0:
            fig, axes = plt.subplots(1, 2, figsize=(12, 5))
            
            # Rating distribution
            rating_summary = df.groupby('rating')['count'].sum()
            colors = ['#ff6b6b', '#feca57', '#48dbfb', '#1dd1a1', '#5f27cd']
            axes[0].pie(rating_summary.values, labels=rating_summary.index, autopct='%1.1f%%', 
                       colors=colors[:len(rating_summary)])
            axes[0].set_title('User Ratings Distribution', fontsize=14, fontweight='bold')
            
            # Correctness bar chart
            correctness_summary = df.groupby('correctness')['count'].sum()
            axes[1].bar(correctness_summary.index, correctness_summary.values, 
                       color=['#2ecc71', '#f39c12', '#e74c3c'])
            axes[1].set_title('Response Correctness', fontsize=14, fontweight='bold')
            axes[1].set_ylabel('Count')
            
            plt.tight_layout()
            plt.savefig('static/plots/feedback_analytics.png', dpi=100, bbox_inches='tight')
            plt.close()
            
            return df.to_dict('records')
        return []
    
    def get_summary_stats(self):
        """Get summary statistics"""
        queries = {
            'total_users': "SELECT COUNT(*) as count FROM users",
            'total_sessions': "SELECT COUNT(*) as count FROM sessions",
            'total_messages': "SELECT COUNT(*) as count FROM messages",
            'avg_rating': "SELECT AVG(rating) as avg FROM feedback WHERE rating IS NOT NULL",
            'total_feedback': "SELECT COUNT(*) as count FROM feedback"
        }
        
        stats = {}
        for key, query in queries.items():
            df = self.get_data(query)
            if len(df) > 0:
                stats[key] = df.iloc[0]['count'] if key != 'avg_rating' else round(df.iloc[0]['avg'], 2)
            else:
                stats[key] = 0
        
        return stats
    
    def generate_all_analytics(self):
        """Generate all analytics and return data"""
        return {
            'daily_stats': self.generate_daily_stats(),
            'feedback': self.generate_feedback_analytics(),
            'summary': self.get_summary_stats()
        }