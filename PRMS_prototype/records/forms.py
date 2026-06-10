from .models import Patient
from django import forms

class PatientForm(forms.ModelForm):
    class Meta:
        model = Patient
        fields = ['name', 'age', 'residence', 'disease']
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control'}),
            'age': forms.NumberInput(attrs={'class': 'form-control'}),
            'residence': forms.TextInput(attrs={'class': 'form-control'}),
            'disease': forms.TextInput(attrs={'class': 'form-control'}),
        }
