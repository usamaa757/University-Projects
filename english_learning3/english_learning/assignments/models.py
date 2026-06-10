from django.db import models
from django.conf import settings
from django.utils import timezone
from accounts.models import User

class Assignment(models.Model):
    teacher = models.ForeignKey(User, on_delete=models.CASCADE)
    title = models.CharField(max_length=200)
    description = models.TextField()
    file = models.FileField(upload_to='assignments/')
    due_date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)
    total_marks = models.PositiveIntegerField(null=True, blank=True) 

    @property
    def is_active(self):
        return timezone.now() <= self.due_date

    def __str__(self):
        return self.title

class AssignmentSubmission(models.Model):
    assignment = models.ForeignKey(Assignment, on_delete=models.CASCADE)
    student = models.ForeignKey(User, on_delete=models.CASCADE)
    file = models.FileField(upload_to='submissions/')
    submitted_at = models.DateTimeField(auto_now_add=True)
    marks = models.PositiveIntegerField(null=True, blank=True) 

    def __str__(self):
        return f"{self.assignment.title} - {self.student.username}"
