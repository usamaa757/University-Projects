from django.contrib import admin
from .models import Course, Lesson, Quiz, Question, Choice, QuizSubmission, StudentProgress, ListeningExercise, ListeningSubmission
from accounts.models import User

admin.site.register(Course)
admin.site.register(Lesson)
admin.site.register(Quiz)
admin.site.register(Question)
admin.site.register(Choice)
admin.site.register(User)
admin.site.register(QuizSubmission)
admin.site.register(StudentProgress)
admin.site.register(ListeningExercise)
admin.site.register(ListeningSubmission)

