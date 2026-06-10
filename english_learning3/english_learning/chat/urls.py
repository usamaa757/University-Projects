from django.urls import path
from . import views

urlpatterns = [
    path("create_room/", views.create_room, name="create_room"),
    path("room_list/", views.room_list, name="room_list"),
    path("chat/<str:room_name>/", views.chat_room, name="chat_room"),
]