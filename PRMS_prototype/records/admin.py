from django.contrib import admin
from accounts.models import CustomUser
from records.models import Patient
# Register your models here.
admin.site.register(CustomUser)
admin.site.register(Patient)