from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth import login, authenticate, logout
from django.contrib import messages
from django.contrib.auth.decorators import login_required
from accounts.forms import CustomUserCreationForm, UserUpdateForm
from django.contrib.auth import get_user_model
from django.db.models import Count, Avg
from django.utils import timezone
from datetime import timedelta
User = get_user_model()
from django.utils import timezone
from datetime import timedelta
from django.http import HttpResponseForbidden
from learning.models import (
    Course,
    Lesson,
    Exercise,
    Quiz,
    Assignment,
    AssignmentSubmission,
    QuizSubmission,
    SpeakingSubmission,
    WritingSubmission,
    ListeningSubmission,
    ListeningExercise,
    SpeakingExercise,
    WritingExercise,
    LiveSession,
    StudentProgress,
    ForumTopic,
    UserBadge,
    UserStreak,
    Badge,
    UserPoints
    
)

def signup_view(request):
    if request.method == 'POST':
        form = CustomUserCreationForm(request.POST)
        if form.is_valid():
            user = form.save()
            email = form.cleaned_data.get('email')
            raw_password = form.cleaned_data.get('password1')
            user = authenticate(request, email=email, password=raw_password)
            if user:
                login(request, user)
                return redirect('accounts:dashboard_redirect')
    else:
        form = CustomUserCreationForm()
    return render(request, 'accounts/signup.html', {'form': form})


def login_view(request):
    if request.method == "POST":
        email = request.POST.get("email")
        password = request.POST.get("password")
        user = authenticate(request, email=email, password=password)

        if user is not None:
            login(request, user)
            return redirect("accounts:dashboard_redirect")  # ✅ namespace added
        else:
            messages.error(request, "Invalid email or password")

    return render(request, "accounts/login.html")

@login_required
def logout_view(request):
    logout(request)
    return redirect('accounts:login')  # ✅ namespace added

@login_required
def dashboard_redirect(request):
    user = request.user

    if user.is_superuser or user.role == "ADMIN":
        return redirect("accounts:admin_dashboard")
    elif user.role == "TEACHER":
        return redirect("accounts:teacher_dashboard")
    elif user.role == "STUDENT":
        return redirect("accounts:student_dashboard")
    else:
        return redirect("accounts:login")




@login_required
def admin_dashboard(request):
    # Ensure only admins can access this
    if request.user.role != User.Roles.ADMIN:
        return HttpResponseForbidden("Access denied. Admins only.")
    
    # User statistics
    total_users = User.objects.count()
    student_count = User.objects.filter(role=User.Roles.STUDENT).count()
    teacher_count = User.objects.filter(role=User.Roles.TEACHER).count()
    admin_count = User.objects.filter(role=User.Roles.ADMIN).count()
    live_session_count = LiveSession.objects.count()
    
    # New users this week
    week_ago = timezone.now() - timedelta(days=7)
    new_users_this_week = User.objects.filter(date_joined__gte=week_ago).count()
    
    # Course statistics
    total_courses = Course.objects.count()
    total_lessons = Lesson.objects.count()
    active_courses = Course.objects.annotate(lesson_count=Count('lessons')).filter(lesson_count__gt=0).count()
    
    # Courses by level
    courses_by_level = {
        'BEGINNER': Course.objects.filter(skill_level='BEGINNER').count(),
        'INTERMEDIATE': Course.objects.filter(skill_level='INTERMEDIATE').count(),
        'ADVANCED': Course.objects.filter(skill_level='ADVANCED').count(),
    }
    
    # Average lessons per course
    avg_lessons_per_course = round(total_lessons / max(1, total_courses), 1)
    
    # Most popular category
    from collections import Counter
    category_counts = Counter(Course.objects.values_list('category', flat=True))
    popular_category = category_counts.most_common(1)[0][0] if category_counts else 'N/A'
    
    # Submission statistics
    quiz_submissions = QuizSubmission.objects.count()
    assignment_submissions = AssignmentSubmission.objects.count()
    speaking_submissions = SpeakingSubmission.objects.count()
    writing_submissions = WritingSubmission.objects.count()
    listening_submissions = ListeningSubmission.objects.count()
    
    total_submissions = (
        quiz_submissions + assignment_submissions + 
        speaking_submissions + writing_submissions + listening_submissions
    )
    
    # Pending submissions (ungraded)
    pending_submissions = (
        AssignmentSubmission.objects.filter(marks_awarded__isnull=True).count() +
        SpeakingSubmission.objects.filter(score__isnull=True).count() +
        WritingSubmission.objects.filter(score__isnull=True).count() +
        ListeningSubmission.objects.filter(score__isnull=True).count()
    )
    
    # Completed lessons (approximation)
    completed_lessons = QuizSubmission.objects.values('student').distinct().count()
    
    # Active live sessions
    active_sessions = 0  # You can implement this based on your LiveSession model
    
    # Recent activities
    recent_activities = [
        {
            'icon': 'user-plus',
            'title': f'{new_users_this_week} new users joined this week',
            'time': 'This week'
        },
        {
            'icon': 'book',
            'title': f'{Course.objects.filter(created_at__gte=week_ago).count()} new courses created',
            'time': 'This week'
        },
        {
            'icon': 'tasks',
            'title': f'{total_submissions} total submissions received',
            'time': 'All time'
        },
        {
            'icon': 'chart-line',
            'title': f'{student_count} active students learning',
            'time': 'Current'
        }
    ]
    
    context = {
        # User stats
        'total_users': total_users,
        'student_count': student_count,
        'teacher_count': teacher_count,
        'admin_count': admin_count,
        'new_users_this_week': new_users_this_week,
        
        # Course stats
        'total_courses': total_courses,
        'total_lessons': total_lessons,
        'active_courses': active_courses,
        'courses_by_level': courses_by_level,
        'avg_lessons_per_course': avg_lessons_per_course,
        'popular_category': popular_category,
        
        # Submission stats
        'total_submissions': total_submissions,
        'pending_submissions': pending_submissions,
        'completed_lessons': completed_lessons,
        'active_sessions': active_sessions,
        
        # Activity
        'recent_activities': recent_activities,
        'live_session_count': live_session_count,
    }
    
    return render(request, 'accounts/admin_dashboard.html', context)
    
def manage_content(request):
    courses = Course.objects.all()
    lessons = Lesson.objects.all()
    return render(request, "accounts/manage_content.html", {
        "courses": courses,
        "lessons": lessons
    })
    

# ---------------- LIST USERS ----------------
@login_required
def user_list(request):
    if not request.user.is_superuser:  # ✅ only superuser can access
        return redirect("accounts:dashboard_redirect")

    users = User.objects.all()
    return render(request, "accounts/user_list.html", {"users": users})


# ---------------- CREATE USER ----------------
@login_required
def user_create(request):
    if not request.user.is_superuser:  # ✅ only superuser can access
        return redirect("accounts:dashboard_redirect")

    if request.method == "POST":
        form = CustomUserCreationForm(request.POST)
        if form.is_valid():
            form.save()
            return redirect("accounts:user_list")
    else:
        form = CustomUserCreationForm()

    return render(request, "accounts/user_form.html", {"form": form})


# ---------------- EDIT USER ----------------
@login_required
def user_edit(request, user_id):
    if not request.user.is_superuser:  # ✅ only superuser can access
        return redirect("accounts:dashboard_redirect")

    user = get_object_or_404(User, id=user_id)

    if request.method == "POST":
        form = UserUpdateForm(request.POST, instance=user)
        if form.is_valid():
            form.save()
            return redirect("accounts:user_list")
    else:
        form = UserUpdateForm(instance=user)

    return render(request, "accounts/user_form.html", {"form": form})


# ---------------- DELETE USER ----------------
@login_required
def user_delete(request, user_id):
    if not request.user.is_superuser:  # ✅ only superuser can access
        return redirect("accounts:dashboard_redirect")

    user = get_object_or_404(User, id=user_id)

    if request.method == "POST":  # confirm delete
        user.delete()
        return redirect("accounts:user_list")

    return render(request, "accounts/user_confirm_delete.html", {"user": user})




@login_required
def analytics_view(request):
    if not request.user.is_superuser:
        return redirect("accounts:dashboard_redirect")

    total_exercises = (
        Quiz.objects.count()
        + SpeakingExercise.objects.count()
        + WritingExercise.objects.count()
        + ListeningExercise.objects.count()
    )

    data = {
        "total_users": User.objects.count(),
        "total_teachers": User.objects.filter(role="TEACHER").count(),
        "total_students": User.objects.filter(role="STUDENT").count(),
        "total_courses": Course.objects.count(),
        "total_lessons": Lesson.objects.count(),
        "total_exercises": total_exercises,
    }

    return render(request, "accounts/analytics.html", {"data": data})



@login_required
def teacher_dashboard(request):
    # Ensure only teachers can access this
    if request.user.role != User.Roles.TEACHER:
        return HttpResponseForbidden("Access denied. Teachers only.")
    
    # Statistics
    total_students = User.objects.filter(role=User.Roles.STUDENT).count()
    my_courses = Course.objects.filter(created_by=request.user)
    live_sessions = Course.objects.filter(created_by=request.user)
    my_courses_count = my_courses.count()
  
    
    # Pending submissions (ungraded)
    pending_submissions = (
        AssignmentSubmission.objects.filter(marks_awarded__isnull=True).count() +
        SpeakingSubmission.objects.filter(score__isnull=True).count() +
        WritingSubmission.objects.filter(score__isnull=True).count() +
        ListeningSubmission.objects.filter(score__isnull=True).count()
    )
    
    # Recent submissions (last 7 days)
    recent_submissions = []
    
    # Add recent assignment submissions
    recent_assignments = AssignmentSubmission.objects.filter(
        submitted_at__gte=timezone.now() - timedelta(days=7)
    )[:5]
    for submission in recent_assignments:
        recent_submissions.append({
            'student': submission.student,
            'exercise_title': submission.assignment.title,
            'submitted_at': submission.submitted_at,
            'submission_type': 'ASSIGNMENT'
        })
    
    # Add recent quiz submissions
    recent_quizzes = QuizSubmission.objects.filter(
        submitted_at__gte=timezone.now() - timedelta(days=7)
    )[:3]
    for submission in recent_quizzes:
        recent_submissions.append({
            'student': submission.student,
            'exercise_title': submission.quiz.title,
            'submitted_at': submission.submitted_at,
            'submission_type': 'QUIZ'
        })
    
    # Sort by submission time
    now = timezone.now()
    recent_submissions.sort(key=lambda x: x['submitted_at'], reverse=True)
    recent_submissions = recent_submissions[:5]  # Limit to 5 most recent
    
    # Live Sessions - upcoming (future sessions)
    upcoming_sessions = LiveSession.objects.filter(
        host=request.user,
        scheduled_at__gte=now
    ).count()
    
    # Get today's upcoming sessions for display
    today_start = now.replace(hour=0, minute=0, second=0, microsecond=0)
    today_end = today_start + timedelta(days=1)
    
    todays_sessions = LiveSession.objects.filter(
        host=request.user,
        scheduled_at__gte=today_start,
        scheduled_at__lt=today_end
    ).order_by('scheduled_at')
    
    # Get all upcoming sessions for the quick actions
    all_upcoming_sessions = LiveSession.objects.filter(
        host=request.user,
        scheduled_at__gte=now
    ).order_by('scheduled_at')[:5]
    
    context = {
        'total_students': total_students,
        'my_courses_count': my_courses_count,
        'my_courses': my_courses[:3],  # Show only 3 recent courses
        'pending_submissions': pending_submissions,
        'recent_submissions': recent_submissions,
        'upcoming_sessions': upcoming_sessions,
        'todays_sessions': todays_sessions,
        'all_upcoming_sessions': all_upcoming_sessions,
        'recent_submissions': recent_submissions,
        'now': now,    }
    
    return render(request, 'accounts/teacher_dashboard.html', context)

@login_required
def manage_assignments(request):
    return render(request, "accounts/manage_assignments.html")

@login_required
def student_list_view(request):
    """List all students in the system (for teachers only)."""
    if request.user.role == "STUDENT" :
        return render(request, "403.html", status=403)

    # ✅ Fetch all users with role = STUDENT
    students = User.objects.filter(role="STUDENT")

    context = {"students": students}
    return render(request, "accounts/student_list.html", context)


@login_required
def live_sessions(request):
    return render(request, "accounts/live_sessions.html")


@login_required
def student_dashboard(request):
    student = request.user
    now = timezone.now()

    enrolled_courses = Course.objects.all()

    # Course progress
    for course in enrolled_courses:
        total_lessons = course.lessons.count()
        completed_lessons = StudentProgress.objects.filter(
            student=student,
            course=course,
            status='COMPLETED'
        ).values('lesson').distinct().count()

        course.progress = round((completed_lessons / total_lessons) * 100) if total_lessons else 0

    total_lessons = Lesson.objects.count()
    completed_lessons = StudentProgress.objects.filter(
        student=student,
        status='COMPLETED'
    ).values('lesson').distinct().count()

    completed_courses = sum(
        1 for course in enrolled_courses
        if course.lessons.count() > 0 and
        StudentProgress.objects.filter(
            student=student,
            course=course,
            status='COMPLETED'
        ).values('lesson').distinct().count() == course.lessons.count()
    )

    pending_assignments = Assignment.objects.filter(
        due_date__gte=now.date()
    ).exclude(submissions__student=student).order_by('due_date')

    overdue_assignments = Assignment.objects.filter(
        due_date__lt=now.date()
    ).exclude(submissions__student=student).count()

    overall_progress = round(
        sum(course.progress for course in enrolled_courses) / enrolled_courses.count()
    ) if enrolled_courses else 0

    quiz_scores = QuizSubmission.objects.filter(student=student).aggregate(avg=Avg('score'))['avg'] or 0
    assignment_scores = AssignmentSubmission.objects.filter(student=student).aggregate(avg=Avg('marks_awarded'))['avg'] or 0
    speaking_scores = SpeakingSubmission.objects.filter(student=student).aggregate(avg=Avg('score'))['avg'] or 0
    writing_scores = WritingSubmission.objects.filter(student=student).aggregate(avg=Avg('score'))['avg'] or 0
    listening_scores = ListeningSubmission.objects.filter(student=student).aggregate(avg=Avg('score'))['avg'] or 0

    scores = [s for s in [quiz_scores, assignment_scores, speaking_scores, writing_scores, listening_scores] if s > 0]
    average_score = round(sum(scores) / len(scores)) if scores else 0

    quiz_progress = {'completed': QuizSubmission.objects.filter(student=student).count(), 'total': Quiz.objects.count()}
    writing_progress = {'completed': WritingSubmission.objects.filter(student=student).count(), 'total': WritingExercise.objects.count()}
    speaking_progress = {'completed': SpeakingSubmission.objects.filter(student=student).count(), 'total': SpeakingExercise.objects.count()}

    upcoming_sessions = LiveSession.objects.filter(scheduled_at__gte=now).order_by('scheduled_at')[:3]

    # ✅ Gamification
    user_points, _ = UserPoints.objects.get_or_create(user=student)
    total_points = user_points.total_points

    user_badges = UserBadge.objects.filter(user=student)
    badge_count = user_badges.count()

    streak = UserStreak.objects.filter(user=student).first()
    current_streak = streak.current_streak if streak else 0

    context = {
        'enrolled_courses': enrolled_courses,
        'total_lessons': total_lessons,
        'completed_lessons': completed_lessons,
        'completed_courses': completed_courses,
        'pending_assignments': pending_assignments,
        'overdue_assignments': overdue_assignments,
        'overall_progress': overall_progress,
        'average_score': average_score,
        'quiz_progress': quiz_progress,
        'writing_progress': writing_progress,
        'speaking_progress': speaking_progress,
        'upcoming_sessions': upcoming_sessions,
        'total_points': total_points,
        'badge_count': badge_count,
        'current_streak': current_streak,
        'badges': user_badges,
    }

    return render(request, 'accounts/student_dashboard.html', context)

@login_required
def student_lessons(request):
    # Fetch all courses and prefetch lessons to reduce DB queries
    courses = Course.objects.prefetch_related("lessons").all()

    context = {
        "courses": courses,
        "user": request.user,  # Optional: pass current user if needed in template
    }

    return render(request, "learning/lesson_list.html", context)

@login_required
def student_assignments(request):
    # Fetch all lessons (or filter based on student enrollment if applicable)
    lessons = Lesson.objects.prefetch_related('assignments').all()

    # Fetch all submissions by the current student
    submissions = AssignmentSubmission.objects.filter(student=request.user)
    submissions_dict = {sub.assignment_id: sub for sub in submissions}

    context = {
        'lessons': lessons,
        'submissions': submissions_dict,
    }

    return render(request, "learning/assignment_list.html", context)

@login_required
def forum_list(request):
    topics = ForumTopic.objects.all().order_by('-created_at')
    return render(request, 'learning/forum_list.html', {'topics': topics})


def student_lesson_detail(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)

    embed_url = None
    if lesson.video_url:
        url = lesson.video_url.strip()
        if "watch?v=" in url:
            video_id = url.split("watch?v=")[-1].split("&")[0]
            embed_url = f"https://www.youtube.com/embed/{video_id}"
        elif "youtu.be/" in url:
            video_id = url.split("youtu.be/")[-1].split("?")[0]
            embed_url = f"https://www.youtube.com/embed/{video_id}"
        elif "embed/" in url:
            embed_url = url  # already embed
    lesson.embed_url = embed_url

    return render(request, "accounts/student_lesson_detail.html", {"lesson": lesson})
