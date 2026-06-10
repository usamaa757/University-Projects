from django.urls import path
from . import views

urlpatterns = [
        path('add_review/<int:pk>', views.add_review, name='add_review'),
        path('delete_review/<int:pk>/', views.delete_review, name='delete_review'),
        path('fake_reviews/', views.fake_reviews, name='fake_reviews'),
        path('delete_review/<int:review_id>/', views.delete_review, name='delete_review'),
        path('product_ranking/', views.product_ranking, name='product_ranking'),
        path('suspicious_ips/', views.suspicious_ips, name='suspicious_ips'),
        path('review_analysis/', views.review_analysis, name='review_analysis'),

]
