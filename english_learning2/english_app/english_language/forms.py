# forms.py
from django import forms
from .models import User, LiveSession, LessonMedia, Lesson, Quiz, Question, Choice, MatchingPair, SpeakingExercise, Message, Room, Assignment, AssignmentSubmission
from django.forms import modelformset_factory
from django.contrib.auth.forms import UserCreationForm

# User:
class UserRegisterForm(UserCreationForm):
    email = forms.EmailField()

    class Meta:
        model = User
        fields = ['username', 'email', 'role', 'password1', 'password2']
        widgets = {
            'role': forms.Select(attrs={'class': 'form-control role-select'}),
        }

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # Exclude 'admin' from dropdown
        self.fields['role'].choices = [
            (value, label) for value, label in User.ROLE_CHOICES if value != 'admin'
        ]


class UserEditForm(forms.ModelForm):
    class Meta:
        model = User
        fields = ['username', 'email', 'first_name', 'last_name', 'role', 'is_active']
        widgets = {
            'role': forms.Select(choices=User.ROLE_CHOICES),
        }
    
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        for field in self.fields:
            self.fields[field].widget.attrs.update({'class': 'form-control'})
# Lesson:

class LessonForm(forms.ModelForm):
    class Meta:
        model = Lesson
        fields = ["title", "description", "level", "category"]
        widgets = {
            'level': forms.Select(attrs={'class': 'form-control'}),
            'category': forms.Select(attrs={'class': 'form-control'}),
        }


class LessonMediaForm(forms.ModelForm):
    class Meta:
        model = LessonMedia
        fields = ["media_type", "file", "link", "front_text", "back_text"]
        widgets = {
            'media_type': forms.Select(attrs={'class': 'form-control'}),
            'file': forms.ClearableFileInput(attrs={'class': 'form-control'}),
            'link': forms.URLInput(attrs={'class': 'form-control'}),
            'front_text': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
            'back_text': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
        }
        

class LiveSessionForm(forms.ModelForm):
    class Meta:
        model = LiveSession
        fields = ['title', 'description', 'date_time', 'meeting_link']
        widgets = {
            'date_time': forms.DateTimeInput(attrs={'type': 'datetime-local', 'class': 'form-control'}),
            'meeting_link': forms.URLInput(attrs={'class': 'form-control'}),
        }

# Exercise:

class QuizForm(forms.ModelForm):
    class Meta:
        model = Quiz
        fields = ['title', 'lesson', 'quiz_type', 'due_date']
        widgets = {
            'lesson': forms.Select(attrs={'class': 'form-control'}),
            'quiz_type': forms.Select(attrs={'class': 'form-control'}),
            'due_date': forms.DateInput(attrs={'type': 'date', 'class': 'form-control'}),
        }

class QuestionForm(forms.ModelForm):
    class Meta:
        model = Question
        fields = ['question_text', 'correct_answer']
        widgets = {
            'question_text': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
            'correct_answer': forms.TextInput(attrs={'class': 'form-control'}),
        }

class ChoiceForm(forms.ModelForm):
    class Meta:
        model = Choice
        fields = ['text', 'is_correct']
        widgets = {
            'text': forms.TextInput(attrs={'placeholder': 'Enter option text'}),
        }

# Custom formset to enforce radio buttons
ChoiceFormSet = forms.modelformset_factory(
    Choice,
    form=ChoiceForm,
    extra=4,
    can_delete=True
)

class MatchingPairForm(forms.ModelForm):
    class Meta:
        model = MatchingPair
        fields = ['left_item', 'right_item']
        widgets = {
            'left_item': forms.TextInput(attrs={'placeholder': 'Left item', 'class': 'form-control'}),
            'right_item': forms.TextInput(attrs={'placeholder': 'Right item', 'class': 'form-control'}),
        }

MatchingPairFormSet = modelformset_factory(
    MatchingPair,
    form=MatchingPairForm,
    extra=3,     # Show 3 blank pairs by default
    can_delete=True
)

class SpeakingExerciseForm(forms.ModelForm):
    class Meta:
        model = SpeakingExercise
        fields = ["text"]

class WritingExerciseForm(forms.Form):
    lesson = forms.ModelChoiceField(
        queryset=Lesson.objects.all(),
        widget=forms.Select(attrs={'class': 'form-control'}),
        empty_label="-- Select lesson level & category --",
        label="Lesson"
    )

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # Customize the label for each option
        self.fields['lesson'].label_from_instance = (
            lambda obj: f"{obj.get_level_display()} - {obj.get_category_display()}"
        )
# Chat:

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

# Assignment:


class AssignmentForm(forms.ModelForm):
    class Meta:
        model = Assignment
        fields = ['title', 'description', 'file', 'due_date', 'total_marks']
        widgets = {
            'due_date': forms.DateInput(attrs={'type': 'date'})
        }
class SubmissionForm(forms.ModelForm):
    class Meta:
        model = AssignmentSubmission
        fields = ['file']