from django.urls import path

from . import views

app_name = "accounts"

urlpatterns = [
    path("register/", views.register_view, name="register"),
    path("profile/", views.profile_view, name="profile"),
    path("notifications/", views.notification_list, name="notifications"),
    path("panel/dashboard/", views.admin_dashboard, name="admin-dashboard"),
    path("panel/users/", views.admin_user_list, name="admin-user-list"),
    path("panel/users/<int:user_id>/approve/", views.admin_user_approve, name="admin-user-approve"),
    path("panel/users/<int:user_id>/suspend/", views.admin_user_suspend, name="admin-user-suspend"),
    path("panel/users/<int:user_id>/delete/", views.admin_user_delete, name="admin-user-delete"),
    path("panel/logs/", views.admin_logs, name="admin-logs"),
]
