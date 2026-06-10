from django.urls import path
from . import views

urlpatterns = [
    path('create_assignment/', views.create_assignment, name='create_assignment'),
    path('edit_assignment/<int:pk>/', views.edit_assignment, name='edit_assignment'),
    path('assignment_list/', views.assignment_list, name='assignment_list'),
    path('submit_assignment/<int:assignment_id>/', views.submit_assignment, name='submit_assignment'),
    path('view_submissions/<int:assignment_id>/', views.view_submissions, name='view_submissions'),

]
