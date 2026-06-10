from django import forms
from .models import Product

class ProductForm(forms.ModelForm):
    class Meta:
        model = Product
        fields = ['name', 'description', 'price']
        widgets = {
            'name': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Product name'}),
            'description': forms.Textarea(attrs={'class': 'form-control', 'placeholder': 'Product description'}),
            'price': forms.NumberInput(attrs={'class': 'form-control', 'placeholder': 'Product price'}),
        }

