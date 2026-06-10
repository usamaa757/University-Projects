# reviews/forms.py
from django import forms
from django.contrib.auth.forms import UserCreationForm, AuthenticationForm
from .models import CustomUser


class CustomUserCreationForm(forms.ModelForm):
    username = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Your Name'})
    )
    city = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Your city'})
    )
    email = forms.EmailField(
        widget=forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'Enter Email'}),
        required=True
    )
    password = forms.CharField(
        widget=forms.PasswordInput(attrs={'class': 'form-control', 'placeholder': 'Enter Password'}),
        required=True
    )
    confirm_password = forms.CharField(
        widget=forms.PasswordInput(attrs={'class': 'form-control', 'placeholder': 'Confirm Password'}),
        required=True
    )

    class Meta:
        model = CustomUser
        fields = ['username', 'email', 'city', 'password']

    def clean(self):
        cleaned_data = super().clean()
        password = cleaned_data.get("password")
        confirm_password = cleaned_data.get("confirm_password")

        if password != confirm_password:
            raise forms.ValidationError("Passwords do not match.")

        return cleaned_data

    def save(self, commit=True):
        user = super().save(commit=False)
        user.set_password(self.cleaned_data["password"])  # hashes password
        if commit:
            user.save()
        return user


class CustomLoginForm(AuthenticationForm):
    username = forms.CharField(
    max_length=100,
    widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Your Name'})
)
    password = forms.CharField(
    widget=forms.PasswordInput(attrs={
        'class': 'form-control', 
        'placeholder': 'Enter Password',
        'style': 'width: 100%'  # Full width
    }),
    required=True
)
    class Meta:
        model = CustomUser
        fields = ['username', 'password']

