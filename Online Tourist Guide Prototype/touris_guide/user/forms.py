from django import forms
from django.contrib.auth.forms import AuthenticationForm, UserChangeForm, UserCreationForm
from django.contrib.auth import get_user_model, authenticate
from .models import CustomUser, Activity, TravelTip, Destination, HotelDiscount
from captcha.fields import CaptchaField

CustomUser = get_user_model()


class UserCreationForm(forms.ModelForm):
    first_name = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter First Name'}),
        required=True
    )
    last_name = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Last Name'}),
        required=True
    )
    nationality = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Nationality'}),
        required=True
    )
    city = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter City'}),
        required=True
    )
    postal_address = forms.CharField(
        widget=forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter Postal Address', 'rows': 3}),
        required=True
    )
    mobile_no = forms.CharField(
        max_length=15,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Mobile No.'}),
        required=True
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

    # Personal Preferences
    PREFERRED_SEASONS = [
        ('summer', 'Summer'),
        ('winter', 'Winter'),
        ('spring', 'Spring'),
        ('autumn', 'Autumn'),
    ]
    preferred_season = forms.ChoiceField(
        choices=PREFERRED_SEASONS,
        widget=forms.Select(attrs={'class': 'form-control'}),
        required=True
    )

    PREFERRED_TRAVEL_TYPE = [
        ('adventure', 'Adventure'),
        ('cultural', 'Cultural'),
        ('beach', 'Beach Holiday'),
        ('city', 'City Exploration'),
        ('wildlife', 'Wildlife Safari'),
    ]
    preferred_travel_type = forms.ChoiceField(
        choices=PREFERRED_TRAVEL_TYPE,
        widget=forms.Select(attrs={'class': 'form-control'}),
        required=True
    )

    AGE_RANGE = [
        ('18-25', '18-25'),
        ('26-35', '26-35'),
        ('36-45', '36-45'),
        ('46-60', '46-60'),
        ('60+', '60+'),
    ]
    age_range = forms.ChoiceField(
        choices=AGE_RANGE,
        widget=forms.Select(attrs={'class': 'form-control'}),
        required=True
    )

    BUDGET_RANGE = [
        ('low', 'Low Budget'),
        ('medium', 'Medium Budget'),
        ('high', 'Luxury Travel'),
    ]
    budget_range = forms.ChoiceField(
        choices=BUDGET_RANGE,
        widget=forms.Select(attrs={'class': 'form-control'}),
        required=True
    )

    # Captcha (Basic Validation - You can integrate Google reCAPTCHA for better security)
    captcha = CaptchaField()

    class Meta:
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'nationality', 'city', 'postal_address',
            'mobile_no', 'email', 'password', 'confirm_password',
            'preferred_season', 'preferred_travel_type', 'age_range', 'budget_range', 'captcha'
        ]

    def clean(self):
        cleaned_data = super().clean()
        password = cleaned_data.get("password")
        confirm_password = cleaned_data.get("confirm_password")

        if password and confirm_password and password != confirm_password:
            self.add_error("confirm_password", "Passwords do not match.")
        
        return cleaned_data

    def save(self, commit=True):
        user = super().save(commit=False)
        
        # Generate a unique username using mobile_no
        if not user.username:
            user.username = f"user_{user.mobile_no}"  # Example: user_1234567890
        
        user.set_password(self.cleaned_data["password"])

        if commit:
            user.save()
        
        return user



    def clean(self):
        cleaned_data = super().clean()
        if cleaned_data.get("password") != cleaned_data.get("confirm_password"):
            self.add_error('confirm_password', "Passwords do not match.")
        return cleaned_data

    

# class LoginForm(forms.Form):
#     username = forms.CharField(widget=forms.TextInput(attrs={'class': 'form-control'}))
#     password = forms.CharField(widget=forms.PasswordInput(attrs={'class': 'form-control'}))

#     def __init__(self, *args, **kwargs):
#         self.request = kwargs.pop('request', None)  # Store request
#         super().__init__(*args, **kwargs)

#     def clean(self):
#         cleaned_data = super().clean()
#         username = cleaned_data.get("username")
#         password = cleaned_data.get("password")

#         if username and password:
#             user = authenticate(request=self.request, username=username, password=password)
#             if user is None:
#                 raise forms.ValidationError("Invalid username or password.")
        
#         return cleaned_data
class LoginForm(forms.Form):
    username = forms.CharField(max_length=150)
    password = forms.CharField(widget=forms.PasswordInput)

    def __init__(self, *args, **kwargs):
        self.request = kwargs.pop('request', None)  # Get the request object if passed
        super().__init__(*args, **kwargs)

    def clean(self):
        cleaned_data = super().clean()
        username = cleaned_data.get("username")
        password = cleaned_data.get("password")

        if username and password:
            user = authenticate(request=self.request, username=username, password=password)  # Now request is passed correctly
            if user is None:
                raise forms.ValidationError("Invalid username or password.")

        return cleaned_data
class ChangeUserForm(UserChangeForm):
    first_name = forms.CharField(
        widget=forms.TextInput(attrs={'class': 'form-control'}),
        required=True,
        label="First Name"
    )

    last_name = forms.CharField(
        widget=forms.TextInput(attrs={'class': 'form-control'}),
        required=True,
        label="Last Name"
    )

    email = forms.EmailField(
        widget=forms.EmailInput(attrs={'class': 'form-control'}),
        required=True,
        label="Email"
    )

    new_password = forms.CharField(
        widget=forms.PasswordInput(attrs={'class': 'form-control'}),
        required=False,
        label="New Password"
    )

    confirm_new_password = forms.CharField(
        widget=forms.PasswordInput(attrs={'class': 'form-control'}),
        required=False,
        label="Confirm New Password"
    )

    class Meta:
        model = CustomUser
        fields = ['first_name', 'last_name', 'email']

    def clean(self):
        cleaned_data = super().clean()
        new_password = cleaned_data.get('new_password')
        confirm_new_password = cleaned_data.get('confirm_new_password')

        if new_password and new_password != confirm_new_password:
            self.add_error('confirm_new_password', "Passwords do not match.")

        return cleaned_data

    def save(self, commit=True):
        user = super().save(commit=False)
        if self.cleaned_data.get('new_password'):
            user.set_password(self.cleaned_data['new_password'])
        if commit:
            user.save()
        return user
    
class ActivityForm(forms.ModelForm):
    class Meta:
        model = Activity
        fields = ['title', 'description', 'location', 'date']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Activity title'}),
            'description': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter activity details'}),
            'location': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Location'}),
            'date': forms.DateInput(attrs={'class': 'form-control', 'type': 'date'}),
        }


class TravelTipForm(forms.ModelForm):
    class Meta:
        model = TravelTip
        fields = ['title', 'content']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Tip title'}),
            'content': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter travel tip details'}),
        }
class MobileLoginForm(AuthenticationForm):
    username = forms.CharField(label="Mobile Number", widget=forms.TextInput(attrs={'class': 'form-control'}))

class DestinationForm(forms.ModelForm):
    class Meta:
        model = Destination
        fields = ['name', 'description', 'location', 'image']
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control'}),
            'description': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
            'location': forms.TextInput(attrs={'class': 'form-control'}),
            'image': forms.ClearableFileInput(attrs={'class': 'form-control'}),
        }
class UserProfileForm(forms.ModelForm):
    class Meta:
        model = CustomUser
        fields = [
            'first_name', 'last_name', 'nationality', 'city', 'postal_address',
            'mobile_no', 'email', 'preferred_season', 'preferred_travel_type',
            'age_range', 'budget_range'
        ]
        widgets = {
            'first_name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter First Name'}),
            'last_name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Last Name'}),
            'nationality': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Nationality'}),
            'city': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter City'}),
            'postal_address': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter Postal Address', 'rows': 3}),
            'mobile_no': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Mobile No.'}),
            'email': forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'Enter Email'}),
            'preferred_season': forms.Select(attrs={'class': 'form-control'}),
            'preferred_travel_type': forms.Select(attrs={'class': 'form-control'}),
            'age_range': forms.Select(attrs={'class': 'form-control'}),
            'budget_range': forms.Select(attrs={'class': 'form-control'}),
        }

    def clean_email(self):
        email = self.cleaned_data.get("email")
        if CustomUser.objects.exclude(pk=self.instance.pk).filter(email=email).exists():
            raise forms.ValidationError("This email is already in use. Please choose a different one.")
        return email
class HotelDiscountForm(forms.ModelForm):  # ✅ Inherit from forms.ModelForm
    class Meta:
        model = HotelDiscount  # ✅ Specify the model
        fields = ['hotel_name', 'discount_details', 'destination', 'valid_until']
        widgets = {
            'hotel_name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter hotel name'}),
            'discount_details': forms.Textarea(attrs={'class': 'form-control', 'rows': 4, 'placeholder': 'Enter discount details'}),
            'destination': forms.Select(attrs={'class': 'form-control'}),
            'valid_until': forms.DateInput(attrs={'type': 'date', 'class': 'form-control'}),
        }