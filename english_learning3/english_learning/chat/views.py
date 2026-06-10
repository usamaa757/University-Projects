from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from .models import Room, Message   
from .forms import MessageForm, RoomForm
from django.contrib import messages
@login_required
def create_room(request):
    if request.method == "POST":
        form = RoomForm(request.POST)
        if form.is_valid():
            room = form.save()
            messages.success(request, f'Room "{room.name}" created successfully.')
            return redirect("chat_room", room_name=room.name)  # redirect to new room
    else:
        form = RoomForm()
    return render(request, "create_room.html", {"form": form})

@login_required
def room_list(request):
    rooms = Room.objects.all()
    return render(request, "room_list.html", {"rooms": rooms})

@login_required
def chat_room(request, room_name):
    room = get_object_or_404(Room, name=room_name)
    messages = room.messages.all()
    rooms = Room.objects.all()  # 👈 get all rooms for sidebar

    if request.method == "POST":
        form = MessageForm(request.POST)
        if form.is_valid():
            msg = form.save(commit=False)
            msg.user = request.user
            msg.room = room
            msg.save()
            return redirect("chat_room", room_name=room.name)
    else:
        form = MessageForm()

    return render(request, "chat.html", {
        "room": room,
        "rooms": rooms,         
        "room_name": room.name,  
        "messages": messages,
        "form": form
    })
