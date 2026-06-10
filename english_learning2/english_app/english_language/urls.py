from django.urls import path
from . import views
from django.contrib.auth.views import LogoutView


urlpatterns = [
    path('', views.index, name='index'),

  # Account:
    
    path('register/', views.register, name='register'),
    path('login/', views.CustomLoginView.as_view(), name='login'),
    path('logout/', LogoutView.as_view(next_page='login'), name='logout'),  # Logout here
    path('admin_dashboard/', views.admin_dashboard, name='admin_dashboard'),
    path('teacher_dashboard/', views.teacher_dashboard, name='teacher_dashboard'),
    path('student_dashboard/', views.student_dashboard, name='student_dashboard'),
    path("progress_report/", views.progress_report, name="progress_report"),
    path("progress_report/<int:student_id>/", views.progress_report, name="progress_report_by_id"),
    path('student_list/', views.student_list, name='student_list'),
    
    path('user_list/', views.user_list, name='user_list'),
    path('user_detail/<int:user_id>/', views.user_detail, name='user_detail'),
    path('user_edit/<int:user_id>/edit/', views.user_edit, name='user_edit'),
    path('user_delete/<int:user_id>/delete/', views.user_delete, name='user_delete'),
  # Lessons:

    path("create_live_session/", views.create_live_session, name="create_live_session"), 
    path("live_sessions_list/", views.live_sessions_list, name="live_sessions_list"), 
    path("edit_session/<int:pk>/edit/", views.edit_session, name="edit_session"),
    path("delete_session/<int:pk>/delete/", views.delete_session, name="delete_session"),
    
    path("lessons/<int:lesson_id>/delete/", views.delete_lesson, name="delete_lesson"),
    path("lesson_list/", views.lesson_list, name="lesson_list"),
    path("lesson_detail/<int:lesson_id>/", views.lesson_detail, name="lesson_detail"),
    path("create_lesson/", views.create_lesson, name="create_lesson"),
  
  #  Exercise:
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
    path("quiz/<int:quiz_id>/delete_quiz/", views.delete_quiz, name="delete_quiz"),
    path("quiz_result_detail/<int:quiz_id>/", views.quiz_result_detail, name="quiz_result_detail"),
    
  # Chat:
    
    path("create_room/", views.create_room, name="create_room"),
    path("room_list/", views.room_list, name="room_list"),
    path("chat/<str:room_name>/", views.chat_room, name="chat_room"),
    
  # Assignment:
    
    path('create_assignment/', views.create_assignment, name='create_assignment'),
    path('edit_assigment/<int:pk>/', views.edit_assigment, name='edit_assigment'),
    path('student_assignments/', views.student_assignments, name='student_assignments'),
    path('assignment_list/', views.assignment_list, name='assignment_list'),
    path('submit_assignment/<int:assignment_id>/', views.submit_assignment, name='submit_assignment'),
    path('view_submissions/<int:assignment_id>/', views.view_submissions, name='view_submissions'),
    

    
]
