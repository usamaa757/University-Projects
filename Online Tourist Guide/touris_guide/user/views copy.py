from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login as auth_login, authenticate, logout
from django.contrib import messages
from .forms import LoginForm, ActivityForm, TravelTipForm, HotelDiscountForm, UserCreationForm, ChangeUserForm, MobileLoginForm, UserProfileForm, DestinationForm, TravelInterestForm
from django.contrib.auth.decorators import login_required
from .models import CustomUser, Activity, Destination, TravelTip, HotelDiscount, TravelInterest
from django.contrib.auth.views import LoginView


def index(request):
    return render(request, 'index.html')
# views.py
from django.contrib import messages
from django.contrib.auth import authenticate, login as auth_login
from django.shortcuts import render, redirect
from .forms import LoginForm  # Ensure your form is imported

def login(request):
    if request.method == 'POST':
        form = LoginForm(request.POST)  # No 'request=' here
        if form.is_valid():
            username = form.cleaned_data['username']
            password = form.cleaned_data['password']

            user = authenticate(request, username=username, password=password)
            if user is not None:
                auth_login(request, user)
                return redirect('admin_dashboard' if user.is_superuser else 'user_dashboard')
            else:
                messages.error(request, "Invalid username or password.")
        else:
            messages.error(request, "Please correct the errors below.")
    else:
        form = LoginForm()

    return render(request, 'login.html', {'form': form})


def register(request):
    if request.method == 'POST':
        form = UserCreationForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "You have successfully logged in.")

            return redirect('login')
    else:
        form = UserCreationForm()
    return render(request, 'register.html', {'form': form})


@login_required
def admin_dashboard(request):
    return render(request, "admin_dashboard.html")

@login_required
def user_dashboard(request):
    user = request.user  
    preferred_type = user.preferred_travel_type  

    suggested_destinations = Destination.objects.filter(travel_type=preferred_type)
    suggested_activities = Activity.objects.filter(category=preferred_type)
    suggested_tips = TravelTip.objects.filter(category=preferred_type)

    # Fetch hotel discounts for the suggested destinations
    hotel_discounts = HotelDiscount.objects.filter(destination__in=suggested_destinations)

    context = {
        'suggested_destinations': suggested_destinations,
        'suggested_activities': suggested_activities,
        'suggested_tips': suggested_tips,
        'hotel_discounts': hotel_discounts
    }
    return render(request, 'user_dashboard.html', context)



@login_required
def edit_profile(request):
    user_profile = CustomUser.objects.get(pk=request.user.pk)  

    if request.method == "POST":
        form = UserProfileForm(request.POST, instance=user_profile)
        if form.is_valid():
            form.save()
            messages.success(request, "Profile updated successfully!")
            return redirect("edit_profile")
    else:
        form = UserProfileForm(instance=user_profile)

    return render(request, "edit_profile.html", {"form": form})

def user_logout(request):
    logout(request)
    return redirect("login")

@login_required
def create_user(request):
    if request.user.is_superuser:
        if request.method == "POST":
            form = UserCreationForm(request.POST)
            if form.is_valid():
                form.save()
                messages.success(request, "User registered successfully!") 
                return redirect('user_list')
            else:
                if 'captcha' in form.errors:
                    messages.error(request, "Incorrect CAPTCHA. Please try again.")  
                else:
                    messages.error(request, "There was an error in the form. Please check your inputs.")

        else:
            form = UserCreationForm()
    else:
        messages.error(request, "Unauthorized access. Only admins can create users.")
        return redirect('user_dashboard')  

    return render(request, 'create_user.html', {'form': form})
@login_required
def user_list(request):
    users = CustomUser.objects.filter(is_superuser=False) 
    return render(request, "user_list.html", {"users": users})

@login_required
def create_activity(request):
    if request.method == "POST":
        form = ActivityForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, 'Activity created')
            return redirect('create_activity')
    else:
        form = ActivityForm()
    return render(request, 'create_activity.html', {'form': form})

@login_required
def create_travel_tip(request):
    if request.method == "POST":
        form = TravelTipForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, 'Tip created')
            return redirect('create_travel_tip')
    else:
        form = TravelTipForm()
    return render(request, 'create_travel_tip.html', {'form': form})
def travel_tips_list(request):
    travel_tips = TravelTip.objects.all().order_by('-created_at')
    return render(request, 'travel_tip_list.html', {'travel_tips': travel_tips})

def activity_list(request):
    activities = Activity.objects.all()  # Fetch all activities
    return render(request, "activity_list.html", {"activities": activities})

@login_required
def edit_activity(request, activity_id):
    activity = get_object_or_404(Activity, id=activity_id)

    if request.method == "POST":
        form = ActivityForm(request.POST, instance=activity)
        if form.is_valid():
            form.save()
            messages.success(request, "Activity updated successfully!")
            return redirect("activity_list")  #
    else:
        form = ActivityForm(instance=activity)

    return render(request, "edit_activity.html", {"form": form})


@login_required
def delete_user(request, user_id):
    user = get_object_or_404(CustomUser, id=user_id)
    user.delete()
    messages.success(request, "User deleted successfully!")

    return redirect('user_list')

@login_required
def delete_activity(request, activity_id):
    activity = get_object_or_404(Activity, id=activity_id)
    activity.delete()
    messages.success(request, 'Activity deleted')
    return redirect('activity_list')

@login_required
def delete_travel_tip(request, tip_id):
    tip = get_object_or_404(TravelTip, id=tip_id)
    tip.delete()
    return redirect('admin_dashboard')

@login_required
def edit_user(request, user_id):
    user = get_object_or_404(CustomUser, id=user_id)

    if request.method == "POST":
        form = ChangeUserForm(request.POST, instance=user)
        if form.is_valid():
            form.save()
            messages.success(request, "Profile updated successfully!")
            return redirect("edit_user", user_id=user.id)
        else:
            messages.error(request, "Please correct the errors below.")

    else:
        form = ChangeUserForm(instance=user)

    return render(request, "edit_user.html", {"form": form, "user": user})


def destination_list(request):
    destinations = Destination.objects.all()
    return render(request, 'destination_list.html', {'destinations': destinations})

@login_required
def create_destination(request):
    if request.method == 'POST':
        form = DestinationForm(request.POST, request.FILES)
        if form.is_valid():
            form.save()
            messages.success(request, 'Destination created')
            return redirect('destination_list')
    else:
        form = DestinationForm()
    return render(request, 'create_destination.html', {'form': form})

@login_required
def discount_list(request):
    discounts = HotelDiscount.objects.all()
    return render(request, 'discount.html', {'discounts': discounts})


@login_required
def create_discount(request):
    if request.method == 'POST':
        form = HotelDiscountForm(request.POST)
        if form.is_valid():
            discount = form.save(commit=False)
            discount.user = request.user
            discount.save()
            return redirect('discount_list')
    else:
        form = HotelDiscountForm()
    
    return render(request, 'create_discount.html', {'form': form})

@login_required
def edit_discount(request, discount_id):
    discount = get_object_or_404(HotelDiscount, id=discount_id, user=request.user)
    
    if request.method == 'POST':
        form = HotelDiscountForm(request.POST, instance=discount)
        if form.is_valid():
            form.save()
            return redirect('user_dashboard')
    else:
        form = HotelDiscountForm(instance=discount)

    return render(request, 'edit_discount.html', {'form': form})

@login_required
def delete_discount(request, discount_id):
    discount = get_object_or_404(HotelDiscount, id=discount_id, user=request.user)
    discount.delete()
    return redirect('user_dashboard')

@login_required
def create_travel_interest(request):
    if request.method == 'POST':
        form = TravelInterestForm(request.POST)
        if form.is_valid():
            travel_interest = form.save(commit=False)
            travel_interest.user = request.user
            travel_interest.save()
            return redirect('travel_interest_list')
    else:
        form = TravelInterestForm()
    return render(request, 'create_travel_interest.html', {'form': form})

@login_required
def travel_interest_list(request):
    interests = TravelInterest.objects.filter(user=request.user)
    return render(request, 'travel_interest_list.html', {'interests': interests})