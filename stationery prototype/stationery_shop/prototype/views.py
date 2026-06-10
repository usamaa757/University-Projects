# login_app/views.py
from django.shortcuts import render
from .forms import LoginForm

def index(request):
    logged_in = False
    if request.method == 'POST':
        form = LoginForm(request.POST)
        if form.is_valid():
            logged_in = True
    else:
        form = LoginForm()
    return render(request, 'index.html', {'form': form, 'logged_in': logged_in})