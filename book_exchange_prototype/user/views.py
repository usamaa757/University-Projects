from django.shortcuts import render, redirect
from .forms import RegisterForm
from django.contrib import messages
from django.contrib.auth import authenticate, login, logout
from django.core.mail import send_mail
from .models import CustomUser
from django.contrib.auth.decorators import login_required

def index(request):
    return render(request, 'index.html')

def register(request):
    if request.method == "POST":
        form = RegisterForm(request.POST, request.FILES)
        if form.is_valid():
            form.save()
            messages.success(request, "Registration successful! Wait for account approval.")
            return redirect("login")
    else:
        form = RegisterForm()

    return render(request, "register.html", {"form": form})

def login_view(request):
    if request.method == "POST":
        username = request.POST['username']
        password = request.POST['password']
        user = authenticate(request, username=username, password=password)

        if user is not None:
            login(request, user)  # Log in user before redirecting
            if user.is_superuser:
                return redirect('admin_dashboard')
            elif user.is_approved:
                return redirect('user_dashboard')
            else:
                messages.warning(request, "Your account is awaiting approval.")
        else:
            messages.warning(request, "Invalid username or password.")

    return render(request, "login.html")
@login_required
def user_dashboard(request):
    return render(request, 'user_dashboard.html')

@login_required  # Only authenticated users can access this view all_users
def admin_dashboard(request):
    if not request.user.is_superuser:
        return redirect('login')

    unapproved_users = CustomUser.objects.filter(is_active=True, is_approved=False, is_superuser=False)

    return render(request, 'admin_dashboard.html', {'unapproved_users': unapproved_users})

@login_required  # Only authenticated users can access this view
def all_users(request):
    if not request.user.is_superuser:
        return redirect('index')

    users = CustomUser.objects.filter(is_superuser=False)

    return render(request, 'all_users.html', {'users': users})

@login_required
def approve_user(request, user_id):
    if not request.user.is_superuser:
        return redirect('index')

    user = CustomUser.objects.get(id=user_id)
    user.is_approved = True
    user.is_active = True
    user.save()


    # Send Approval Email
    # send_mail(
    #     'Your Account Has Been Approved',
    #     'Congratulations! Your account has been approved. You can now log in.',
    #     'admin@bookexchange.com',
    #     [user.email],
    #     fail_silently=False,
    # )
    send_mail(
        subject='Your Account Has Been Approved',
        message='Congratulations! Your account has been approved. You can now log in.',  # Plain text fallback
        from_email='admin@bookexchange.com',
        recipient_list=[user.email],
        fail_silently=False,
        html_message=f"""
        <html>
        <body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
            <div style="max-width: 600px; margin: auto; background: white; padding: 20px; 
                        border-radius: 5px; box-shadow: 0px 0px 10px #ddd;">
                <h2 style="color: #333;">Congratulations, {user.first_name}!</h2>
                <p style="font-size: 16px; color: #555;">
                    Your account has been approved. You can now log in and start using our platform.
                </p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="http://127.0.0.1:8000/login/" 
                       style="display: inline-block; padding: 12px 20px; font-size: 16px; color: white; 
                              background-color: #31165d; text-decoration: none; border-radius: 5px;">
                        Log In
                    </a>
                </div>
            </div>
        </body>
        </html>
        """
    )

    messages.success(request, f'User {user.username} has been approved.')
    return redirect('all_users')

@login_required
def de_activate_user(request, user_id):
    if not request.user.is_superuser:
        return redirect('index')
    
    user = CustomUser.objects.get(id=user_id)
    user.is_active = False
    user.is_approved = False
    user.save()
    messages.success(request, f'User {user.username} has been de-activated.')
    return redirect('all_users')


def admin_login(request):
    if request.method == "POST":
        username = request.POST['username']
        password = request.POST['password']
        user = authenticate(request, username=username, password=password)

        if user is not None and user.is_superuser:  # Only allow superusers
            login(request, user)
            return redirect('admin_dashboard')  # Redirect to the Admin Dashboard
        else:
            messages.error(request, "Invalid admin credentials.")

    return render(request, "admin_login.html")
@login_required
def logout_view(request):
    logout(request)
    messages.info(request, "Logged out successfully.")
    return redirect('login')