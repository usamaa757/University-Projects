from django.shortcuts import render, redirect
from .models import Patient
from .forms import PatientForm
from django.contrib.auth.decorators import login_required



@login_required
def add_patient(request):
    if request.method == "POST":
        form = PatientForm(request.POST)
        if form.is_valid():
            form.save()
            return redirect('patient_list')
    else:
        form = PatientForm()
    return render(request, 'add_patient.html', {'form': form})


@login_required
def patient_list(request):
    if request.user.role not in ["admin", "operator"]:
        messages.error(request, "Access denied! You do not have permission to view this page.")
        return redirect('dashboard')  

    patients = Patient.objects.all()
    return render(request, 'patient_list.html', {'patients': patients})