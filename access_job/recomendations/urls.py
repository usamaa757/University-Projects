from django.urls import path

from . import views

app_name = "recomendations"

urlpatterns = [
    path("recommendations/", views.recommendation_list, name="recommendation-list"),
    path("recommendations/generate/", views.generate_recommendations, name="generate-recommendations"),
    path("panel/recommendations/", views.admin_recommendation_list, name="admin-recommendation-list"),
    path(
        "panel/recommendations/<int:recommendation_id>/edit/",
        views.admin_recommendation_edit,
        name="admin-recommendation-edit",
    ),
]
