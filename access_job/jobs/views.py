from django.contrib.auth.decorators import login_required
from django.contrib.auth.decorators import user_passes_test
from django.db.models import Q
from django.core.mail import send_mail
from django.shortcuts import get_object_or_404, redirect, render

from accounts.models import Notification, Profile, log_activity
from applications.models import Application, Bookmark
from jobs.forms import JobForm
from jobs.models import Job


def home(request):
    return render(request, "jobs/landing.html")


def job_list(request):
    jobs = Job.objects.filter(status="open")

    keyword = request.GET.get("keyword", "").strip()
    category = request.GET.get("category", "").strip()
    location = request.GET.get("location", "").strip()
    salary_min = request.GET.get("salary_min", "").strip()
    salary_max = request.GET.get("salary_max", "").strip()

    if keyword:
        jobs = jobs.filter(
            Q(title__icontains=keyword) | Q(company__icontains=keyword) | Q(description__icontains=keyword)
        )
    if category:
        jobs = jobs.filter(category__icontains=category)
    if location:
        jobs = jobs.filter(location__icontains=location)
    if salary_min:
        jobs = jobs.filter(salary_max__gte=salary_min)
    if salary_max:
        jobs = jobs.filter(salary_min__lte=salary_max)

    bookmarked_ids = set()
    applied_ids = set()
    if request.user.is_authenticated:
        bookmarked_ids = set(Bookmark.objects.filter(user=request.user).values_list("job_id", flat=True))
        applied_ids = set(Application.objects.filter(user=request.user).values_list("job_id", flat=True))

    context = {
        "jobs": jobs,
        "bookmarked_ids": bookmarked_ids,
        "applied_ids": applied_ids,
        "filters": {
            "keyword": keyword,
            "category": category,
            "location": location,
            "salary_min": salary_min,
            "salary_max": salary_max,
        },
    }
    return render(request, "jobs/job_list.html", context)


@login_required
def apply_job(request, job_id):
    job = get_object_or_404(Job, id=job_id, status="open")
    cover_letter = request.POST.get("cover_letter", "")
    Application.objects.get_or_create(user=request.user, job=job, defaults={"cover_letter": cover_letter})
    log_activity(request.user, "Applied job", f"Applied for {job.title}.")
    return redirect("jobs:job-list")


@login_required
def toggle_bookmark(request, job_id):
    job = get_object_or_404(Job, id=job_id)
    bookmark, created = Bookmark.objects.get_or_create(user=request.user, job=job)
    if not created:
        bookmark.delete()
        log_activity(request.user, "Removed bookmark", f"Removed bookmark for {job.title}.")
    else:
        log_activity(request.user, "Bookmarked job", f"Bookmarked {job.title}.")
    return redirect("jobs:job-list")


def is_staff_user(user):
    return user.is_authenticated and user.is_staff


def send_job_alert_notifications(job):
    user_profiles = Profile.objects.select_related("user").filter(user__is_active=True)
    for profile in user_profiles:
        skills_text = (profile.skills or "").lower()
        job_text = f"{job.title} {job.description} {job.category}".lower()
        location_match = not profile.location or profile.location.lower() in (job.location or "").lower()
        category_match = not profile.skills or any(token in job_text for token in skills_text.split(","))
        if not location_match and not category_match:
            continue

        Notification.objects.create(
            user=profile.user,
            title=f"New Job Alert: {job.title}",
            message=f"{job.title} at {job.company} in {job.location}.",
            notification_type="job_alert",
        )

        if profile.user.email:
            send_mail(
                subject=f"Job Alert: {job.title}",
                message=(
                    f"Hello {profile.user.username},\n\n"
                    f"A new job may match your profile:\n"
                    f"{job.title} at {job.company}\n"
                    f"Location: {job.location}\n\n"
                    f"Please login to view details."
                ),
                from_email=None,
                recipient_list=[profile.user.email],
                fail_silently=True,
            )


@login_required
@user_passes_test(is_staff_user)
def admin_job_list(request):
    jobs = Job.objects.all()
    return render(request, "jobs/admin_job_list.html", {"jobs": jobs})


@login_required
@user_passes_test(is_staff_user)
def admin_job_create(request):
    form = JobForm(request.POST or None)
    if request.method == "POST" and form.is_valid():
        new_job = form.save()
        send_job_alert_notifications(new_job)
        log_activity(request.user, "Created job", f"Created job {new_job.title}.")
        return redirect("jobs:admin-job-list")
    return render(request, "jobs/admin_job_form.html", {"form": form, "title": "Add Job"})


@login_required
@user_passes_test(is_staff_user)
def admin_job_edit(request, job_id):
    job = get_object_or_404(Job, id=job_id)
    form = JobForm(request.POST or None, instance=job)
    if request.method == "POST" and form.is_valid():
        form.save()
        log_activity(request.user, "Updated job", f"Updated job {job.title}.")
        return redirect("jobs:admin-job-list")
    return render(request, "jobs/admin_job_form.html", {"form": form, "title": "Edit Job"})


@login_required
@user_passes_test(is_staff_user)
def admin_job_delete(request, job_id):
    job = get_object_or_404(Job, id=job_id)
    if request.method == "POST":
        job_title = job.title
        job.delete()
        log_activity(request.user, "Deleted job", f"Deleted job {job_title}.")
        return redirect("jobs:admin-job-list")
    return render(request, "jobs/admin_job_delete.html", {"job": job})
