from django.urls import path
from . import views

urlpatterns = [
    path("login/", views.login, name="login"),
    path("logout/", views.user_logout, name="logout"),
    path("", views.index, name="index"),
    path("admin_dashboard/", views.admin_dashboard, name="admin_dashboard"),
    path("user_dashboard/", views.user_dashboard, name="user_dashboard"),
    path('delete_user/<int:user_id>/', views.delete_user, name='delete_user'),
    path('delete_activity/<int:activity_id>/', views.delete_activity, name='delete_activity'),
    path('delete_travel_tip/<int:tip_id>/', views.delete_travel_tip, name='delete_travel_tip'),
    path('create_user/', views.create_user, name='create_user'),
    path('create_activity/', views.create_activity, name='create_activity'),
    path('create_travel_tip/', views.create_travel_tip, name='create_travel_tip'),   
    path("activities/", views.activity_list, name="activity_list"),
    path("user_list/", views.user_list, name="user_list"),
    path("edit_activity/<int:activity_id>/", views.edit_activity, name="edit_activity"),
    path("edit_user/<int:user_id>/", views.edit_user, name="edit_user"),
    path("edit_profile", views.edit_profile, name="edit_profile"),

    # path('login/', views.CustomLoginView.as_view(), name='login'),
    path('register/', views.register, name='register'),
    path('destination_list/', views.destination_list, name='destination_list'),
    path('create_destination/', views.create_destination, name='create_destination'),
    path('travel_tips/', views.travel_tips_list, name='travel_tips_list'),
    path('create_discount/', views.create_discount, name='create_discount'),
    path('edit_discount/<int:discount_id>/', views.edit_discount, name='edit_discount'),
    path('delete_discount/<int:discount_id>/', views.delete_discount, name='delete_discount'),
]
