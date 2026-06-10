from django import forms
from django.contrib.auth.forms import AuthenticationForm, UserChangeForm, UserCreationForm
from django.contrib.auth import get_user_model, authenticate
from .models import CustomUser, Activity, TravelTip, Destination, HotelDiscount, TravelInterest, Review, Comment, Photos, Like, ServiceProvider
from captcha.fields import CaptchaField

CustomUser = get_user_model()


class UserCreationForm(forms.ModelForm):
    username = forms.CharField(
        max_length=100,
        widget=forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Your Name'}),
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
            'username','nationality', 'city', 'postal_address',
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
    
        
        user.set_password(self.cleaned_data["password"])

        if commit:
            user.save()
        
        return user



    def clean(self):
        cleaned_data = super().clean()
        if cleaned_data.get("password") != cleaned_data.get("confirm_password"):
            self.add_error('confirm_password', "Passwords do not match.")
        return cleaned_data

class LoginForm(forms.Form):
    username = forms.CharField(max_length=150)
    password = forms.CharField(widget=forms.PasswordInput)

    def clean(self):
        cleaned_data = super().clean()
        username = cleaned_data.get("username")
        password = cleaned_data.get("password")

        if not username or not password:
            raise forms.ValidationError("Both username and password are required.")

        return cleaned_data

class ChangeUserForm(UserChangeForm):
    username = forms.CharField(
        widget=forms.TextInput(attrs={'class': 'form-control'}),
        required=True,
        label="First Name"
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
        fields = ['title', 'description', 'destination', 'date', 'category']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Activity title'}),
            'description': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter activity details'}),
            'category': forms.Select(attrs={'class': 'form-control'}),
            'destination': forms.Select(attrs={'class': 'form-control'}),
            'date': forms.DateInput(attrs={'class': 'form-control', 'type': 'date'}),
        }

class TravelTipForm(forms.ModelForm):
    class Meta:
        model = TravelTip
        fields = ['title', 'content', 'category', 'destination']
        widgets = {
            'title': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Tip title'}),
            'category': forms.Select(attrs={'class': 'form-control'}),
            'content': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Enter travel tip details'}),
            'destination': forms.Select(attrs={'class': 'form-control'}),
        }

# class MobileLoginForm(AuthenticationForm):
#     username = forms.CharField(label="Mobile Number", widget=forms.TextInput(attrs={'class': 'form-control'}))

class DestinationForm(forms.ModelForm):
    class Meta:
        model = Destination
        fields = ['name', 'description', 'location', 'image', 'travel_type']
        widgets = {
    'name': forms.TextInput(attrs={
        'class': 'form-control', 
        'placeholder': 'Enter destination name'
    }),
    'travel_type': forms.Select(attrs={
        'class': 'form-control'
    
    }),
    'description': forms.Textarea(attrs={
        'class': 'form-control', 
        'rows': 3, 
        'placeholder': 'Write a brief description about the destination'
    }),
    'location': forms.TextInput(attrs={
        'class': 'form-control', 
        'placeholder': 'Enter location'
    }),
    'image': forms.ClearableFileInput(attrs={
        'class': 'form-control'
       
    }),
}

class UserProfileForm(forms.ModelForm):
    class Meta:
        model = CustomUser
        fields = [
            'username', 'nationality', 'city', 'postal_address',
            'mobile_no', 'email', 'preferred_season', 'preferred_travel_type',
            'age_range', 'budget_range'
        ]
        widgets = {
            'username': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter Username'}),
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
class HotelDiscountForm(forms.ModelForm):  
    class Meta:
        model = HotelDiscount  
        fields = ['hotel_name', 'discount_details', 'destination', 'valid_until']
        widgets = {
            'hotel_name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Enter hotel name'}),
            'discount_details': forms.Textarea(attrs={'class': 'form-control', 'rows': 4, 'placeholder': 'Enter discount details'}),
            'destination': forms.Select(attrs={'class': 'form-control'}),
            'valid_until': forms.DateInput(attrs={'type': 'date', 'class': 'form-control'}),
        }
class TravelInterestForm(forms.ModelForm):
    class Meta:
        model = TravelInterest
        fields = ['title', 'destination', 'start_date', 'end_date', 'accommodation', 'activities', 'travel_type']
        widgets = {
    'title': forms.TextInput(attrs={
        'class': 'form-control', 
        'placeholder': 'Enter travel interest title'
    }),
    'destination': forms.Select(attrs={
        'class': 'form-control', 
        'placeholder': 'Enter destination'
    }),
    'start_date': forms.DateInput(attrs={
        'class': 'form-control', 
        'type': 'date',
        'placeholder': 'Start Date'
    }),
    'end_date': forms.DateInput(attrs={
        'class': 'form-control', 
        'type': 'date',
        'placeholder': 'End Date'
    }),
    'accommodation': forms.Textarea(attrs={
        'class': 'form-control', 
        'rows': 2,
        'placeholder': 'Where will you stay?'
    }),
    'activities': forms.Textarea(attrs={
        'class': 'form-control', 
        'rows': 2,
        'placeholder': 'Planned activities or places to visit'
    }),
    'travel_type': forms.Select(attrs={
        'class': 'form-control'
    
    })
}
class ReviewForm(forms.ModelForm):
    class Meta:
        model = Review
        fields = ['rating', 'comment']
        widgets = {
            'rating': forms.Select(
                choices=[(i, f"{i} Stars") for i in range(1, 6)],
                attrs={'class': 'form-select'}
            ),
            'comment': forms.Textarea(
                attrs={'class': 'form-control', 'rows': 4}
            ),
        }

class CommentForm(forms.ModelForm):
    class Meta:
        model = Comment
        fields = ['text']
        widgets = {
            'text': forms.Textarea(attrs={'rows': 3, 'class': 'form-control', 'placeholder': 'Write a comment...'})
        }
class PhotoUploadForm(forms.Form):
    photos = forms.FileField(
        widget=forms.ClearableFileInput(attrs={'class': 'form-control'}),
        label='Upload Destination Photos'
    )


class ServiceProviderForm(forms.ModelForm):
    class Meta:
        model = ServiceProvider
        fields = '__all__'
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Company Name'}),
            'service_type': forms.Select(attrs={'class': 'form-select'}),
            'contact_person': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Contact Person'}),
            'phone': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Phone Number'}),
            'email': forms.EmailInput(attrs={'class': 'form-control', 'placeholder': 'Email Address'}),
            'website': forms.URLInput(attrs={'class': 'form-control', 'placeholder': 'Website (optional)'}),
            'notes': forms.Textarea(attrs={'class': 'form-control', 'rows': 3, 'placeholder': 'Additional Notes'}),
        }
