from django.shortcuts import render, redirect
from django.contrib.auth import login, logout, authenticate
from .forms import CustomUserCreationForm, CustomLoginForm
from django.contrib import messages
from reviews.models import Product, Review, SuspiciousIP
from reviews.utils import get_client_ip
from django.db.models import Count
from django.contrib.auth.decorators import login_required, user_passes_test

def register_view(request):
    if request.method == 'POST':
        form = CustomUserCreationForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "Registration successful.")
            return redirect('register')  # You redirect here
    else:
        form = CustomUserCreationForm()
    return render(request, 'register.html', {'form': form})


def login_view(request):
    if request.method == 'POST':
        form = CustomLoginForm(request, data=request.POST)
        if form.is_valid():
            user = form.get_user()
            login(request, user)
            if request.user.is_superuser:
                return redirect('admin_dashboard')
            else:
                return redirect('user_dashboard')
            messages.error(request, "You are not authorized to view this page.")
            return redirect('login')  
        else:
            messages.error(request, "Invalid credentials.")
    else:
        form = CustomLoginForm()
    return render(request, 'login.html', {'form': form})

@login_required
@user_passes_test(lambda u: u.is_superuser)
def admin_dashboard(request):
    products = Product.objects.all()

    # Rating-based review summary
    total_positive = Review.objects.filter(rating__gte=4).count()
    total_negative = Review.objects.filter(rating__lte=2).count()
    total_neutral = Review.objects.filter(rating=3).count()
    total_reviews = total_positive + total_negative + total_neutral

    # 🔔 ADMIN NOTIFICATIONS (NEW)
    fake_review_count = Review.objects.filter(is_fake=True).count()
    suspicious_ip_count = SuspiciousIP.objects.count()

    return render(request, 'admin_dashboard.html', {
        'products': products,
        'total_positive': total_positive,
        'total_negative': total_negative,
        'total_neutral': total_neutral,
        'total_reviews': total_reviews,

        # Notifications
        'fake_review_count': fake_review_count,
        'suspicious_ip_count': suspicious_ip_count,
    })

def user_dashboard(request):
    if request.user.is_user:
        products = Product.objects.all()
        ip_address = get_client_ip(request)
        fake_reviews = Review.objects.filter(ip_address=ip_address, is_fake=True)

        context = {
            'products': products,
            'fake_reviews': fake_reviews,
            'ip_address': ip_address
        }
        return render(request, 'user_dashboard.html', context)
    else:
        messages.error(request, "You are not authorized to view this page.")
        return redirect('login')


def logout_view(request):
    logout(request)
    messages.success(request, "Logged out.")
    return redirect('login')
