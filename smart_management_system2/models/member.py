from datetime import datetime, timedelta
import json

class Member:
    def __init__(self, member_id=None, first_name="", last_name="", email="", phone="", 
                 date_of_birth=None, gender="", address="", city="", emergency_contact="",
                 medical_conditions="", fitness_goals="", membership_type="Regular",
                 join_date=None, expiry_date=None, status="Active", branch_id=1,
                 subscription_plan="Monthly", payment_method="Cash"):
        
        self.member_id = member_id
        self.first_name = first_name
        self.last_name = last_name
        self.email = email
        self.phone = phone
        self.date_of_birth = date_of_birth
        self.gender = gender
        self.address = address
        self.city = city
        self.emergency_contact = emergency_contact
        self.medical_conditions = medical_conditions
        self.fitness_goals = fitness_goals
        self.membership_type = membership_type
        self.join_date = join_date or datetime.now().strftime('%Y-%m-%d')
        self.expiry_date = expiry_date or (datetime.now() + timedelta(days=30)).strftime('%Y-%m-%d')
        self.status = status
        self.branch_id = branch_id
        self.subscription_plan = subscription_plan
        self.payment_method = payment_method
    
    def calculate_membership_fee(self):
        """Calculate membership fee based on type and plan"""
        base_fees = {
            'Regular': {'Monthly': 3000, 'Quarterly': 8000, 'Annual': 28000},
            'Premium': {'Monthly': 5000, 'Quarterly': 13500, 'Annual': 48000},
            'Trial': {'Monthly': 1000, 'Quarterly': 0, 'Annual': 0}
        }
        
        if self.membership_type in base_fees and self.subscription_plan in base_fees[self.membership_type]:
            return base_fees[self.membership_type][self.subscription_plan]
        return 0
    
    def to_dict(self):
        return {
            'member_id': self.member_id,
            'first_name': self.first_name,
            'last_name': self.last_name,
            'email': self.email,
            'phone': self.phone,
            'date_of_birth': self.date_of_birth,
            'gender': self.gender,
            'membership_type': self.membership_type,
            'status': self.status,
            'subscription_plan': self.subscription_plan,
            'expiry_date': self.expiry_date
        }
    
    @classmethod
    def from_dict(cls, data):
        return cls(**data)