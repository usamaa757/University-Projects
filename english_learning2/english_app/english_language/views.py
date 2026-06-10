# Django imports
from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.contrib.auth import login
from django.contrib.auth.views import LoginView, LogoutView
from django.contrib.auth.decorators import login_required, user_passes_test
from django.db.models import Count
from django.forms import modelformset_factory
from django.urls import reverse
from django.utils import timezone
from django.views.decorators.csrf import csrf_exempt
from django.utils.timezone import now, timedelta
from .decorators import teacher_required, student_required, admin_or_teacher_required, admin_required

# Third-party libs
import os, random, tempfile
import textstat
import language_tool_python
import string  
import pronouncing
import whisper
from difflib import SequenceMatcher

# Project imports

from .models import (
    User,
    Lesson, LessonMedia, LiveSession,
    Assignment, AssignmentSubmission,
    Room, Message,
    Quiz, Question, Choice, MatchingPair,
    SpeakingExercise, WritingExercise,
    QuizResult, SpeakingResult, WritingExerciseResult
)
from .forms import (
    UserRegisterForm, UserEditForm,
    LessonForm, LessonMediaForm, LiveSessionForm,
    AssignmentForm, SubmissionForm,
    RoomForm, MessageForm,
    QuizForm, QuestionForm, ChoiceForm,
    MatchingPairForm, MatchingPairFormSet,
    ChoiceFormSet, SpeakingExerciseForm, WritingExerciseForm
)

# User:
def index(request):
    return render(request, 'index.html')

def register(request):
    if request.method == "POST":
        form = UserRegisterForm(request.POST)
        if form.is_valid():
            user = form.save()
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


@teacher_required
def teacher_dashboard(request):
    assignments = Assignment.objects.filter(teacher=request.user)
    total_assignments = assignments.count()
    active_assignments = assignments.filter(due_date__gte=timezone.now()).count()
    expired_assignments = total_assignments - active_assignments

    upcoming = assignments.filter(due_date__gte=timezone.now()).order_by('due_date')[:5]

    context = {
        'total_assignments': total_assignments,
        'active_assignments': active_assignments,
        'expired_assignments': expired_assignments,
        'upcoming': upcoming
    }
    return render(request, 'teacher_dashboard.html', context)

@student_required
def student_dashboard(request):
    # Assignments still open
    open_assignments = Assignment.objects.filter(due_date__gte=timezone.now()).order_by('due_date')
    lessons = Lesson.objects.all()
    quizzes = Quiz.objects.filter(lesson__in=lessons)

    # Assignments already submitted by this student
    submitted_assignments = AssignmentSubmission.objects.filter(student=request.user)

    # Quizzes already taken by student (ids)
    taken_quiz_ids = QuizResult.objects.filter(student=request.user).values_list('quiz_id', flat=True)

    return render(request, 'student_dashboard.html', {
        'open_assignments': open_assignments,
        'submitted_assignments': submitted_assignments,
        'lessons': lessons,
        'quizzes': quizzes,
        'taken_quiz_ids': list(taken_quiz_ids),
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



@login_required
@admin_required
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

@login_required
@teacher_required
def student_list(request):
    if request.user.role != "teacher":  # only teachers can view
        messages.error(request, "You are not authorized to view students.")
        return redirect("login")

    students = User.objects.filter(role="student")
    return render(request, "student_list.html", {"students": students})


# Lesson:

@login_required
@teacher_required
def create_lesson(request):
    MediaFormSet = modelformset_factory(LessonMedia, form=LessonMediaForm, extra=2, can_delete=True)

    if request.method == "POST":
        lesson_form = LessonForm(request.POST)
        formset = MediaFormSet(request.POST, request.FILES, queryset=LessonMedia.objects.none())

        if lesson_form.is_valid() and formset.is_valid():
            lesson = lesson_form.save()

            for form in formset:
                if form.cleaned_data:
                    media = form.save(commit=False)
                    media.lesson = lesson
                    media.save()

            return redirect("lesson_detail", lesson.id)
    else:
        lesson_form = LessonForm()
        formset = MediaFormSet(queryset=LessonMedia.objects.none())

    return render(request, "create_lesson.html", {
        "lesson_form": lesson_form,
        "formset": formset
    })


def lesson_list(request):
    level = request.GET.get("level")
    category = request.GET.get("category")

    lessons = Lesson.objects.all()
    if level:
        lessons = lessons.filter(level=level)
    if category:
        lessons = lessons.filter(category=category)

    return render(request, "lesson_list.html", {
        "lessons": lessons,
        "levels": Lesson.LEVEL_CHOICES,
        "categories": Lesson.CATEGORY_CHOICES,
    })


@login_required
@teacher_required
def delete_lesson(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)

    if request.user.role != "teacher":  # prevent students
        messages.error(request, "You are not authorized to delete lessons.")
        return redirect("lesson_list")

    lesson.delete()
    messages.success(request, f"Lesson '{lesson.title}' deleted successfully.")
    return redirect("lesson_list")

def lesson_detail(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    return render(request, "lesson_detail.html", {
        "lesson": lesson,
        "media_files": lesson.media.all(),
    })
# Create your views here.
@login_required
@teacher_required
def create_live_session(request):
    if request.method == 'POST':
        form = LiveSessionForm(request.POST)
        if form.is_valid():
            session = form.save(commit=False)
            session.teacher = request.user
            session.save()
            return redirect('live_sessions_list')
    else:
        form = LiveSessionForm()
    return render(request, 'create_live_session.html', {'form': form})


def live_sessions_list(request):
    sessions = LiveSession.objects.filter(date_time__gte=timezone.now()).order_by('date_time')
    return render(request, 'live_sessions_list.html', {'sessions': sessions})


# Edit View
@login_required
@teacher_required
def edit_session(request, pk):
    session = get_object_or_404(LiveSession, pk=pk)
    if request.method == "POST":
        form = LiveSessionForm(request.POST, instance=session)
        if form.is_valid():
            form.save()
            return redirect('live_sessions_list')
    else:
        form = LiveSessionForm(instance=session)
    return render(request, 'edit_session.html', {'form': form})

# Delete View
@login_required
@teacher_required
def delete_session(request, pk):
    session = get_object_or_404(LiveSession, pk=pk)
    if request.method == "POST":
        session.delete()
        return redirect('live_sessions_list')
    return redirect('live_sessions_list')

# Exercise:


tool = language_tool_python.LanguageTool('en-US')  # English grammar checker
os.environ["PATH"] += os.pathsep + r"C:\ffmpeg\bin"
model = whisper.load_model("base")

@login_required
@teacher_required
def create_quiz(request):
    if request.method == "POST":
        form = QuizForm(request.POST)
        if form.is_valid():
            quiz = form.save()
            return redirect('add_questions', quiz_id=quiz.id)
    else:
        form = QuizForm()
    return render(request, 'create_quiz.html', {'form': form})

@login_required
@teacher_required
def add_questions(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)

    formset = None  # default

    if request.method == "POST":
        q_form = QuestionForm(request.POST)

        # Initialize formset only if quiz type requires it
        if quiz.quiz_type == "mcq":
            formset = ChoiceFormSet(request.POST, queryset=Choice.objects.none())
        elif quiz.quiz_type == "match":
            formset = MatchingPairFormSet(request.POST, queryset=MatchingPair.objects.none())

        if q_form.is_valid() and (formset is None or formset.is_valid()):
            # Save Question
            question = q_form.save(commit=False)
            question.quiz = quiz
            question.question_type = quiz.quiz_type
            question.save()

            # Save MCQ Choices
            if quiz.quiz_type == "mcq":
                correct_choice_index = request.POST.get("correct_choice")
                for idx, form in enumerate(formset):
                    if form.cleaned_data and not form.cleaned_data.get("DELETE", False):
                        choice = form.save(commit=False)
                        choice.question = question
                        choice.is_correct = (str(idx) == correct_choice_index)
                        choice.save()

            # Save Matching Pairs
            elif quiz.quiz_type == "match":
                for form in formset:
                    if form.cleaned_data and not form.cleaned_data.get("DELETE", False):
                        pair = form.save(commit=False)
                        pair.question = question
                        pair.save()

            # Fill-in-the-blank works automatically — 
            # the `correct_answer` comes directly from q_form.cleaned_data

            return redirect("add_questions", quiz.id)

    else:
        q_form = QuestionForm()
        if quiz.quiz_type == "mcq":
            formset = ChoiceFormSet(queryset=Choice.objects.none())
        elif quiz.quiz_type == "match":
            formset = MatchingPairFormSet(queryset=MatchingPair.objects.none())

    return render(request, "add_questions.html", {
        "quiz": quiz,
        "question_form": q_form,
        "formset": formset,
    })


@login_required
def quiz_list(request):
    quizzes = Quiz.objects.select_related("lesson").all()
    taken_quiz_ids = []
    quiz_results = {}

    if request.user.is_authenticated and request.user.role == "student":
        taken_quiz_ids = list(
            QuizResult.objects.filter(student=request.user).values_list("quiz_id", flat=True)
        )
        results = QuizResult.objects.filter(student=request.user)
        quiz_results = {r.quiz_id: r for r in results}

    # attach result to each quiz for easy template access
    for quiz in quizzes:
        quiz.result = quiz_results.get(quiz.id)

    return render(request, "quiz_list.html", {
        "quizzes": quizzes,
        "taken_quiz_ids": taken_quiz_ids,
        "today": now().date(),
    })

@login_required
@teacher_required
def delete_quiz(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)
    
    if request.user.role != "teacher":  # prevent students
        messages.error(request, "You are not authorized to delete quizzes.")
        return redirect("quiz_list")

    quiz.delete()
    messages.success(request, f"Quiz '{quiz.title}' deleted successfully.")
    return redirect("quiz_list")

@login_required
@student_required
def take_quiz(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)

    if request.method == "POST":
        score = 0
        total = 0
        for question in quiz.questions.all():
            total += 1

            if question.question_type == "mcq":
                user_answer = request.POST.get(f"question_{question.id}")
                if question.choices.filter(id=user_answer, is_correct=True).exists():
                    score += 1

            elif question.question_type == "fill":
                user_answer = request.POST.get(f"question_{question.id}", "").strip().lower()
                correct_answer = (question.correct_answer or "").strip().lower() 
                if user_answer == correct_answer and correct_answer != "":
                    score += 1

            elif question.question_type == "match":
                all_correct = True
                for pair in question.pairs.all():
                    user_answer = request.POST.get(f"pair_{pair.id}")
                    if user_answer != pair.right_item:
                        all_correct = False
                        break
                if all_correct:
                    score += 1

        if request.user.is_authenticated:
            QuizResult.objects.create(
                student=request.user,
                quiz=quiz,
                score=score,
                total=total
            )

        return render(request, "quiz_result.html", {"quiz": quiz, "score": score, "total": total})

    return render(request, 'take_quiz.html', {'quiz': quiz})

@login_required
def quiz_results_list(request):
    results = QuizResult.objects.select_related("student", "quiz__lesson").all()
    return render(request, "quiz_results_list.html", {"results": results})

@login_required
def my_quiz_results(request):
    results = QuizResult.objects.filter(student=request.user).select_related("quiz__lesson")
    return render(request, "my_quiz_results.html", {"results": results})

@login_required
def quiz_result_detail(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)
    result = get_object_or_404(QuizResult, quiz=quiz, student=request.user)

    return render(request, "quiz_result_detail.html", {
        "quiz": quiz,
        "result": result,
    })
    
@login_required
@teacher_required
def create_speaking_vocabulary(request):
    exercises = SpeakingExercise.objects.all()

    if request.method == "POST":
        form = SpeakingExerciseForm(request.POST)
        if form.is_valid():
            form.save()
            return redirect("create_speaking_vocabulary")
    else:
        form = SpeakingExerciseForm()

    return render(request, "create_speaking_vocabulary.html", {
        "form": form,
        "exercises": exercises
    })

@login_required
def speaking_exercise_list(request):
    exercises = SpeakingExercise.objects.all()
    return render(request, "speaking_exercise_list.html", {"exercises": exercises})



@csrf_exempt
def practice_speaking(request, pk):
    exercise = get_object_or_404(SpeakingExercise, pk=pk)
    transcript = None
    pronunciation_score = None
    matching_score = None



    def normalize_text(text):
        return text.lower().translate(str.maketrans("", "", string.punctuation)).split()

    def word_matching_score(expected, spoken):
        expected_words = normalize_text(expected)
        spoken_words = normalize_text(spoken)
        matches = sum(1 for w in expected_words if w in spoken_words)
        return matches / len(expected_words)

    def phoneme_score(expected_text, spoken_text):
        expected_words = normalize_text(expected_text)
        spoken_words = normalize_text(spoken_text)
        total_score = 0
        for e_word, s_word in zip(expected_words, spoken_words):
            e_phones = pronouncing.phones_for_word(e_word)
            s_phones = pronouncing.phones_for_word(s_word)
            if e_phones and s_phones:
                similarity = SequenceMatcher(None, e_phones[0], s_phones[0]).ratio()
                total_score += similarity
            else:
                total_score += 0
        return (total_score / len(expected_words)) * 100 if expected_words else 0

    if request.method == "POST" and request.FILES.get("audio_file"):
        audio_file = request.FILES["audio_file"]

        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp:
            for chunk in audio_file.chunks():
                tmp.write(chunk)
            tmp_path = tmp.name

        result = model.transcribe(tmp_path, language="en")
        transcript = result["text"].strip()

        # Matching score (word-based, ignores punctuation)
        matching_score = round(word_matching_score(exercise.text, transcript) * 100, 2)

        # Pronunciation score (phoneme comparison)
        pronunciation_score = round(phoneme_score(exercise.text, transcript), 2)

        os.remove(tmp_path)

        if request.user.is_authenticated:
            SpeakingResult.objects.create(
                exercise=exercise,
                student=request.user,
                audio_file=audio_file,
                transcript=transcript,
                feedback=f"Matching score: {matching_score}%, Pronunciation score: {pronunciation_score}%",
            )

    return render(request, "speaking.html", {
        "exercise": exercise,
        "transcript": transcript,
        "pronunciation_score": pronunciation_score,
        "matching_score": matching_score,
    })


@csrf_exempt
def check_pronunciation(request):
    score = None
    transcript = None
    expected_text = "The capital of France is Paris"

    if request.method == "POST" and request.FILES.get("audio_file"):
        audio_file = request.FILES["audio_file"]

        import tempfile, os
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp:
            for chunk in audio_file.chunks():
                tmp.write(chunk)
            tmp_path = tmp.name

        result = model.transcribe(tmp_path, language="en")
        transcript = result["text"].strip()

        similarity = SequenceMatcher(None, exercise.text.lower(), transcript.lower()).ratio()
        matching_score = round(similarity * 100, 2)

        # For now, use the same as matching_score for pronunciation (or calculate differently if needed)
        pronunciation_score = matching_score  

        return render(request, "speaking.html", {
            "exercise": exercise,
            "transcript": transcript,
            "pronunciation_score": pronunciation_score,
            "matching_score": matching_score,
        })


@login_required
@student_required
def writing_practice(request):
    essay = None
    matches = []
    corrected_text = None
    readability = None
    score = None
    selected_lesson = None

    form = WritingExerciseForm(request.POST or None)

    if request.method == "POST":
        essay = request.POST.get("essay", "").strip()

        if form.is_valid():
            selected_lesson = form.cleaned_data["lesson"]

        if essay and selected_lesson:
            # Grammar/Spell checks
            matches = tool.check(essay)
            corrected_text = tool.correct(essay)

            # Readability metrics
            readability = {
                "flesch_reading_ease": textstat.flesch_reading_ease(essay),
                "grade_level": textstat.flesch_kincaid_grade(essay),
                "word_count": textstat.lexicon_count(essay)
            }

            similarity = SequenceMatcher(None, essay.lower(), corrected_text.lower()).ratio()
            score = round(similarity * 100, 2)

            # Create or reuse exercise tied to lesson
            exercise, _ = WritingExercise.objects.get_or_create(
                lesson=selected_lesson,
                title=f"Practice for {selected_lesson.title}",
                prompt="Free writing"
            )

            WritingExerciseResult.objects.create(
                exercise=exercise,
                student=request.user,
                submission_text=essay,
                score=score,
                feedback=f"Auto-evaluated. Similarity to corrected text: {score}%"
            )

    return render(request, "writing_essay.html", {
        "essay": essay,
        "matches": matches,
        "readability": readability,
        "corrected_text": corrected_text,
        "score": score,
        "form": form,
        "lesson": selected_lesson,
    })



# Chat:



@login_required
@student_required
def create_room(request):
    if request.method == "POST":
        form = RoomForm(request.POST)
        if form.is_valid():
            room = form.save()
            return redirect("chat_room", room_name=room.name)  # redirect to new room
    else:
        form = RoomForm()
    return render(request, "create_room.html", {"form": form})

@login_required
@student_required
def room_list(request):
    rooms = Room.objects.all()
    return render(request, "room_list.html", {"rooms": rooms})

@login_required
@student_required
def chat_room(request, room_name):
    room = get_object_or_404(Room, name=room_name)
    messages = room.messages.all()
    rooms = Room.objects.all()  # 👈 get all rooms for sidebar

    if request.method == "POST":
        form = MessageForm(request.POST)
        if form.is_valid():
            msg = form.save(commit=False)
            msg.user = request.user
            msg.room = room
            msg.save()
            return redirect("chat_room", room_name=room.name)
    else:
        form = MessageForm()

    return render(request, "chat.html", {
        "room": room,
        "rooms": rooms,         
        "room_name": room.name,  
        "messages": messages,
        "form": form
    })
    
    
# Assignment:

@login_required
@teacher_required
def create_assignment(request):
    if request.method == 'POST':
        form = AssignmentForm(request.POST, request.FILES)
        if form.is_valid():
            assignment = form.save(commit=False)
            assignment.teacher = request.user
            assignment.save()
            return redirect('assignment_list')
    else:
        form = AssignmentForm()
    return render(request, 'create_assignment.html', {'form': form})


# def teacher_assignments(request):
#     assignments = (
#         Assignment.objects
#         .filter(teacher=request.user)
#         .annotate(submission_count=Count('assignmentsubmission'))
#         .order_by('-created_at')
#     )
#     return render(request, 'teacher_assignments.html', {'assignments': assignments})
@login_required
@teacher_required
def edit_assigment(request, pk):
    assignment = get_object_or_404(Assignment, pk=pk)

    if request.method == 'POST':
        new_date = request.POST.get('due_date')
        new_total_marks = request.POST.get('total_marks')
        
        if new_date:
            assignment.due_date = new_date

        if new_total_marks:
            assignment.total_marks = int(new_total_marks)

        assignment.save()
        return redirect('assignment_list')

    return render(request, 'edit_assigment.html', {'assignment': assignment})


@login_required
@student_required
def student_assignments(request):
    # Get all submissions by the current student
    submissions = AssignmentSubmission.objects.filter(
        student=request.user
    ).select_related('assignment').order_by('-assignment__due_date')

    return render(request, 'student_assignments.html', {
        'submissions': submissions
    })



@login_required
@student_required

def submit_assignment(request, assignment_id):
    assignment = get_object_or_404(Assignment, id=assignment_id)

    # check if the student already has a submission
    submission = AssignmentSubmission.objects.filter(
        assignment=assignment,
        student=request.user
    ).first()

    if request.method == 'POST':
        form = SubmissionForm(request.POST, request.FILES, instance=submission)
        if form.is_valid():
            submission = form.save(commit=False)
            submission.assignment = assignment
            submission.student = request.user
            submission.submitted_at = timezone.now()  # update submission time
            submission.save()
            messages.success(request, "✅ Assignment submitted successfully!")
            return redirect('assignment_list')
    else:
        form = SubmissionForm(instance=submission)

    return render(request, 'submit_assignment.html', {
        'form': form,
        'assignment': assignment,
        'submission': submission  # pass existing submission if any
    })

@login_required
@teacher_required
def view_submissions(request, assignment_id):
    assignment = get_object_or_404(Assignment, id=assignment_id)
    submissions = AssignmentSubmission.objects.filter(assignment=assignment)

    if request.method == 'POST':
        submission_id = request.POST.get("submission_id")
        marks = request.POST.get("marks")

        if submission_id and marks is not None:
            submission = get_object_or_404(AssignmentSubmission, id=submission_id)
            submission.marks = int(marks)
            submission.save()
            messages.success(request, f"Marks updated for {submission.student.username}.")
        return redirect('view_submissions', assignment_id=assignment.id)

    return render(request, 'view_submissions.html', {
        'assignment': assignment,
        'submissions': submissions
    })


@login_required
def assignment_list(request):
    submitted_ids = AssignmentSubmission.objects.filter(
        student=request.user
    ).values_list('assignment_id', flat=True)

    assignments = Assignment.objects.all().order_by('-due_date')

    context = {
        "assignments": assignments,
        "submitted_ids": list(submitted_ids),   # pass submitted IDs for student
    }
    return render(request, "assignment_list.html", context)

@login_required
@admin_required
def user_list(request):
    users = User.objects.exclude(role__iexact='admin').order_by('-date_joined')

    # Filtering
    role_filter = request.GET.get('role')
    if role_filter:
        users = users.filter(role=role_filter)
    
    search_query = request.GET.get('search')
    if search_query:
        users = users.filter(
            models.Q(username__icontains=search_query) |
            models.Q(email__icontains=search_query) |
            models.Q(first_name__icontains=search_query) |
            models.Q(last_name__icontains=search_query)
        )
    
    context = {
        'users': users,
        'role_filter': role_filter,
        'search_query': search_query,
    }
    return render(request, 'user_list.html', context)

@login_required
@admin_required
def user_detail(request, user_id):
    selected_user = get_object_or_404(User, id=user_id)
    return render(request, "user_detail.html", {"selected_user": selected_user})

@login_required
@admin_required
def user_edit(request, user_id):
    user = get_object_or_404(User, id=user_id)
    
    if request.method == 'POST':
        form = UserEditForm(request.POST, instance=user)
        if form.is_valid():
            form.save()
            messages.success(request, f'User {user.username} updated successfully.')
            return redirect('user_list')
    else:
        form = UserEditForm(instance=user)
    
    return render(request, 'user_edit.html', {'form': form, 'user': user})

@login_required
@admin_required
def user_delete(request, user_id):
    user = get_object_or_404(User, id=user_id)
    
    if request.method == 'POST':
        username = user.username
        user.delete()
        messages.success(request, f'User {username} deleted successfully.')
        return redirect('user_list')
    
    return render(request, 'user_confirm_delete.html', {'user': user})