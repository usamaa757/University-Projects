from user.forms import LoginForm, RegistrationForm, CustomUserForm
from book.models import Book, Notification, Review
from user.models import CustomUser, PasswordResetToken
from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, get_user_model, logout
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse, JsonResponse
from django.db.models import Q, Max
from django.utils.crypto import get_random_string
from django.core.mail import send_mail
from django.conf import settings


def register(request):
    if request.method == 'POST':
        form = RegistrationForm(request.POST)
        if form.is_valid():
            user = form.save(commit=False)
            user.set_password(form.cleaned_data['password'])
            user.save()
            
            messages.success(request, 'Account created successfully!')
            
            return redirect('login')
        else:
            return render(request, 'register.html', {'form': form})

    else:
        form = RegistrationForm()

    return render(request, 'register.html', {'form': form})

# write a function for index which loads all the books available in the database and show them in the index.html page
def index(request):
    books = Book.objects.all()
    return render(request, 'index.html', {'books': books})

def login_view(request):
    if request.method == 'POST':
        form = LoginForm(request=request, data=request.POST) 
        if form.is_valid():
            user = authenticate(request, username=form.cleaned_data['username'], password=form.cleaned_data['password'])
            
            if user is not None:
                login(request, user)
                messages.success(request, 'Login successful!')
                return redirect('dashboard')  
            else:
                messages.error(request, 'Invalid username or password.')

    else:
        form = LoginForm()

    return render(request, 'login.html', {'form': form})


@login_required
def dashboard(request):
    notifications = Notification.objects.filter(user=request.user, is_read=False).order_by('-created_at')
    total_users = CustomUser.objects.filter(is_superuser=False, is_staff=False).count()
    total_books = Book.objects.count()
    total_reviews = Review.objects.count()

    return render(request, 'dashboard.html', {
        'notifications': notifications,
        'total_users': total_users,
        'total_books': total_books,
        'total_reviews': total_reviews, 
    })
    
@login_required
def user_profile(request):
    if request.method == 'POST':
        form = CustomUserForm(
            request.POST, 
            request.FILES, 
            instance=request.user
        )
        if form.is_valid():
            form.save()
            messages.success(request, 'Profile updated successfully!')
            return redirect('profile')
        
    else:
        form = CustomUserForm(instance=request.user)
    
    return render(request, 'profile.html', {'form': form})


# @login_required
def logout_view(request):
    logout(request)
    messages.success(request, 'Logout successfully...!')
    return redirect('login')


# @login_required
def search_books(request):
    query = request.GET.get('q', '')
    
 
    
    books = Book.objects.filter(
        Q(title__icontains=query) |
        Q(author__icontains=query) |
        Q(genre__icontains=query) |
        Q(location__icontains=query) 
    ).filter(owner__user_type='Owner').order_by('-created_at') 
    context = {
        'books': books,
        'query': query
    }
    return render(request, 'index.html', context)

# Request Password Reset View
def request_reset(request):
    if request.method == "POST":
        email = request.POST.get("email")
        try:
            user = CustomUser.objects.get(email=email)
            token = get_random_string(50)  

         
            PasswordResetToken.objects.create(user=user, token=token)

            reset_link = f"{settings.SITE_URL}/reset_password/{token}/"

            
            send_mail(
                "Password Reset Request",
                f"Click the link below to reset your password:\n{reset_link}",
                settings.EMAIL_HOST_USER,
                [email],
                fail_silently=False,
            )

            messages.success(request, "A reset link has been sent to your email.")
            return redirect("password_reset_done")
        except CustomUser.DoesNotExist:
            messages.error(request, "User with this email does not exist.")

    return render(request, "request_reset.html")


# Reset Password View
def reset_password(request, token):
    try:
        reset_obj = PasswordResetToken.objects.get(token=token)
    except PasswordResetToken.DoesNotExist:
        messages.error(request, "Invalid or expired token.")
        return redirect("request-reset")

    if request.method == "POST":
        password = request.POST.get("password")
        confirm_password = request.POST.get("confirm_password")
        
        if password == confirm_password:
            reset_obj.user.set_password(password)
            reset_obj.user.save()
            reset_obj.delete()
            messages.success(request, "Password updated successfully.")
            return redirect("login")
        else:
            messages.error(request, "Passwords do not match.")

    return render(request, "reset_password.html", {"token": token})