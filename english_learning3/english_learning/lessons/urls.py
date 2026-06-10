from django.urls import path
from . import views


urlpatterns = [
  path("create_live_session/", views.create_live_session, name="create_live_session"), 
  path("live_session_list/", views.live_session_list, name="live_session_list"), 
  
  path("lesson/<int:lesson_id>/delete/", views.delete_lesson, name="delete_lesson"),
  path("lesson_list", views.lesson_list, name="lesson_list"),
  path("lesson_detail<int:lesson_id>/", views.lesson_detail, name="lesson_detail"),
  path("create_lesson/", views.create_lesson, name="create_lesson"),
]
