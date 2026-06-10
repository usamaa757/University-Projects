from django import forms
from .models import Message, Room

class MessageForm(forms.ModelForm):
    class Meta:
        model = Message
        fields = ["content"]  # only text, user/room filled in view


class RoomForm(forms.ModelForm):
    class Meta:
        model = Room
        fields = ["name"]
        widgets = {
            "name": forms.TextInput(attrs={"class": "form-control", "placeholder": "Enter room name"})
        }
