from django.urls import path
from . import views
from django.contrib.auth import views as auth_views
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('create_user/', views.create_user, name='create_user'),
    path('user_list/', views.user_list, name='user_list'),
    path('delete_user/<int:user_id>/', views.delete_user, name='delete_user'),
    path('edit_user/<int:user_id>/', views.edit_user, name='edit_user'),

    path('login/', views.login, name = 'login'),
    path('dashboard/', views.dashboard, name='dashboard'),
    path('logout/', views.custom_logout, name = 'logout'),

] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
