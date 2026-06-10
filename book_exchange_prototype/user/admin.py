from django.contrib import admin
from .models import CustomUser

class UserAdmin(admin.ModelAdmin):
    list_display = ('username', 'email', 'is_approved')
    list_filter = ('is_approved',)
    actions = ['approve_users']

    def approve_users(self, request, queryset):
        for user in queryset:
            user.is_approved = True
            user.is_active = True
            user.save()
            send_mail(
                'Account Approved',
                'Your account has been approved by the admin.',
                'admin@bookexchange.com',
                [user.email],
                fail_silently=False,
            )
        self.message_user(request, "Selected users approved successfully!")

admin.site.register(CustomUser, UserAdmin)
