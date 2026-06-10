from django.urls import path
from . import views

urlpatterns = [
    path('', views.index, name='index'),
    path('register/', views.register, name='register'),
    path('login/', views.login_view, name='login'),
    path('user_dashboard/', views.user_dashboard, name='user_dashboard'),
    path('admin_dashboard/', views.admin_dashboard, name='admin_dashboard'),
    path('approve_user/<int:user_id>/', views.approve_user, name='approve_user'),
    path('admin_login/', views.admin_login, name='admin_login'),
    path('logout/', views.logout_view, name='logout'),
] 
