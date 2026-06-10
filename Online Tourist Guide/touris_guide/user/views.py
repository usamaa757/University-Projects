from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login as auth_login, authenticate, logout
from django.contrib import messages
from .forms import LoginForm, CommentForm, ActivityForm, ReviewForm, TravelTipForm, HotelDiscountForm, UserCreationForm, ChangeUserForm, UserProfileForm, DestinationForm, TravelInterestForm, PhotoUploadForm, ServiceProviderForm
from django.contrib.auth.decorators import login_required
from .models import CustomUser, Activity, Destination, TravelTip, HotelDiscount, TravelInterest, Review, Photos, Like, Comment, ServiceProvider
from django.contrib.auth.views import LoginView

# Index View
def index(request):
    return render(request, 'index.html')

# Login View
def login(request):
    if request.method == 'POST':
        form = LoginForm(request.POST)
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

# Register View
def register(request):
    if request.method == 'POST':
        form = UserCreationForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "You have successfully Registered.")
            return redirect('login')
    else:
        form = UserCreationForm()
    return render(request, 'register.html', {'form': form})

# Admin Dashboard View
@login_required
def admin_dashboard(request):
    if not request.user.is_superuser:
        return redirect('login')
    else:
        return render(request, "admin_dashboard.html")

@login_required
def user_dashboard(request):
    if request.user.is_superuser:
        return redirect('login')
    else:
        user = request.user  
        preferred_type = user.preferred_travel_type 

        suggested_destinations = Destination.objects.filter(
            travelinterest__travel_type=preferred_type
        ).distinct()[:3]

        suggested_activities = Activity.objects.filter(
            category=preferred_type
        )[:3]

        suggested_tips = TravelTip.objects.filter(
            category=preferred_type
        )[:3]

        hotel_discounts = HotelDiscount.objects.filter(
            destination__in=suggested_destinations
        )[:3]

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

@login_required

def user_logout(request):
    logout(request)
    return redirect("login")
@login_required

def edit_tip(request, id):
    if request.user.is_superuser:
    
        tip = get_object_or_404(TravelTip, id=id)

        if request.method == 'POST':
            form = TravelTipForm(request.POST, instance=tip)
            if form.is_valid():
                form.save()
                messages.success(request, "Tip udated successfully!") 
                
                return redirect('travel_tips_list')
        else:
            form = TravelTipForm(instance=tip)

    return render(request, 'edit_tip.html', {'form': form, 'tip': tip})
@login_required
 
def delete_tip(request, id):
    if request.user.is_superuser:
        tip = get_object_or_404(TravelTip, id=id)
        tip.delete()
        messages.success(request, "Tip deleted successfully!") 
        
        return redirect('travel_tips_list')
    return redirect('login') 

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
    if request.user.is_superuser:
        users = CustomUser.objects.filter(is_superuser=False) 
        return render(request, "user_list.html", {"users": users})
    else:
        messages.error(request, "Unauthorized access. Only admins can create users.")
        return redirect('user_dashboard')  

@login_required
def create_activity(request):
    if request.user.is_superuser: 
        if request.method == "POST":
            form = ActivityForm(request.POST)
            if form.is_valid():
                form.save()
                messages.success(request, 'Activity created')
                return redirect('create_activity')
        else:
            form = ActivityForm()
        return render(request, 'create_activity.html', {'form': form})
    else:  # User only
        if request.method == "POST":
            form = ActivityForm(request.POST)
            if form.is_valid():
                form.save()
                messages.success(request, 'Activity created')
                return redirect('create_activity')
        else:
            form = ActivityForm()
        return render(request, 'create_activity.html', {'form': form})

# Create Travel Tip View (Admin and User)
@login_required
def create_travel_tip(request):
    if request.user.is_superuser:
    
        if request.method == "POST":
            form = TravelTipForm(request.POST)
            if form.is_valid():
                form.save()
                messages.success(request, 'Tip created')
                return redirect('create_travel_tip')
        else:
            form = TravelTipForm()
    return render(request, 'create_travel_tip.html', {'form': form})

# Travel Tips List View (Admin and User)
def travel_tips_list(request):
    travel_tips = TravelTip.objects.all().order_by('-created_at')
    return render(request, 'travel_tip_list.html', {'travel_tips': travel_tips})

# Activity List View (Admin and User)
def activity_list(request):
    activities = Activity.objects.all()  
    return render(request, "activity_list.html", {"activities": activities})

# Edit Activity View (Admin and User)
@login_required
def edit_activity(request, activity_id):
    activity = get_object_or_404(Activity, id=activity_id)

    if request.method == "POST":
        form = ActivityForm(request.POST, instance=activity)
        if form.is_valid():
            form.save()
            messages.success(request, "Activity updated successfully!")
            return redirect("activity_list")
    else:
        form = ActivityForm(instance=activity)

    return render(request, "edit_activity.html", {"form": form})

# Delete User View (Admin)
@login_required
def delete_user(request, user_id):
    if request.user.is_superuser:
        user = get_object_or_404(CustomUser, id=user_id)
        user.delete()
        messages.success(request, "User deleted successfully!")
        return redirect('user_list')
    else:
        messages.error(request, "Unauthorized access. Only admins can delete users.")
        return redirect('user_dashboard')

# Delete Activity View (Admin and User)
@login_required
def delete_activity(request, activity_id):
    if request.user.is_superuser:
        activity = get_object_or_404(Activity, id=activity_id)
        activity.delete()
        messages.success(request, 'Activity deleted')
        return redirect('activity_list')
    else:
        messages.error(request, "Unauthorized access. Only admins can delete activities.")
        return redirect('user_dashboard')

@login_required
def delete_travel_tip(request, tip_id):
    if request.user.is_superuser:
        tip = get_object_or_404(TravelTip, id=tip_id)
        tip.delete()
        return redirect('admin_dashboard')
    else:
        messages.error(request, "Unauthorized access. Only admins can delete travel tips.")
        return redirect('user_dashboard')

# Edit User View (Admin)
@login_required
def edit_user(request, user_id):
    if request.user.is_superuser:
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
    else:
        messages.error(request, "Unauthorized access. Only admins can edit users.")
        return redirect('user_dashboard')

# Destination List View (Admin and User)
def destination_list(request):
  
        destinations = Destination.objects.all()
        return render(request, 'destination_list.html', {'destinations': destinations})

# Create Destination View (Admin and User)
@login_required
def create_destination(request):
    if request.user.is_superuser:
        if request.method == 'POST':
            form = DestinationForm(request.POST, request.FILES)
            if form.is_valid():
                form.save()
                messages.success(request, 'Destination created')
                return redirect('destination_list')
        else:
            form = DestinationForm()
        return render(request, 'create_destination.html', {'form': form})
    else:
        messages.error(request, "Unauthorized access. Only admins can create destinations.")
        return redirect('user_dashboard')

# Discount List View (Admin and User)
@login_required
def discount_list(request):
    if request.user.is_authenticated:
    
        discounts = HotelDiscount.objects.all()
        return render(request, 'discount_list.html', {'discounts': discounts})

# Create Discount View (Admin and User)
@login_required
def create_discount(request):
    if request.user.is_superuser:
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
    else:
        messages.error(request, "Unauthorized access. Only admins can create discounts.")
        return redirect('discount_list')

# Edit Discount View (Admin and User)
@login_required
def edit_discount(request, discount_id):
    if request.user.is_superuser:
        discount = get_object_or_404(HotelDiscount, id=discount_id)

        if request.method == 'POST':
            form = HotelDiscountForm(request.POST, instance=discount)
            if form.is_valid():
                form.save()
                return redirect('discount_list')
        else:
            form = HotelDiscountForm(instance=discount)

        return render(request, 'edit_discount.html', {'form': form})
    else:
        messages.error(request, "Unauthorized access. Only admins can edit discounts.")
        return redirect('discount_list')

# Delete Discount View (Admin and User)
@login_required
def delete_discount(request, discount_id):
    if request.user.is_superuser:
        discount = get_object_or_404(HotelDiscount, id=discount_id, user=request.user)
        discount.delete()
        return redirect('user_dashboard')
    else:
        messages.error(request, "Unauthorized access. Only admins can delete discounts.")
        return redirect('user_dashboard')

# Create Travel Interest View (Admin and User)
@login_required
def create_travel_interest(request):
    if request.user:
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
    else:
        messages.error(request, "Unauthorized access. Only admins can create travel interests.")
        return redirect('user_dashboard')


@login_required
def travel_interest_list(request):
    if request.user.is_authenticated:
    
        interests = TravelInterest.objects.filter(user=request.user)
        return render(request, 'travel_interest_list.html', {'interests': interests})

@login_required
def create_review(request, id):
    if request.user.is_superuser:
        return redirect('login')
        destination = get_object_or_404(Destination, id=id)

        if request.method == 'POST':
            form = ReviewForm(request.POST)
            if form.is_valid():
                review = form.save(commit=False)
                review.user = request.user
                review.destination = destination
                review.save()
                return redirect('destination_list') 
        else:
            form = ReviewForm()

    return render(request, 'create_review.html', {'form': form, 'destination': destination})

def view_reviews(request, destination_id):
    
        destination = get_object_or_404(Destination, id=destination_id)
        reviews = destination.reviews.all()
        return render(request, 'view_reviews.html', {
            'destination': destination,
            'reviews': reviews
        })
    

@login_required
def like_photo(request, photo_id):
    if not request.user.is_superuser:
    
        photo = get_object_or_404(Photos, id=photo_id)
        if request.user in photo.likes.all():
            photo.likes.remove(request.user)
        else:
            photo.likes.add(request.user)
    return redirect(request.META.get('HTTP_REFERER'))

@login_required
def comment_photo(request, photo_id):
    if not request.user.is_superuser:
    
        if request.method == 'POST':
            text = request.POST.get('comment')
            if text:
                photo = get_object_or_404(Photos, id=photo_id)
                Comment.objects.create(photo=photo, user=request.user, text=text)
    return redirect(request.META.get('HTTP_REFERER'))


@login_required
def upload_photos(request, destination_id):
    if not request.user.is_superuser:
    
        destination = get_object_or_404(Destination, pk=destination_id)

        if request.method == 'POST':
            form = PhotoUploadForm(request.POST, request.FILES)
            files = request.FILES.getlist('photos')
            if form.is_valid():
                for f in files:
                    Photos.objects.create(destination=destination, image=f, uploaded_by=request.user)
                return redirect('destination_list') 
        else:
            form = PhotoUploadForm()

    return render(request, 'upload_photos.html', {'form': form, 'destination': destination})

def view_photos(request, destination_id):
    if request.user.is_authenticated:
    
        destination = get_object_or_404(Destination, pk=destination_id)
        photos = Photos.objects.filter(destination=destination).prefetch_related('likes', 'comments')

        return render(request, 'view_photos.html', {
            'destination': destination,
            'photos': photos,
            'user': request.user,
        })

@login_required
def create_service_provider(request):
    if request.user.is_superuser:
    
        if request.method == 'POST':
            form = ServiceProviderForm(request.POST)
            if form.is_valid():
                form.save()
                return redirect('service_provider_list')  # create this view or redirect where needed
        else:
            form = ServiceProviderForm()
        return render(request, 'create_service_provider.html', {'form': form})
    
def service_provider_list(request):
    if request.user.is_authenticated:
    
        providers = ServiceProvider.objects.all()
        return render(request, 'service_provider_list.html', {'providers': providers})

@login_required
def edit_destination(request, pk):
    if request.user.is_superuser:
        
        destination = get_object_or_404(Destination, pk=pk)
        
        if request.method == 'POST':
            form = DestinationForm(request.POST, request.FILES, instance=destination)
            if form.is_valid():
                form.save()
                messages.success(request, 'Destination updated successfully.')
                return redirect('destination_list')  
        else:
            form = DestinationForm(instance=destination)

    return render(request, 'edit_destination.html', {'form': form})

@login_required
def delete_destination(request, pk):
    if request.user.is_superuser:
        
        destination = get_object_or_404(Destination, pk=pk)
        destination.delete()
        messages.success(request, 'Destination deleted successfully.')
    return redirect('destination_list')

@login_required
def edit_service(request, pk):
    if request.user.is_superuser:
        provider = get_object_or_404(ServiceProvider, pk=pk)
        if request.method == 'POST':
            form = ServiceProviderForm(request.POST, instance=provider)
            if form.is_valid():
                form.save()
                messages.success(request, 'Service updated successfully.')
                
                return redirect('service_provider_list')
        else:
            form = ServiceProviderForm(instance=provider)
    return render(request, 'edit_service.html', {'form': form})

@login_required

def delete_provider(request, pk):
    if request.user.is_superuser:
    
        provider = get_object_or_404(ServiceProvider, pk=pk)
        provider.delete()
        messages.success(request, 'Destination deleted successfully.')

    return redirect('service_provider_list')