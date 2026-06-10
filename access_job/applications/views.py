from django.contrib.auth.decorators import login_required
from django.shortcuts import render

from .models import Application


@login_required
def application_list(request):
    applications = Application.objects.filter(user=request.user).select_related("job")
    return render(request, "applications/application_list.html", {"applications": applications})
