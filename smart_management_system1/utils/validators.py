import re
from datetime import datetime

class Validators:
    @staticmethod
    def validate_email(email):
        pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        return re.match(pattern, email) is not None
    
    @staticmethod
    def validate_phone(phone):
        # Pakistani phone number format
        pattern = r'^(\+92|0)[0-9]{10}$'
        return re.match(pattern, phone) is not None
    
    @staticmethod
    def validate_date(date_str, format='%Y-%m-%d'):
        try:
            datetime.strptime(date_str, format)
            return True
        except ValueError:
            return False
    
    @staticmethod
    def validate_time(time_str, format='%H:%M'):
        try:
            datetime.strptime(time_str, format)
            return True
        except ValueError:
            return False
    
    @staticmethod
    def validate_amount(amount_str):
        try:
            amount = float(amount_str)
            return amount >= 0
        except ValueError:
            return False