from django.contrib import admin
from .models import CustomUser, Activity, TravelTip, Destination, HotelDiscount

admin.site.register(CustomUser)
admin.site.register(Activity)
admin.site.register(TravelTip)
admin.site.register(Destination)
admin.site.register(HotelDiscount)
