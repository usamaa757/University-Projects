from django.db import models
from django.contrib.auth.models import AbstractUser
from django.db import models
from django.contrib.auth.models import AbstractUser
from django.db import models
from django.contrib.auth.models import AbstractUser

class CustomUser(AbstractUser):
    username = models.CharField(max_length=100, blank=True, null=True, unique=True)  
    email = models.EmailField(unique=True)  
    nationality = models.CharField(max_length=100, blank=True, null=True)
    city = models.CharField(max_length=100, blank=True, null=True)
    postal_address = models.TextField(blank=True, null=True)
    mobile_no = models.CharField(max_length=11, blank=True, null=True, unique=True)

    preferred_season = models.CharField(
        max_length=10,
        choices=[('summer', 'Summer'), ('winter', 'Winter'), ('spring', 'Spring'), ('autumn', 'Autumn')],
        blank=True,
        null=True
    )
    preferred_travel_type = models.CharField(
        max_length=20,
        choices=[('adventure', 'Adventure'), ('cultural', 'Cultural'), ('beach', 'Beach Holiday'), 
                 ('city', 'City Exploration'), ('wildlife', 'Wildlife Safari')],
        blank=True,
        null=True
    )
    age_range = models.CharField(
        max_length=10,
        choices=[('18-25', '18-25'), ('26-35', '26-35'), ('36-45', '36-45'), 
                 ('46-60', '46-60'), ('60+', '60+')], 
        blank=True, 
        null=True
    )
    budget_range = models.CharField(
        max_length=10,
        choices=[('low', 'Low Budget'), ('medium', 'Medium Budget'), ('high', 'Luxury Travel')],
        blank=True,
        null=True
    )

    # Set username as the primary identifier for login
    USERNAME_FIELD = 'username'  # Change to 'username' for login
    REQUIRED_FIELDS = ['email']  # Email is still required for user creation, but not for login

    def __str__(self):
        return f"{self.username} ({self.preferred_travel_type})"



    def __str__(self):
        return f"{self.title} ({self.category})"

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
       return self.name


class Activity(models.Model):
    title = models.CharField(max_length=255)
    description = models.TextField()
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, related_name='Activities', blank=True, null=True)
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
        return f"{self.title} {self.category}"


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
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, blank=True, null=True, related_name='travel_tips')


class HotelDiscount(models.Model):
    hotel_name = models.CharField(max_length=255)
    discount_details = models.TextField()
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, related_name='hotel_discounts')
    valid_until = models.DateField()
   
    def __str__(self):
        return f"{self.hotel_name} - {self.destination.travel_type}"
class TravelInterest(models.Model):
    user = models.ForeignKey(CustomUser, on_delete=models.CASCADE, related_name='travel_interests')
    title = models.CharField(max_length=200)
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE)
    start_date = models.DateField()
    end_date = models.DateField()
    accommodation = models.TextField(blank=True, null=True)
    activities = models.TextField(blank=True, null=True)
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
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.title} ({self.user.username})"


class Review(models.Model):
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, related_name='reviews')
    user = models.ForeignKey(CustomUser, on_delete=models.CASCADE)
    rating = models.IntegerField(choices=[(i, i) for i in range(1, 6)])
    comment = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.user.username} - {self.destination.name}"
class Photos(models.Model):
    destination = models.ForeignKey(Destination, on_delete=models.CASCADE, related_name='photos')
    image = models.ImageField(upload_to='destination_photos/')
    uploaded_by = models.ForeignKey(CustomUser, on_delete=models.CASCADE)
    uploaded_at = models.DateTimeField(auto_now_add=True)
    likes = models.ManyToManyField(CustomUser, related_name='liked_photos', blank=True)

class Like(models.Model):
    user = models.ForeignKey(CustomUser, on_delete=models.CASCADE)
    photo = models.ForeignKey(Photos, on_delete=models.CASCADE, related_name='photo_likes')

class Comment(models.Model):
    user = models.ForeignKey(CustomUser, on_delete=models.CASCADE)
    photo = models.ForeignKey(Photos, on_delete=models.CASCADE, related_name='comments')
    text = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)
# models.py
class ServiceProvider(models.Model):
    SERVICE_TYPES = [
        ('hotel', 'Hotel Booking'),
        ('flight', 'Flight Booking'),
        ('transport', 'Transport Service'),
    ]

    name = models.CharField(max_length=255)
    service_type = models.CharField(max_length=20, choices=SERVICE_TYPES)
    contact_person = models.CharField(max_length=255)
    phone = models.CharField(max_length=20)
    email = models.EmailField()
    website = models.URLField(blank=True, null=True)
    notes = models.TextField(blank=True)

    def __str__(self):
        return f"{self.name} ({self.get_service_type_display()})"
