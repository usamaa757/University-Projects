# urls.py
from django.urls import path
from . import views

urlpatterns = [
    # path("create_exercise/", views.create_exercise, name="create_exercise"),
    path('quiz_list/', views.quiz_list, name='quiz_list'),
    path("quiz/<int:quiz_id>/", views.take_quiz, name="take_quiz"),
    path("quiz_results_list/", views.quiz_results_list, name="quiz_results_list"),  # Teacher
    path("my_quiz_results/", views.my_quiz_results, name="my_quiz_results"),   # Student
    path('create_quiz/', views.create_quiz, name='create_quiz'),
    path('add_questions/<int:quiz_id>/', views.add_questions, name='add_questions'),
    path('take_quiz/<int:quiz_id>/', views.take_quiz, name='take_quiz'),
    path("writing_practice/", views.writing_practice, name="writing_practice"),
    path("create_speaking_vocabulary/", views.create_speaking_vocabulary, name="create_speaking_vocabulary"),
    path("practice_speaking/<int:pk>/", views.practice_speaking, name="practice_speaking"),
    path("speaking_exercise_list/", views.speaking_exercise_list, name="speaking_exercise_list"),
    path('delete_speaking_exercise/<int:ex_id>/delete/', views.delete_speaking_exercise, name='delete_speaking_exercise'),
    path("quiz/<int:quiz_id>/delete_quiz/", views.delete_quiz, name="delete_quiz"),
    path("quiz_result_detail/<int:quiz_id>/", views.quiz_result_detail, name="quiz_result_detail"),

]
