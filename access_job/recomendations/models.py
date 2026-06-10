from django.db import models

from jobs.models import Job


class Recommendation(models.Model):
    MODEL_CHOICES = [
        ("content", "Content Based"),
        ("collaborative", "Collaborative"),
        ("hybrid", "Hybrid"),
    ]

    user = models.ForeignKey("auth.User", on_delete=models.CASCADE, related_name="recommendations")
    job = models.ForeignKey(Job, on_delete=models.CASCADE, related_name="recommendations")
    model_type = models.CharField(max_length=20, choices=MODEL_CHOICES, default="hybrid")
    score = models.FloatField(default=0.0)
    reason = models.CharField(max_length=255, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ["-score", "-created_at"]
        unique_together = ("user", "job", "model_type")

    def __str__(self):
        return f"{self.user.username} - {self.job.title} ({self.model_type})"
