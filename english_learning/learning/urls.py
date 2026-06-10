from django.urls import path
from . import views

app_name = 'learning'

urlpatterns = [
    # ---------------- COURSES ----------------
    path('courses/', views.course_list, name='course_list'),
    path('courses/create/', views.create_course, name='create_course'),
    path('courses/<int:pk>/edit/', views.edit_course, name='course_edit'),
    path('courses/<int:pk>/delete/', views.delete_course, name='course_delete'),

    # ---------------- LESSONS ----------------
    path('<int:course_id>/lessons/', views.lesson_list, name='lesson_list'),
    path('<int:course_id>/lessons/create/', views.lesson_create, name='lesson_create'),
    path('<int:course_id>/lessons/<int:lesson_id>/edit/', views.lesson_edit, name='lesson_edit'),
    path('<int:course_id>/lessons/<int:lesson_id>/delete/', views.lesson_delete, name='lesson_delete'),

    # ---------------- EXERCISES ----------------
    path('lessons/<int:lesson_id>/exercises/', views.exercise_list, name='exercise_list'),
    path('lessons/<int:lesson_id>/exercises/create/', views.exercise_create, name='exercise_create'),
    path('lessons/<int:lesson_id>/exercises/<str:exercise_type>/<int:exercise_id>/', views.exercise_detail, name='exercise_detail'),
    path('lessons/<int:lesson_id>/exercises/<str:exercise_type>/<int:exercise_id>/edit/', views.exercise_edit, name='exercise_edit'),
    path('lessons/<int:lesson_id>/exercises/<str:exercise_type>/<int:exercise_id>/delete/', views.exercise_delete, name='exercise_delete'),
    path('lesson/<int:lesson_id>/quiz/<int:quiz_id>/add-question/', views.add_question, name='add_question'),
    path('learning/lessons/<int:lesson_id>/exercises/<str:exercise_type>/<int:exercise_id>/result/', views.quiz_result, name='quiz_result'),
    path('quiz/<int:quiz_id>/result/', views.quiz_result, name='quiz_result'),
    path('exercise/writing/<int:lesson_id>/<int:exercise_id>/', views.writing_exercise_detail, name='writing_exercise_detail'),
    path('leaderboard/', views.leaderboard, name='leaderboard'),

    # ---------------- ASSIGNMENTS ----------------
    path('lessons/<int:lesson_id>/assignment_list/', views.assignment_list, name='assignment_list'),
    path('lessons/<int:lesson_id>/assignments/create/', views.create_assignment, name='create_assignment'),
    path('assignments/<int:assignment_id>/', views.assignment_detail, name='assignment_detail'),

    # ---------------- LIVE SESSIONS ----------------
    path('live-sessions/', views.live_sessions, name='live_sessions'),
    path('live-sessions/create/', views.create_live_session, name='create_live_session'),

    # ---------------- STUDENT PROGRESS ----------------
    path('progress/', views.student_progress, name='student_progress'),
    path('progress/<int:student_id>/', views.student_progress, name='student_detail_progress'),

    # ---------------- CERTIFICATES ----------------
    path('certificate/<int:course_id>/pdf/', views.generate_certificate_pdf, name='generate_certificate_pdf'),
    
    # ---------------- FORUMS ----------------
    path('forum/', views.forum_list, name='forum_list'),
    path('forum/create/', views.forum_create_topic, name='forum_create'),
    path('forum/<int:topic_id>/', views.forum_topic_detail, name='forum_topic_detail'),
]
