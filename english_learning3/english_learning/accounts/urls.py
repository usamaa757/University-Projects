from django.urls import path
from . import views
from django.contrib.auth.views import LogoutView

urlpatterns = [
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
]
