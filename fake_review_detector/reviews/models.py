from django.db import models
from users.models import CustomUser
from products.models import Product

# Create your models here.

class Review(models.Model):
    product = models.ForeignKey(Product, on_delete=models.CASCADE, related_name='reviews')
    user = models.ForeignKey(CustomUser, on_delete=models.CASCADE)
    rating = models.PositiveIntegerField(default=1)
    comment = models.TextField()
    top_keywords = models.CharField(max_length=255, blank=True, null=True)
    created_at = models.DateTimeField(auto_now_add=True)

    # IMPORTANT: indexed for fast lookup
    ip_address = models.GenericIPAddressField(null=True, blank=True, db_index=True)

    sentiment = models.CharField(
        max_length=10,
        choices=[
            ('positive', 'Positive'),
            ('negative', 'Negative'),
            ('neutral', 'Neutral')
        ],
        blank=True
    )

    is_fake = models.BooleanField(default=False)

    def __str__(self):
        return f"Review by {self.user.username} on {self.product.name}"

    
class SuspiciousIP(models.Model):
    ip_address = models.CharField(max_length=45)
    fake_review_count = models.IntegerField()
    detected_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.ip_address
