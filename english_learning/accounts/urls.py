# accounts/urls.py
from django.urls import path
from . import views


app_name = 'accounts'

urlpatterns = [
    path('signup/', views.signup_view, name='signup'),
    path('login/', views.login_view, name='login'),
    path('logout/', views.logout_view, name='logout'),
    
    path("dashboard-redirect/", views.dashboard_redirect, name="dashboard_redirect"),
    
    #admin related urls
    path("admin/dashboard/", views.admin_dashboard, name="admin_dashboard"),
    path("users/", views.user_list, name="user_list"),
    path("users/create/", views.user_create, name="user_create"),
    path("users/<int:user_id>/edit/", views.user_edit, name="user_edit"),
    path("users/<int:user_id>/delete/", views.user_delete, name="user_delete"),
    path("analytics/", views.analytics_view, name="analytics"),
    path("manage-content/", views.manage_content, name="manage_content"),
    
    #teacher related url
    path("teacher/dashboard/", views.teacher_dashboard, name="teacher_dashboard"),
    path("manage-assignments/", views.manage_assignments, name="manage_assignments"),
    
    path("live-sessions/", views.live_sessions, name="live_sessions"),
    path("students/", views.student_list_view, name="student_list"),

    #student related views
    path("student/dashboard/", views.student_dashboard, name="student_dashboard"),
    path("student/lessons/", views.student_lessons, name="student_lessons"),

    path("student/forum/", views.forum_list, name="forum_list"),
    path("student/lessons/<int:lesson_id>/", views.student_lesson_detail, name="student_lesson_detail"),


    




]