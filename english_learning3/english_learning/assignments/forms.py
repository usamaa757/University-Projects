from django import forms
from .models import Assignment, AssignmentSubmission

class AssignmentForm(forms.ModelForm):
    class Meta:
        model = Assignment
        fields = ['title', 'description', 'file', 'due_date', 'total_marks']
        widgets = {
            'total_marks': forms.NumberInput(attrs={'placeholder': 'Enter Marks'}),
            'due_date': forms.DateInput(attrs={'type': 'date'})
        }

class SubmissionForm(forms.ModelForm):
    class Meta:
        model = AssignmentSubmission
        fields = ['file']