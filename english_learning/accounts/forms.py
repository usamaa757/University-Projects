from django import forms
from django.contrib.auth.forms import UserCreationForm
from django.contrib.auth import get_user_model

User = get_user_model()

class CustomUserCreationForm(UserCreationForm):
    class Meta:
        model = User
        fields = ("full_name", "email", "role", "password1", "password2")


class UserUpdateForm(forms.ModelForm):
    class Meta:
        model = User
        fields = ("email", "role", "is_active")  # no password fields here
