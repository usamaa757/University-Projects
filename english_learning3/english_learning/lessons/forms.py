# forms.py
from django import forms
from .models import LiveSession, LessonMedia, Lesson



class LessonForm(forms.ModelForm):
    class Meta:
        model = Lesson
        fields = ["title", "description", "level", "category"]


class LessonMediaForm(forms.ModelForm):
    class Meta:
        model = LessonMedia
        fields = ["media_type", "file", "flashcard_front", "flashcard_back"]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)

        # Add some styling or placeholders
        self.fields['flashcard_front'].widget.attrs.update({
            "placeholder": "Flashcard Front (e.g. Question / Word)"
        })
        self.fields['flashcard_back'].widget.attrs.update({
            "placeholder": "Flashcard Back (e.g. Answer / Definition)"
        })

class LiveSessionForm(forms.ModelForm):
    class Meta:
        model = LiveSession
        fields = ['title', 'description', 'date_time', 'meeting_link']
        widgets = {
            'date_time': forms.DateTimeInput(attrs={'type': 'datetime-local'})
        }
