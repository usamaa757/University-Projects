from datetime import datetime, timedelta
import random
import string
import hashlib


class Helpers:
    @staticmethod
    def generate_id(prefix, length=8):
        """Generate a random ID with prefix"""
        chars = string.digits
        random_id = ''.join(random.choice(chars) for _ in range(length))
        return f"{prefix}{random_id}"
    
    @staticmethod
    def format_currency(amount):
        """Format amount as currency"""
        return f"PKR {amount:,.2f}"
    
    @staticmethod
    def calculate_age(birth_date):
        """Calculate age from birth date"""
        if not birth_date:
            return None
        
        birth = datetime.strptime(birth_date, '%Y-%m-%d')
        today = datetime.now()
        
        age = today.year - birth.year
        if (today.month, today.day) < (birth.month, birth.day):
            age -= 1
        
        return age
    
    def hash_password(self, password):
        return hashlib.sha256(password.encode()).hexdigest()

    @staticmethod
    def get_time_slots(start_hour=6, end_hour=22, interval=30):
        """Generate time slots for appointments"""
        slots = []
        for hour in range(start_hour, end_hour):
            for minute in [0, 30]:
                time_str = f"{hour:02d}:{minute:02d}"
                slots.append(time_str)
        return slots
    
    @staticmethod
    def days_until_expiry(expiry_date):
        """Calculate days until expiry"""
        if not expiry_date:
            return None
        
        expiry = datetime.strptime(expiry_date, '%Y-%m-%d')
        today = datetime.now()
        
        delta = expiry - today
        return delta.days