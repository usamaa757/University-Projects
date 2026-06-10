class Staff:
    def __init__(self, staff_id=None, first_name="", last_name="", role="", branch_id=1,
                 email="", phone="", hire_date=None, salary=0, specialization="",
                 schedule="{}", status="Active"):
        
        self.staff_id = staff_id
        self.first_name = first_name
        self.last_name = last_name
        self.role = role
        self.branch_id = branch_id
        self.email = email
        self.phone = phone
        self.hire_date = hire_date
        self.salary = salary
        self.specialization = specialization
        self.schedule = schedule
        self.status = status
    
    def get_full_name(self):
        return f"{self.first_name} {self.last_name}"
    
    def to_dict(self):
        return {
            'staff_id': self.staff_id,
            'full_name': self.get_full_name(),
            'role': self.role,
            'email': self.email,
            'phone': self.phone,
            'specialization': self.specialization,
            'status': self.status
        }