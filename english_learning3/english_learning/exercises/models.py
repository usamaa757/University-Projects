from django.db import models
from lessons.models import Lesson
from accounts.models import User

class Quiz(models.Model):
    QUIZ_TYPES = [
        ("mcq", "Multiple Choice"),
        ("fill", "Fill in the Blank"),
        ("match", "Matching"),
    ]
    title = models.CharField(max_length=200)
    lesson = models.ForeignKey(Lesson, on_delete=models.CASCADE, related_name="quizzes")
    quiz_type = models.CharField(max_length=10, choices=QUIZ_TYPES, default="mcq")
    created_at = models.DateTimeField(auto_now_add=True)
    due_date = models.DateField(null=True, blank=True)

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
