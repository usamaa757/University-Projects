from django.contrib.auth.decorators import login_required
from django.contrib.auth.decorators import user_passes_test
from django.contrib import messages
from django.shortcuts import get_object_or_404
from django.shortcuts import redirect, render

from accounts.models import log_activity

from .forms import RecommendationForm
from .models import Recommendation
from .services import generate_user_recommendations


@login_required
def recommendation_list(request):
    min_match_percent = request.GET.get("min_match", "70").strip()
    if min_match_percent not in {"70", "80"}:
        min_match_percent = "70"

    min_score = int(min_match_percent) / 100.0
    recommendations = (
        Recommendation.objects.filter(
            user=request.user,
            model_type="hybrid",
            score__gte=min_score,
        )
        .select_related("job")
        .order_by("-score", "-created_at")
    )
    return render(
        request,
        "recomendations/recommendation_list.html",
        {
            "recommendations": recommendations,
            "min_match": min_match_percent,
        },
    )


@login_required
def generate_recommendations(request):
    generated_count = generate_user_recommendations(request.user)
    log_activity(
        request.user,
        "Generated recommendations",
        f"Generated or refreshed recommendations for {generated_count} jobs.",
    )
    messages.success(request, f"Recommendations generated for {generated_count} jobs.")
    return redirect("recomendations:recommendation-list")


def is_staff_user(user):
    return user.is_authenticated and user.is_staff


@login_required
@user_passes_test(is_staff_user)
def admin_recommendation_list(request):
    recommendations = Recommendation.objects.select_related("user", "job")
    return render(
        request,
        "recomendations/admin_recommendation_list.html",
        {"recommendations": recommendations},
    )


@login_required
@user_passes_test(is_staff_user)
def admin_recommendation_edit(request, recommendation_id):
    recommendation = get_object_or_404(Recommendation, id=recommendation_id)
    form = RecommendationForm(request.POST or None, instance=recommendation)
    if request.method == "POST" and form.is_valid():
        form.save()
        log_activity(
            request.user,
            "Updated recommendation",
            f"Updated recommendation {recommendation.id} for {recommendation.user.username}.",
        )
        return render(
            request,
            "recomendations/admin_recommendation_edit.html",
            {"form": form, "recommendation": recommendation, "saved": True},
        )
    return render(
        request,
        "recomendations/admin_recommendation_edit.html",
        {"form": form, "recommendation": recommendation, "saved": False},
    )
