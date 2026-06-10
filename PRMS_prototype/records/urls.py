from django.urls import path, include
from . import views
from django.conf import settings
from django.conf.urls.static import static

urlpatterns = [
    path('add_patient/', views.add_patient, name='add_patient'),
    path('patient_list/', views.patient_list, name='patient_list'),

] + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
