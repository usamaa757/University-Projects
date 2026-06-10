from django.urls import path
from django.views.generic import RedirectView

from . import views

app_name = "jobs"

urlpatterns = [
    path("", RedirectView.as_view(pattern_name="jobs:index", permanent=False)),
    path("index.html", views.home, name="index"),
    path("jobs/", views.job_list, name="job-list"),
    path("jobs/<int:job_id>/apply/", views.apply_job, name="apply-job"),
    path("jobs/<int:job_id>/bookmark/", views.toggle_bookmark, name="toggle-bookmark"),
    path("panel/jobs/", views.admin_job_list, name="admin-job-list"),
    path("panel/jobs/add/", views.admin_job_create, name="admin-job-create"),
    path("panel/jobs/<int:job_id>/edit/", views.admin_job_edit, name="admin-job-edit"),
    path("panel/jobs/<int:job_id>/delete/", views.admin_job_delete, name="admin-job-delete"),
]
