# courses/models.py
from django.db import models
from django.conf import settings
from django.utils import timezone
from datetime import date

User = settings.AUTH_USER_MODEL

class Course(models.Model):
    SKILL_LEVELS = [
        ("BEGINNER", "Beginner"),
        ("INTERMEDIATE", "Intermediate"),
        ("ADVANCED", "Advanced"),
    ]

    CATEGORIES = [
        ("GRAMMAR", "Grammar"),
        ("VOCABULARY", "Vocabulary"),
        ("LISTENING", "Listening"),
        ("SPEAKING", "Speaking"),
    ]

    title = models.CharField(max_length=200)
    description = models.TextField()
    skill_level = models.CharField(max_length=20, choices=SKILL_LEVELS)
    category = models.CharField(max_length=20, choices=CATEGORIES)
    created_by = models.ForeignKey(User, on_delete=models.CASCADE, related_name="courses")
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.title



class Lesson(models.Model):
    course = models.ForeignKey(Course, on_delete=models.CASCADE, related_name="lessons")
    title = models.CharField(max_length=200)
    content = models.TextField(blank=True, null=True)

    # Multimedia options
    video_url = models.URLField(blank=True, null=True, help_text="YouTube/Vimeo or hosted video URL")
    audio_url = models.URLField(blank=True, null=True, help_text="Audio file or podcast URL")
    image = models.ImageField(upload_to="lesson_images/", blank=True, null=True)
    flashcards = models.JSONField(blank=True, null=True, help_text="Store flashcards as JSON [{front:'word', back:'meaning'}]")

    order = models.PositiveIntegerField(default=0)

    def __str__(self):
        return f"{self.course.title} - {self.title}"
    def save(self, *args, **kwargs):
        if not self.order:  # only set automatically if order is not manually set
            last_lesson = Lesson.objects.filter(course=self.course).order_by('-order').first()
            self.order = (last_lesson.order + 1) if last_lesson else 1
        super().save(*args, **kwargs)


class Exercise(models.Model):
    EXERCISE_TYPES = [
        ('QUIZ', 'Quiz'),
        ('SPEAK', 'Speaking'),
        ('WRITE', 'Writing'),
        ('LISTEN', 'Listening'),
    ]
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="exercises")
    title = models.CharField(max_length=200)
    exercise_type = models.CharField(max_length=10, choices=EXERCISE_TYPES)

    class Meta:
        abstract = True
    
class Quiz(models.Model):
    lesson = models.ForeignKey('Lesson', on_delete=models.CASCADE, related_name='quizzes')
    title = models.CharField(max_length=200)
    description = models.TextField(blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    due_date = models.DateField(null=True, blank=True)

    def __str__(self):
        return f"{self.lesson.title} - {self.title}"


class Question(models.Model):
    QUESTION_TYPES = [
        ('MCQ', 'Multiple Choice'),
        ('FILL', 'Fill in the Blank'),
    ]

    quiz = models.ForeignKey(Quiz, on_delete=models.CASCADE, related_name='questions')
    text = models.TextField()
    question_type = models.CharField(max_length=10, choices=QUESTION_TYPES)
    correct_answer = models.CharField(max_length=255, blank=True, null=True)  # for Fill-in-the-Blank

    def __str__(self):
        return f"{self.quiz.title} - {self.text[:50]}"


class Choice(models.Model):
    question = models.ForeignKey(Question, on_delete=models.CASCADE, related_name='choices')
    text = models.CharField(max_length=255)
    is_correct = models.BooleanField(default=False)

    def __str__(self):
        return self.text
    
class QuizSubmission(models.Model):
    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name="quiz_submissions")
    quiz = models.ForeignKey('Quiz', on_delete=models.CASCADE, related_name='submissions')
    answers = models.JSONField()  # {question_id: choice_id/text}
    score = models.PositiveIntegerField(default=0)
    submitted_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        unique_together = ('student', 'quiz')  # prevent multiple submissions

    def __str__(self):
        return f"{self.student} - {self.quiz.title}"
    
class SpeakingExercise(models.Model):
    lesson = models.ForeignKey('Lesson', on_delete=models.CASCADE, related_name='speaking_exercises')
    prompt = models.TextField()  # e.g., "Introduce yourself"
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.lesson.title} - Speaking Exercise"
    
class SpeakingSubmission(models.Model):
    exercise = models.ForeignKey(SpeakingExercise, on_delete=models.CASCADE, related_name='submissions')
    student = models.ForeignKey(User, on_delete=models.CASCADE)
    audio_file = models.FileField(upload_to='speaking_submissions/')
    score = models.FloatField(null=True, blank=True)
    feedback = models.TextField(blank=True, null=True)
    submitted_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        unique_together = ('exercise', 'student')

    def __str__(self):
        return f"{self.student} - {self.exercise}"

 
class WritingExercise(models.Model):
    lesson = models.ForeignKey('Lesson', on_delete=models.CASCADE, related_name='writing_exercises')
    prompt = models.TextField()  # e.g., "Write about your favorite hobby"
    max_words = models.PositiveIntegerField(default=500)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.lesson.title} - Writing Exercise"


class WritingSubmission(models.Model):
    exercise = models.ForeignKey(WritingExercise, on_delete=models.CASCADE, related_name='submissions')
    student = models.ForeignKey(User, on_delete=models.CASCADE)
    content = models.TextField()
    score = models.FloatField(null=True, blank=True)  # auto-scored
    feedback = models.TextField(blank=True, null=True)
    submitted_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.student} - {self.exercise}"
    
class ListeningExercise(models.Model):
    lesson = models.ForeignKey('Lesson', on_delete=models.CASCADE, related_name='listening_exercises')    
    title = models.CharField(max_length=200)
    audio = models.FileField(upload_to='audio/', blank=True, null=True)
    video = models.FileField(upload_to='video/', blank=True, null=True)
    question = models.TextField(blank=True, null=True)
    due_date = models.DateField(null=True, blank=True)
    correct_answer = models.TextField(null=True, blank=True) 


    def __str__(self):
        return f"{self.lesson.title} - {self.title}"


class ListeningSubmission(models.Model):
    exercise = models.ForeignKey(ListeningExercise, on_delete=models.CASCADE, related_name="submissions")
    student = models.ForeignKey(User, on_delete=models.CASCADE, null=True, blank=True)
    answer = models.TextField(null=True, blank=True) 
    submitted_at = models.DateTimeField(auto_now_add=True)
    score = models.FloatField(null=True, blank=True)



    def __str__(self):
        return f"{self.exercise.title} - {self.answer[:50]}"

class Assignment(models.Model):
    lesson = models.ForeignKey('Lesson', on_delete=models.CASCADE, related_name='assignments')
    title = models.CharField(max_length=200)
    description = models.TextField(null=True, blank=True)
    file = models.FileField(upload_to='assignments', blank=True, null=True)
    total_marks = models.PositiveIntegerField(default=20)
    due_date = models.DateField()
    created_at = models.DateTimeField(auto_now_add=True)
    created_by = models.ForeignKey(User, on_delete=models.CASCADE, related_name='created_assignments')

    def __str__(self):
        return f"{self.lesson.title} - {self.title}"

    @property
    def is_past_due(self):
        return timezone.now() > self.due_date


class AssignmentSubmission(models.Model):
    assignment = models.ForeignKey(Assignment, on_delete=models.CASCADE, related_name='submissions')
    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name='assignment_submissions')
    submitted_file = models.FileField(upload_to='assignments/')
    submitted_at = models.DateTimeField(auto_now_add=True)
    marks_awarded = models.PositiveIntegerField(blank=True, null=True)
    feedback = models.TextField(blank=True, null=True)

    class Meta:
        unique_together = ('assignment', 'student')  # One submission per student

    def __str__(self):
        return f"{self.assignment.title} - {self.student.username}"

class StudentProgress(models.Model):
    STATUS = [
        ("PENDING", "Pending"),
        ("COMPLETED", "Completed"),
    ]

    student = models.ForeignKey(User, on_delete=models.CASCADE, related_name="progress")
    course = models.ForeignKey(Course, on_delete=models.CASCADE)
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE)
    exercise_type = models.CharField(max_length=20)  # QUIZ, SPEAK, WRITE, LISTEN
    score = models.FloatField(null=True, blank=True)
    status = models.CharField(max_length=20, choices=STATUS, default="PENDING")
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        unique_together = ('student', 'lesson', 'exercise_type')

    def __str__(self):
        return f"{self.student} - {self.lesson} - {self.exercise_type}"
class LiveSession(models.Model):
    title = models.CharField(max_length=200)
    url = models.URLField(max_length=500)
    host = models.ForeignKey(User, on_delete=models.CASCADE, related_name='hosted_sessions')
    scheduled_at = models.DateTimeField(null=True, blank=True)  # optional: schedule date/time
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        ordering = ['-scheduled_at', '-created_at']

    def __str__(self):
        return f"{self.title} - hosted by {self.host.username}"

class ForumTopic(models.Model):
    title = models.CharField(max_length=255)
    description = models.TextField(blank=True, null=True)
    created_by = models.ForeignKey(User, on_delete=models.CASCADE, related_name='topics')
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return self.title


class ForumPost(models.Model):
    topic = models.ForeignKey(ForumTopic, on_delete=models.CASCADE, related_name='posts')
    author = models.ForeignKey(User, on_delete=models.CASCADE, related_name='posts')
    content = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)
    parent = models.ForeignKey('self', null=True, blank=True, on_delete=models.CASCADE, related_name='replies')

    def __str__(self):
        return f"{self.author.username}: {self.content[:30]}"

class Badge(models.Model):
    name = models.CharField(max_length=50)
    description = models.TextField()
    icon = models.ImageField(upload_to='badges/', null=True, blank=True)

    def __str__(self):
        return self.name

class UserBadge(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='badges')
    badge = models.ForeignKey(Badge, on_delete=models.CASCADE)
    awarded_at = models.DateTimeField(auto_now_add=True)

class UserStreak(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE, related_name='streaks')
    last_active = models.DateField(default=date.today)
    current_streak = models.PositiveIntegerField(default=0)
    max_streak = models.PositiveIntegerField(default=0)

class UserPoints(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE, related_name="points")
    total_points = models.PositiveIntegerField(default=0)

    def __str__(self):
        return f"{self.user} - {self.total_points} points"