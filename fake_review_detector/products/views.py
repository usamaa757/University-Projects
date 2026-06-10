
from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.contrib.auth.decorators import login_required, user_passes_test
from reviews.forms import ReviewForm
from reviews.models import Review
from .forms import ProductForm
from .models import Product
# Create your views here.

def is_superuser(user):
    return user.is_authenticated and user.is_superuser

def is_user(user):
    return user.is_authenticated and user.is_user

# Add Product
@login_required
@user_passes_test(is_superuser)
def add_product(request):
    if request.method == 'POST':
        form = ProductForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, 'Product added successfully.')
            return redirect('product_list')
    else:
        form = ProductForm()
    return render(request, 'add_product.html', {'form': form})

# Product List
@login_required
def product_list(request):
    products = Product.objects.all()
    return render(request, 'product_list.html', {'products': products})

# Edit Product
@login_required
@user_passes_test(is_superuser)
def edit_product(request, pk):
    product = get_object_or_404(Product, pk=pk)
    if request.method == 'POST':
        form = ProductForm(request.POST, instance=product)
        if form.is_valid():
            form.save()
            messages.success(request, 'Product updated successfully.')
            return redirect('product_list')
    else:
        form = ProductForm(instance=product)
    return render(request, 'edit_product.html', {'form': form})

# Delete Product
@login_required
@user_passes_test(is_superuser)
def delete_product(request, pk):
    product = get_object_or_404(Product, pk=pk)
    product.delete()
    return redirect('product_list')
