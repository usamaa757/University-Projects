from django.contrib.auth.models import AbstractUser
from django.db import models

class CustomUser(AbstractUser):
    ROLE_CHOICES = [
        ('admin', 'Admin'),
        ('operator', 'Data Entry Operator'),
    ]
    role = models.CharField(max_length=20, choices=ROLE_CHOICES, default='operator')

    def __str__(self):
        return f"{self.username} ({self.role})"

    def save(self, *args, **kwargs):
        super().save(*args, **kwargs)  
        
