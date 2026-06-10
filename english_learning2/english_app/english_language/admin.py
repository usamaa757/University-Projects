from django.contrib import admin
from .models import (
    User,
    Lesson, LessonMedia, LiveSession,
    Assignment, AssignmentSubmission,
    Room, Message,
    Quiz, Question, Choice, MatchingPair,
    SpeakingExercise, WritingExercise,
    QuizResult, SpeakingResult, WritingExerciseResult
)

# Register your models here.
admin.site.register(User)
admin.site.register(Lesson)
admin.site.register(LessonMedia)
admin.site.register(LiveSession)
admin.site.register(Assignment)
admin.site.register(AssignmentSubmission)
admin.site.register(Room)
admin.site.register(Message)
admin.site.register(Quiz)
admin.site.register(Question)
admin.site.register(Choice)
admin.site.register(MatchingPair)
admin.site.register(SpeakingExercise)
admin.site.register(WritingExercise)
admin.site.register(QuizResult)
admin.site.register(SpeakingResult)
admin.site.register(WritingExerciseResult)
