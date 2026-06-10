from django import forms
from django.contrib.auth.forms import UserCreationForm
from .models import CustomUser

class RegisterForm(UserCreationForm):
    mobile_no = forms.CharField(max_length=15)
    address = forms.CharField(widget=forms.Textarea)
    profile_picture = forms.ImageField(required=False)

    class Meta:
        model = CustomUser
        fields = ['first_name', 'last_name', 'username', 'email', 'password1', 'password2', 'mobile_no', 'address', 'profile_picture']
