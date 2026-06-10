from django import forms

from .models import Job


class JobForm(forms.ModelForm):
    class Meta:
        model = Job
        fields = [
            "title",
            "company",
            "description",
            "category",
            "location",
            "salary_min",
            "salary_max",
            "job_type",
            "status",
        ]
