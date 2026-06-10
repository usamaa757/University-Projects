from django.urls import path

from . import views

app_name = "applications"

urlpatterns = [
    path("applications/", views.application_list, name="application-list"),
]
