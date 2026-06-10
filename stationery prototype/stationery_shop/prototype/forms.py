# login_app/forms.py
from django import forms
from django.core.exceptions import ValidationError
import re

def validate_password(value):
    if not re.search(r'[^A-Za-z0-9]', value):
        raise ValidationError("Password must contain at least one special character.")

class LoginForm(forms.Form):
    username = forms.CharField(label='Username', required=True)
    password = forms.CharField(
        label='Password',
        widget=forms.PasswordInput,
        validators=[validate_password]
    )