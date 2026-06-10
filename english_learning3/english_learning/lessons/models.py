from django.db import models
from django.conf import settings
from django.utils import timezone
from accounts.models import User


class Lesson(models.Model):
    LEVEL_CHOICES = [
        ("beginner", "Beginner"),
        ("intermediate", "Intermediate"),
        ("advanced", "Advanced"),
    ]
    CATEGORY_CHOICES = [
        ("grammar", "Grammar"),
        ("vocabulary", "Vocabulary"),
        ("listening", "Listening"),
        ("speaking", "Speaking"),
    ]

    title = models.CharField(max_length=200)
    description = models.TextField(blank=True)
    level = models.CharField(max_length=20, choices=LEVEL_CHOICES)
    category = models.CharField(max_length=20, choices=CATEGORY_CHOICES)
    content = models.TextField()  # text explanation or lesson notes
    created_at = models.DateField(auto_now_add=True)

    def __str__(self):
        return f"{self.title} ({self.get_level_display()} - {self.get_category_display()})"
class LessonMedia(models.Model):
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="media")
    media_type = models.CharField(
        max_length=20,
        choices=[("video", "Video"), ("audio", "Audio"), ("flashcard", "Flashcard")]
    )
    file = models.FileField(upload_to="lesson_media/", blank=True, null=True)

    # Flashcard fields
    flashcard_front = models.CharField(max_length=255, blank=True, null=True)
    flashcard_back = models.TextField(blank=True, null=True)

    def __str__(self):
        return f"{self.lesson.title} - {self.media_type}"

# Create your models here.
class LiveSession(models.Model):
    teacher = models.ForeignKey(User, on_delete=models.CASCADE)
    title = models.CharField(max_length=200)
    description = models.TextField()
    date_time = models.DateTimeField()
    meeting_link = models.URLField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.title
