# forms.py
from django import forms
from .models import Quiz, Question, Choice, MatchingPair, SpeakingExercise
from django.forms import modelformset_factory

class QuizForm(forms.ModelForm):
    class Meta:
        model = Quiz
        fields = ['title', 'lesson', 'quiz_type', 'due_date']
        widgets = {
            'due_date': forms.DateInput(
                attrs={'type': 'date', 'class': 'date-input'}
            ),
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