from django.contrib.auth.models import AbstractUser
from django.db import models
from django.conf import settings
from django.utils import timezone

# User:

class User(AbstractUser):
    ROLE_CHOICES = [
        ('admin', 'Admin'),
        ('teacher', 'Teacher'),
        ('student', 'Student'),
    ]
    role = models.CharField(max_length=10, choices=ROLE_CHOICES, default='student')

    def is_admin(self):
        return self.role == 'admin'

    def is_teacher(self):
        return self.role == 'teacher'

    def is_student(self):
        return self.role == 'student'

    def __str__(self):
        return f"{self.username} ({self.role})"


# Lesson:

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
    link = models.URLField(blank=True, null=True)
    front_text = models.CharField(max_length=255, blank=True, null=True)  # Front of card
    back_text = models.TextField(blank=True, null=True)  # Back of card


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


# Assignment:

class Assignment(models.Model):
    teacher = models.ForeignKey(User, on_delete=models.CASCADE)
    title = models.CharField(max_length=200)
    description = models.TextField()
    file = models.FileField(upload_to='assignments/')
    due_date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)
    total_marks = models.PositiveIntegerField(default=20)
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

# Chat:

class Room(models.Model):
    name = models.CharField(max_length=100, unique=True)

    def __str__(self):
        return self.name

class Message(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    room = models.ForeignKey(Room, on_delete=models.CASCADE, related_name="messages", blank=True, null=True)
    content = models.TextField()
    timestamp = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ["timestamp"]

    def __str__(self):
        return f"{self.user.username}: {self.content[:20]}"


# Exercise:

class Quiz(models.Model):
    QUIZ_TYPES = [
        ("mcq", "Multiple Choice"),
        ("fill", "Fill in the Blank"),
        ("match", "Matching"),
    ]
    title = models.CharField(max_length=200)
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="quizzes")
    quiz_type = models.CharField(max_length=10, choices=QUIZ_TYPES, default="mcq")
    due_date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.title

class QuizResult(models.Model):
    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name="quizzes")
    quiz = models.ForeignKey(Quiz, on_delete=models.CASCADE, related_name="results")
    score = models.IntegerField()
    total = models.IntegerField()
    taken_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        ordering = ["-taken_at"]

class Question(models.Model):
    QUESTION_TYPES = [
        ("mcq", "Multiple Choice"),
        ("fill", "Fill in the Blank"),
        ("match", "Matching"),
    ]
    quiz = models.ForeignKey(Quiz, related_name="questions", on_delete=models.CASCADE)
    question_text = models.TextField()
    question_type = models.CharField(max_length=10, choices=QUESTION_TYPES)
    
    # NEW field for fill-in-the-blank
    correct_answer = models.CharField(max_length=255, blank=True, null=True)

    def __str__(self):
        return self.question_text


class Choice(models.Model):
    question = models.ForeignKey(Question, on_delete=models.CASCADE, related_name='choices')
    text = models.CharField(max_length=200)
    is_correct = models.BooleanField(default=False)

    def __str__(self):
        return self.text

class MatchingPair(models.Model):
    question = models.ForeignKey(Question, on_delete=models.CASCADE, related_name='pairs')
    left_item = models.CharField(max_length=200)
    right_item = models.CharField(max_length=200)

    def __str__(self):
        return f"{self.left_item} -> {self.right_item}"

# ---------------------
# SPEAKING
# ---------------------
class SpeakingExercise(models.Model):
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="speaking_exercises", null=True, blank=True)
    text = models.CharField(max_length=500)  # prompt sentence to practice
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.text[:50]


class SpeakingResult(models.Model):
    exercise = models.ForeignKey(SpeakingExercise, on_delete=models.CASCADE, related_name="results")
    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name="speaking_attempts")
    audio_file = models.FileField(upload_to="speaking_attempts/", null=True, blank=True)  # optional
    transcript = models.TextField(blank=True, null=True)  # if you store recognized text
    feedback = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.student.username} - {self.exercise.text[:30]}"


# ---------------------
# WRITING
# ---------------------
class WritingExercise(models.Model):
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="writing_exercises", null=True, blank=True)
    title = models.CharField(max_length=200)
    prompt = models.TextField()  # The writing task
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.title


class WritingExerciseResult(models.Model):
    exercise = models.ForeignKey(WritingExercise, on_delete=models.CASCADE, related_name="results")
    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name="writing_attempts")
    submission_text = models.TextField()
    score = models.FloatField(null=True, blank=True)  # Teacher or AI can grade
    feedback = models.TextField(blank=True, null=True)
    submitted_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.student.username} - {self.exercise.title} ({self.score if self.score else 'Pending'})"
