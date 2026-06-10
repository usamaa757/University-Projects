from django.db import models
from django.contrib.auth.models import AbstractUser
from django.core.validators import RegexValidator

from django.contrib.auth.models import AbstractUser, BaseUserManager

class CustomUserManager(BaseUserManager):
    def create_user(self, mobile_no=None, username=None, password=None, **extra_fields):
        if not mobile_no and not username:
            raise ValueError("A user must have either a mobile number or username")
        
        extra_fields.setdefault('is_staff', False)
        extra_fields.setdefault('is_superuser', False)

        user = self.model(mobile_no=mobile_no, username=username, **extra_fields)
        user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, username, password=None, **extra_fields):
        extra_fields.setdefault('is_staff', True)
        extra_fields.setdefault('is_superuser', True)

        if not username:
            raise ValueError("Superusers must have a username")

        return self.create_user(username=username, password=password, **extra_fields)

# class CustomUser(AbstractUser):
#     mobile_no = models.CharField(
#         max_length=11, blank=True, null=True, unique=True,
        
#     )

#     objects = CustomUserManager()  # Use the custom manager

#     USERNAME_FIELD = 'username'  # Superusers log in with username
#     REQUIRED_FIELDS = ['mobile_no']  # Mobile number required for normal users


class CustomUser(AbstractUser):
    nationality = models.CharField(max_length=100, blank=True, null=True)
    city = models.CharField(max_length=100, blank=True, null=True)
    postal_address = models.TextField(blank=True, null=True)
    mobile_no = models.CharField(
        max_length=11, blank=True, null=True, unique=True,
        
    )

    objects = CustomUserManager()  # Use the custom manager

    USERNAME_FIELD = 'username'  # Superusers log in with username
    REQUIRED_FIELDS = ['mobile_no']  # Mobile number required for normal users
    preferred_season = models.CharField(
        max_length=10,
        choices=[
            ('summer', 'Summer'),
            ('winter', 'Winter'),
            ('spring', 'Spring'),
            ('autumn', 'Autumn'),
        ],
        blank=True,
        null=True
    )
    preferred_travel_type = models.CharField(
        max_length=20,
        choices=[
            ('adventure', 'Adventure'),
            ('cultural', 'Cultural'),
            ('beach', 'Beach Holiday'),
            ('city', 'City Exploration'),
            ('wildlife', 'Wildlife Safari'),
        ],
        blank=True,
        null=True
    )
    age_range = models.CharField(
        max_length=10,
        choices=[
            ('18-25', '18-25'),
            ('26-35', '26-35'),
            ('36-45', '36-45'),
            ('46-60', '46-60'),
            ('60+', '60+'),
        ],
        blank=True,
        null=True
    )
    budget_range = models.CharField(
        max_length=10,
        choices=[
            ('low', 'Low Budget'),
            ('medium', 'Medium Budget'),
            ('high', 'Luxury Travel'),
        ],
        blank=True,
        null=True
    )

    def __str__(self):
        return f"{self.first_name} {self.last_name} ({self.email})"


# class CustomUser(AbstractUser):
#     username = None  # Remove the username field
#     mobile_no = models.CharField(
#         max_length=15, blank=False, null=True,
#         unique=True,
#         validators=[
#             RegexValidator(
#                 regex=r'^\+?1?\d{9,15}$',
#                 message="Enter a valid mobile number (e.g., +1234567890)."
#             )
#         ]
#     )
#     USERNAME_FIELD = 'mobile_no'  # Use mobile_no as the unique identifier
#     REQUIRED_FIELDS = []  # No additional fields required

#     def __str__(self):
#         return self.mobile_no
# class CustomUser(AbstractUser):
#     email = models.EmailField(unique=True)
#     first_name = models.CharField(max_length=100, blank=False, null=True)
#     last_name = models.CharField(max_length=100, blank=False, null=True)
#     def __str__(self):
#         return self.first_name

#     def is_admin(self):
#         return self.is_superuser

#     def is_user(self):
#         return self.is_user

class Activity(models.Model):
    title = models.CharField(max_length=255)
    description = models.TextField()
    location = models.CharField(max_length=255)
    date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)
    category =   models.CharField(
        max_length=20,
        choices=[
            ('adventure', 'Adventure'),
            ('cultural', 'Cultural'),
            ('beach', 'Beach Holiday'),
            ('city', 'City Exploration'),
            ('wildlife', 'Wildlife Safari'),
        ],
        blank=True,
        null=True
    ) 

    def __str__(self):
        return self.title


class TravelTip(models.Model):
    title = models.CharField(max_length=255)
    content = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)
    category = models.CharField(
        max_length=20,
        choices=[
            ('adventure', 'Adventure'),
            ('cultural', 'Cultural'),
            ('beach', 'Beach Holiday'),
            ('city', 'City Exploration'),
            ('wildlife', 'Wildlife Safari'),
        ],
        blank=True,
        null=True
    )
    def __str__(self):
        return self.title

class Destination(models.Model):
    name = models.CharField(max_length=100)
    location = models.CharField(max_length=100)
    travel_type = models.CharField(
        max_length=20,
        choices=[
            ('adventure', 'Adventure'),
            ('cultural', 'Cultural'),
            ('beach', 'Beach Holiday'),
            ('city', 'City Exploration'),
            ('wildlife', 'Wildlife Safari'),
        ],
        blank=True,
        null=True
    )    
    description = models.TextField()
    image = models.ImageField(upload_to='destinations/', blank=True, null=True)
    def __str__(self):
        return self.travel_type  
    
class HotelDiscount(models.Model):
    hotel_name = models.CharField(max_length=255)
    discount_details = models.TextField()
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, related_name='hotel_discounts')
    valid_until = models.DateField()

    def __str__(self):
        return f"{self.hotel_name} - {self.destination.travel_type}"
