from django import forms
from .models import (
        Course, Lesson, Quiz, Question, 
    SpeakingExercise, Choice,
    WritingExercise,
    ListeningExercise, AssignmentSubmission, Assignment, LiveSession, ForumPost, ForumTopic
)

# ---------------- COURSES ----------------
class CourseForm(forms.ModelForm):
    class Meta:
        model = Course
        fields = ['title', 'description', 'skill_level', 'category']


# ---------------- LESSONS ----------------
class LessonForm(forms.ModelForm):
    class Meta:
        model = Lesson
        fields = [
            "title", "content",
            "video_url", "audio_url",
            "image", "flashcards",
            "order",
        ]

# ----------------------------------------
# Quiz and Questions Forms
# ----------------------------------------

class QuizForm(forms.ModelForm):
    class Meta:
        model = Quiz
        fields = ['title', 'description', 'due_date']
        widgets = {
            'due_date': forms.DateTimeInput(attrs={'type': 'date'}),
        }
class QuestionForm(forms.ModelForm):
    class Meta:
        model = Question
        fields = ['text', 'question_type', 'correct_answer']
        
# A separate formset will handle Choices
ChoiceFormSet = forms.inlineformset_factory(
    Question,
    Choice,
    fields=('text', 'is_correct'),
    extra=4,  # default 4 choices for MCQ
    can_delete=True
)
class SpeakingExerciseForm(forms.ModelForm):
    class Meta:
        model = SpeakingExercise
        fields = ['prompt']

class WritingExerciseForm(forms.ModelForm):
    class Meta:
        model = WritingExercise
        fields = ['prompt', 'max_words']

class ListeningExerciseForm(forms.ModelForm):
    class Meta:
        model = ListeningExercise
        fields = ['title', 'audio', 'video', 'question', 'due_date', 'correct_answer']
        widgets = {
            'due_date': forms.DateTimeInput(attrs={'type': 'date'}),
        }
class AssignmentSubmissionForm(forms.ModelForm):
    class Meta:
        model = AssignmentSubmission
        fields = ['submitted_file']
        widgets = {
            'submitted_file': forms.ClearableFileInput(attrs={'class': 'form-control'}),
        }
class AssignmentForm(forms.ModelForm):
    class Meta:
        model = Assignment
        fields = ['title', 'description', 'file', 'total_marks', 'due_date']

        widgets = {
            'due_date': forms.DateTimeInput(attrs={'type': 'date'}),
        }
class AssignmentGradeForm(forms.ModelForm):
    class Meta:
        model = AssignmentSubmission
        fields = ['marks_awarded', 'feedback']
        widgets = {
            'marks_awarded': forms.NumberInput(attrs={'class': 'form-control', 'min': 0}),
            'feedback': forms.Textarea(attrs={'class': 'form-control', 'rows': 3}),
        }

class LiveSessionForm(forms.ModelForm):
    class Meta:
        model = LiveSession
        fields = ['title', 'url', 'scheduled_at']
        widgets = {
            'scheduled_at': forms.DateTimeInput(attrs={'type': 'datetime-local', 'class': 'form-input'}),
            'title': forms.TextInput(attrs={'class': 'form-input', 'placeholder': 'Session Title'}),
            'url': forms.URLInput(attrs={'class': 'form-input', 'placeholder': 'Session URL'}),
        }

class ForumTopicForm(forms.ModelForm):
    class Meta:
        model = ForumTopic
        fields = ['title', 'description']

class ForumPostForm(forms.ModelForm):
    class Meta:
        model = ForumPost
        fields = ['content']
        widgets = {
            'content': forms.Textarea(attrs={'rows': 3, 'placeholder': 'Write your comment...'})
        }