from django.contrib import messages
from django.contrib.auth import login
from django.contrib.auth.decorators import login_required
from django.contrib.auth.decorators import user_passes_test
from django.contrib.auth.forms import UserCreationForm
from django.contrib.auth.models import User
from django.db.models import Avg
from django.shortcuts import redirect, render

from applications.models import Application
from jobs.models import Job
from recomendations.models import Recommendation

from .models import ActivityLog, Notification, Profile, log_activity


def register_view(request):
    if request.user.is_authenticated:
        return redirect("jobs:job-list")

    form = UserCreationForm(request.POST or None)
    if request.method == "POST" and form.is_valid():
        user = form.save()
        Profile.objects.create(user=user)
        login(request, user)
        log_activity(user, "User registered", "New account created from registration form.")
        messages.success(request, "Account created successfully.")
        return redirect("jobs:job-list")

    return render(request, "accounts/register.html", {"form": form})


@login_required
def profile_view(request):
    profile, _ = Profile.objects.get_or_create(user=request.user)

    if request.method == "POST":
        profile.phone = request.POST.get("phone", "")
        profile.location = request.POST.get("location", "")
        profile.skills = request.POST.get("skills", "")
        profile.education = request.POST.get("education", "")
        profile.experience = request.POST.get("experience", "")
        if request.FILES.get("resume"):
            profile.resume = request.FILES["resume"]
        profile.save()
        log_activity(request.user, "Profile updated", "User updated profile information.")
        messages.success(request, "Profile updated.")
        return redirect("accounts:profile")

    return render(request, "accounts/profile.html", {"profile": profile})


def is_staff_user(user):
    return user.is_authenticated and user.is_staff


@login_required
@user_passes_test(is_staff_user)
def admin_dashboard(request):
    total_users = User.objects.count()
    active_users = User.objects.filter(is_active=True).count()
    total_jobs = Job.objects.count()
    open_jobs = Job.objects.filter(status="open").count()
    total_applications = Application.objects.count()
    total_recommendations = Recommendation.objects.filter(model_type="hybrid").count()
    avg_recommendation_score = Recommendation.objects.aggregate(avg=Avg("score")).get("avg") or 0
    applied_pairs = set(Application.objects.values_list("user_id", "job_id"))
    recommendation_hit_count = 0
    for rec_user_id, rec_job_id in Recommendation.objects.filter(model_type="hybrid").values_list("user_id", "job_id"):
        if (rec_user_id, rec_job_id) in applied_pairs:
            recommendation_hit_count += 1
    recommendation_to_application_rate = 0
    if total_recommendations:
        recommendation_to_application_rate = (recommendation_hit_count / total_recommendations) * 100

    unread_notifications = Notification.objects.filter(is_read=False).count()
    recent_logs = ActivityLog.objects.select_related("user")[:10]

    context = {
        "total_users": total_users,
        "active_users": active_users,
        "total_jobs": total_jobs,
        "open_jobs": open_jobs,
        "total_applications": total_applications,
        "total_recommendations": total_recommendations,
        "avg_recommendation_score": round(avg_recommendation_score, 2),
        "recommendation_to_application_rate": round(recommendation_to_application_rate, 2),
        "unread_notifications": unread_notifications,
        "recent_logs": recent_logs,
    }
    return render(request, "accounts/admin_dashboard.html", context)


@login_required
@user_passes_test(is_staff_user)
def admin_user_list(request):
    users = User.objects.all().order_by("-date_joined")
    return render(request, "accounts/admin_user_list.html", {"users": users})


@login_required
@user_passes_test(is_staff_user)
def admin_user_approve(request, user_id):
    target_user = User.objects.get(id=user_id)
    target_user.is_active = True
    target_user.save()
    profile, _ = Profile.objects.get_or_create(user=target_user)
    profile.is_approved = True
    profile.save()
    log_activity(request.user, "Approved user", f"Approved user {target_user.username}.")
    return redirect("accounts:admin-user-list")


@login_required
@user_passes_test(is_staff_user)
def admin_user_suspend(request, user_id):
    target_user = User.objects.get(id=user_id)
    if target_user.id != request.user.id:
        target_user.is_active = False
        target_user.save()
        log_activity(request.user, "Suspended user", f"Suspended user {target_user.username}.")
    return redirect("accounts:admin-user-list")


@login_required
@user_passes_test(is_staff_user)
def admin_user_delete(request, user_id):
    target_user = User.objects.get(id=user_id)
    if target_user.id != request.user.id:
        username = target_user.username
        target_user.delete()
        log_activity(request.user, "Deleted user", f"Deleted user {username}.")
    return redirect("accounts:admin-user-list")


@login_required
@user_passes_test(is_staff_user)
def admin_logs(request):
    logs = ActivityLog.objects.select_related("user")
    return render(request, "accounts/admin_logs.html", {"logs": logs})


@login_required
def notification_list(request):
    notifications = Notification.objects.filter(user=request.user)
    notifications.filter(is_read=False).update(is_read=True)
    return render(request, "accounts/notifications.html", {"notifications": notifications})
