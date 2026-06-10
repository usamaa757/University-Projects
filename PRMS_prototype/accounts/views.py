from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from .forms import UserRegistrationForm, EditUserForm
from .models import CustomUser
from django.contrib.auth.forms import AuthenticationForm
from django.contrib import messages
from django.contrib.auth import login as auth_login, logout

def index(request):
    return render(request, "index.html")

def login(request):
    if request.method == "POST":
        form = AuthenticationForm(request, data=request.POST)
        if form.is_valid():
            user = form.get_user()
            auth_login(request, user)
            return redirect('dashboard')  
        else:
            messages.error(request, "Invalid username or password.")

    else:
        form = AuthenticationForm()
    
    return render(request, 'login.html', {'form': form})


@login_required
def dashboard(request):
    return render(request, 'dashboard.html', {'user': request.user})


def custom_logout(request):
    logout(request)
    return redirect('login')


@login_required
def create_user(request):
    if request.method == "POST":
        form = UserRegistrationForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "User created successfully!")
            return redirect('create_user')  
        else:
            messages.error(request, "Error creating user. Please check the details.")
    else:
        form = UserRegistrationForm()
    
    return render(request, 'create_user.html', {'form': form})

@login_required
def user_list(request):
    users = CustomUser.objects.filter(role='operator')
    return render(request, 'user_list.html', {'users': users})

@login_required
def edit_user(request, user_id):
    user = get_object_or_404(CustomUser, id=user_id) 
    
    if request.method == "POST":
        form = EditUserForm(request.POST, instance=user)
        if form.is_valid():
            form.save()
            messages.success(request, "User details updated successfully.")
            return redirect("user_list")
        else:
            messages.error(request, "Error updating user details. Please check the form.")
    else:
        form = EditUserForm(instance=user)  

    return render(request, "edit_user.html", {"form": form, "user": user})


@login_required
def delete_user(request, user_id):
    user = CustomUser.objects.get(id=user_id)
    user.delete()
    messages.success(request, 'User deleted successfully')
    return redirect('user_list')
