from django.contrib import admin

# Register your models here.
from accounts.models import User
from assignments.models import Assignment, AssignmentSubmission
from exercises.models import Quiz, QuizResult, Question, Choice, MatchingPair, SpeakingExercise, WritingExerciseResult, SpeakingResult 
from lessons.models import Lesson, LessonMedia, LiveSession

admin.site.register(User)
admin.site.register(Assignment)
admin.site.register(AssignmentSubmission)
admin.site.register(Quiz)
admin.site.register(Question)
admin.site.register(MatchingPair)
admin.site.register(Choice)
admin.site.register(QuizResult)
admin.site.register(SpeakingExercise)
admin.site.register(WritingExerciseResult)
admin.site.register(SpeakingResult)
