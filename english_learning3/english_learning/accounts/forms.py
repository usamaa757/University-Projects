from django import forms
from django.contrib.auth.forms import UserCreationForm
from .models import User

class UserRegisterForm(UserCreationForm):
    email = forms.EmailField()

    class Meta:
        model = User
        fields = ['username', 'email', 'role', 'password1', 'password2']
        widgets = {
            'role': forms.Select(attrs={'class': 'select'}),
        }

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # Exclude 'admin' from dropdown
        self.fields['role'].choices = [
            (value, label) for value, label in User.ROLE_CHOICES if value != 'admin'
        ]



class UserEditForm(forms.ModelForm):

    class Meta:
        model = User
        fields = ['username', 'email', 'is_active']  # password NOT here

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in self.fields:
            self.fields[field].widget.attrs.update({'class': 'form-control'})
