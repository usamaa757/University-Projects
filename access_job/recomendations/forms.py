from django import forms

from .models import Recommendation


class RecommendationForm(forms.ModelForm):
    class Meta:
        model = Recommendation
        fields = ["model_type", "score", "reason"]
