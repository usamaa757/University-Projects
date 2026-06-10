from django.urls import path
from . import views

urlpatterns = [
        path('add_product/', views.add_product, name='add_product'),
        path('product_list/', views.product_list, name='product_list'),
        path('edit_product/<int:pk>/', views.edit_product, name='edit_product'),
        path('delete_product/<int:pk>/', views.delete_product, name='delete_product'),

]
