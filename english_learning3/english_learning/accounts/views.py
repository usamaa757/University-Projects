from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login
from django.contrib.auth.views import LoginView
from .forms import UserRegisterForm, UserEditForm
from django.utils import timezone
from django.utils.timezone import now, timedelta
from accounts.decorators import role_required
from assignments.models import Assignment, AssignmentSubmission
from django.urls import reverse
from assignments.forms import AssignmentForm, SubmissionForm
from django.contrib.auth.views import LogoutView
from lessons.models import Lesson
from assignments.models import Assignment
from exercises.models import QuizResult, SpeakingExercise, WritingExerciseResult, Quiz, SpeakingResult, WritingExerciseResult
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from .models import User
from django.db.models import Q


def index(request):
    return render(request, 'index.html')

def register(request):
    if request.method == "POST":
        form = UserRegisterForm(request.POST)
        if form.is_valid():
            user = form.save()
            messages.success(request, f'Account created for {user.username}. You can now log in.')
            return redirect('login')
    else:
        form = UserRegisterForm()
    return render(request, 'register.html', {'form': form})

class CustomLoginView(LoginView):
    template_name = 'login.html'

    def get_success_url(self):
        user = self.request.user
        if user.role == 'admin':
            return reverse('admin_dashboard')
        elif user.role == 'teacher':
            return reverse('teacher_dashboard')
        else:
            return reverse('student_dashboard')

@role_required(['admin'])
def admin_dashboard(request):
    last_7_days = [now().date() - timedelta(days=i) for i in range(6, -1, -1)]
    labels = [day.strftime('%b %d') for day in last_7_days]
    data = [User.objects.filter(date_joined__date=day).count() for day in last_7_days]
    # Stats
    total_users = User.objects.count()
    total_admins = User.objects.filter(role='admin').count()
    total_teachers = User.objects.filter(role='teacher').count()
    total_students = User.objects.filter(role='student').count()

    total_lessons = Lesson.objects.count() if Lesson.objects.exists() else 0
    total_quizzes = Quiz.objects.count() if Quiz.objects.exists() else 0

    # List of recent users
    recent_users = User.objects.order_by('-date_joined').exclude(role__iexact='admin')[:5]

    context = {
        'total_users': total_users,
        'total_admins': total_admins,
        'total_teachers': total_teachers,
        'total_students': total_students,
        'total_lessons': total_lessons,
        'total_quizzes': total_quizzes,
        'recent_users': recent_users,
        'chart_labels': labels,
        'chart_data': data,
    }
    return render(request, 'admin_dashboard.html', context)

@role_required(['teacher'])
def teacher_dashboard(request):
    assignments = Assignment.objects.filter(teacher=request.user)
    total_assignments = assignments.count()
    active_assignments = assignments.filter(due_date__gte=timezone.now()).count()
    expired_assignments = total_assignments - active_assignments

    upcoming = assignments.filter(due_date__gte=timezone.now()).order_by('due_date')[:5]
    
    today = timezone.localdate()

    context = {
        'total_assignments': total_assignments,
        'active_assignments': active_assignments,
        'expired_assignments': expired_assignments,
        'upcoming': upcoming,
        'today': today
    }
    return render(request, 'teacher_dashboard.html', context)
        
        
@role_required(['student'])
def student_dashboard(request):
    # Assignments still open
    open_assignments = Assignment.objects.filter(due_date__gte=timezone.now()).order_by('due_date')
    lessons = Lesson.objects.all()
    quizzes = Quiz.objects.filter(lesson__in=lessons)

    # Assignments already submitted by this student
    submitted_assignments = AssignmentSubmission.objects.filter(student=request.user)

    # Quizzes already taken by student (ids)
    taken_quiz_ids = QuizResult.objects.filter(student=request.user).values_list('quiz_id', flat=True)

    # Current date for comparison
    today = timezone.localdate()

    return render(request, 'student_dashboard.html', {
        'open_assignments': open_assignments,
        'submitted_assignments': submitted_assignments,
        'lessons': lessons,
        'quizzes': quizzes,
        'taken_quiz_ids': list(taken_quiz_ids),
        'today': today,
    })

   
@login_required
def progress_report(request, student_id=None):
    if student_id:  
        student = get_object_or_404(User, id=student_id, role="student")
    else:
        
        student = request.user
        if not student.is_student():
            return redirect("login")  

    lessons_completed = Lesson.objects.filter(quizzes__results__student=student).distinct()
    quiz_results = QuizResult.objects.filter(student=student)
    assignment_results = AssignmentSubmission.objects.filter(student=student)
    speaking_results = SpeakingResult.objects.filter(student=student)
    writing_results = WritingExerciseResult.objects.filter(student=student)

    for result in quiz_results:
        total = result.quiz.questions.count()  # adjust to your model
        if total:
            result.percentage = (result.score / total) * 100
        else:
            result.percentage = 0

    for result in assignment_results:
        total = result.assignment.total_marks
        if total and result.marks is not None:
            result.percentage = (result.marks / total) * 100
        else:
            result.percentage = 0

    return render(request, "progress_report.html", {
        "student": student,
        "lessons_completed": lessons_completed,
        "quiz_results": quiz_results,
        "assignment_results": assignment_results,
        "speaking_results": speaking_results,
        "writing_results": writing_results,
    })


def student_list(request):
    if request.user.role != "teacher":  # only teachers can view
        messages.error(request, "You are not authorized to view students.")
        return redirect("login")

    students = User.objects.filter(role="student")
    return render(request, "student_list.html", {"students": students})

@login_required
@role_required(['admin'])

def user_list(request):
    selected_user = User.objects.exclude(role__iexact='admin').order_by('-date_joined')

    # Filtering
    role_filter = request.GET.get('role')
    if role_filter:
        selected_user = selected_user.filter(role=role_filter)
    
    search_query = request.GET.get('search')
    if search_query:
        selected_user = selected_user.filter(
            Q(username__icontains=search_query) |
            Q(email__icontains=search_query)
        )
    
    context = {
        'selected_user': selected_user,
        'role_filter': role_filter,
        'search_query': search_query,
    }
    return render(request, 'user_list.html', context)


@login_required
@role_required(['admin'])

def user_detail(request, user_id):
    selected_user = get_object_or_404(User, id=user_id)
    return render(request, "user_detail.html", {"selected_user": selected_user})

@login_required
@role_required(['admin'])
def user_edit(request, user_id):
    selected_user = get_object_or_404(User, id=user_id)

    if request.method == 'POST':
        form = UserEditForm(request.POST, instance=selected_user)
        if form.is_valid():
            selected_user.username = form.cleaned_data['username']
            selected_user.email = form.cleaned_data['email']
            selected_user.is_active = form.cleaned_data['is_active']

            new_password = form.cleaned_data.get("password")
            if new_password:
                selected_user.set_password(new_password)  # ✅ only update if provided

            selected_user.save()
            messages.success(request, f'User {selected_user.username} updated successfully.')
            return redirect('user_list')
    else:
        form = UserEditForm(instance=selected_user)

    return render(request, 'user_edit.html', {'form': form, 'selected_user': selected_user})


@login_required
@role_required(['admin'])

def user_delete(request, user_id):
    user = get_object_or_404(User, id=user_id)
    
    if request.method == 'POST':
        username = user.username
        user.delete()
        messages.success(request, f'User {username} deleted successfully.')
        return redirect('user_list')
    
    return render(request, 'user_confirm_delete.html', {'user': user})