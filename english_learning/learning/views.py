from .decorators import teacher_required
from django.shortcuts import render, get_object_or_404, redirect
from django.http import HttpResponse, JsonResponse, HttpResponseForbidden
from django.contrib.auth.decorators import login_required
from django.forms import inlineformset_factory
from .models import *
from .forms import *
from django.contrib import messages
from django.utils import timezone
from datetime import date
import whisper
from fuzzywuzzy import fuzz
from django.views.decorators.csrf import csrf_exempt
from django.db.models import Avg
import difflib
from django.http import HttpResponse
from reportlab.lib.pagesizes import letter, A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer
from reportlab.lib.units import inch
from reportlab.lib import colors
import language_tool_python
from django.contrib.auth import get_user_model
from datetime import datetime, timedelta
User = get_user_model()

tool = language_tool_python.LanguageTool('en-US')
# Course list
def course_list(request):
    courses = Course.objects.all()
    return render(request, 'learning/course_list.html', {'courses': courses})


@login_required
@teacher_required
def create_course(request):
    if request.method == "POST":
        form = CourseForm(request.POST)
        if form.is_valid():
            course = form.save(commit=False)
            course.created_by = request.user
            course.save()
            return redirect('learning:course_list')
    else:
        form = CourseForm()
    return render(request, 'learning/course_form.html', {'form': form})

@teacher_required
def edit_course(request, pk):
    course = get_object_or_404(Course, pk=pk)
    form = CourseForm(request.POST or None, instance=course)
    if form.is_valid():
        form.save()
        return redirect('learning:course_list')
    return render(request, 'learning/course_form.html', {'form': form})

@teacher_required
def delete_course(request, pk):
    course = get_object_or_404(Course, pk=pk)
    if request.method == "POST":
        course.delete()
        return redirect('learning:course_list')
    return render(request, 'learning/course_confirm_delete.html', {'course': course})


# List all lessons of a course
def lesson_list(request, course_id):
    course = get_object_or_404(Course, id=course_id)
    lessons = course.lessons.all().order_by("order")

    # fix video URLs for embedding
    # fix video URLs for embedding
    import re

    YOUTUBE_REGEX = r"(?:v=|youtu\.be/|embed/)([A-Za-z0-9_-]{11})"

    for lesson in lessons:
        lesson.embed_url = None

        if lesson.video_url:
            url = lesson.video_url.strip()

            # Extract clean video ID using regex
            match = re.search(YOUTUBE_REGEX, url)
            if match:
                video_id = match.group(1)
                lesson.embed_url = f"https://www.youtube.com/embed/{video_id}"

            elif "embed/" in url:
                lesson.embed_url = url
            else:
                lesson.embed_url = None
        else:
            lesson.embed_url = None

    return render(request, "learning/lesson_list.html", {"course": course, "lessons": lessons})


# Create a lesson
@teacher_required
def lesson_create(request, course_id):
    course = get_object_or_404(Course, id=course_id)
    if request.method == "POST":
        form = LessonForm(request.POST, request.FILES)
        if form.is_valid():
            lesson = form.save(commit=False)
            lesson.course = course
            lesson.save()
            return redirect("learning:lesson_list", course_id=course.id)

    else:
        form = LessonForm()
    return render(request, "learning/lesson_form.html", {"form": form, "course": course})

@login_required
@teacher_required
def create_live_session(request):
    if request.user.role not in ['TEACHER', 'ADMIN'] and not request.user.is_superuser:
        return redirect('accounts:dashboard')  # Only teachers/admin

    if request.method == 'POST':
        form = LiveSessionForm(request.POST)
        if form.is_valid():
            live_session = form.save(commit=False)
            live_session.host = request.user
            live_session.save()
            return redirect('learning:live_sessions')
    else:
        form = LiveSessionForm()

    return render(request, 'learning/create_live_session.html', {'form': form})

@login_required
def live_sessions(request):
    now = timezone.now()

    if request.user.role == 'TEACHER':
        sessions = LiveSession.objects.filter(
            host=request.user,
            scheduled_at__gte=now
        )
    else:
        sessions = LiveSession.objects.filter(
            scheduled_at__gte=now
        )

    sessions = sessions.order_by('scheduled_at')

    return render(request, 'learning/live_sessions.html', {
        'sessions': sessions
    })
    
# Edit a lesson
@teacher_required
def lesson_edit(request, course_id, lesson_id):
    course = get_object_or_404(Course, id=course_id)
    lesson = get_object_or_404(Lesson, id=lesson_id, course=course)
    if request.method == "POST":
        form = LessonForm(request.POST, request.FILES, instance=lesson)
        if form.is_valid():
            form.save()
            return redirect("learning:lesson_list", course_id=course.id)

    else:
        form = LessonForm(instance=lesson)
    return render(request, "learning/lesson_form.html", {"form": form, "course": course})

# Delete a lesson
@teacher_required
def lesson_delete(request, course_id, lesson_id):
    course = get_object_or_404(Course, id=course_id)
    lesson = get_object_or_404(Lesson, id=lesson_id, course=course)
    if request.method == "POST":
        lesson.delete()
        return  redirect("learning:lesson_list", course_id=course.id) 
    return render(request, "learning/lesson_confirm_delete.html", {"lesson": lesson, "course": course})

def lesson_detail(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)

    # Preload exercises
    quizzes = lesson.quizzes.all()
    writing_exercises = lesson.writing_exercises.all()
    speaking_exercises = lesson.speaking_exercises.all()
    listening_exercises = lesson.listening_exercises.all()

    exercises_by_type = [
        {'name': 'Quizzes', 'list': quizzes},
        {'name': 'Writing Exercises', 'list': writing_exercises},
        {'name': 'Speaking Exercises', 'list': speaking_exercises},
        {'name': 'Listening Exercises', 'list': listening_exercises},
    ]

    context = {
        'lesson': lesson,
        'exercises_by_type': exercises_by_type,
    }
    return render(request, 'learning/lesson_detail.html', context)


whisper_model = whisper.load_model("base")


@login_required
def exercise_detail(request, lesson_id, exercise_type, exercise_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    exercise_type = exercise_type.lower()
    context = {"lesson": lesson, "exercise_type": exercise_type}

    # Helper functions for gamification
    def award_points(user, points):
        # Update points
        user_points, _ = UserPoints.objects.get_or_create(user=user)
        user_points.total_points += points
        user_points.save()

        # Update streak
        streak, _ = UserStreak.objects.get_or_create(user=user)
        today = date.today()

        if streak.last_active == today:
            # Already submitted today; no change
            return
        elif streak.last_active == today - timedelta(days=1):
            # Consecutive day
            streak.current_streak += 1
            streak.max_streak = max(streak.max_streak, streak.current_streak)
        else:
            # Missed a day
            streak.current_streak = 1
        streak.last_active = today
        streak.save()

    def check_badges(user):
        user_points, _ = UserPoints.objects.get_or_create(user=user)

        badge_rules = {
            100: "Rising Star",
            500: "Super Learner",
        }

        for points, badge_name in badge_rules.items():
            if user_points.total_points >= points:
                badge = Badge.objects.filter(name=badge_name).first()
                if badge:
                    UserBadge.objects.get_or_create(user=user, badge=badge)


    # ================= SPEAKING =================
    if exercise_type == "speak":
        exercise = get_object_or_404(SpeakingExercise, id=exercise_id)
        context["exercise"] = exercise

        if request.method == "POST":
            audio_file = request.FILES.get("audio")
            if audio_file:
                submission, _ = SpeakingSubmission.objects.update_or_create(
                    student=request.user,
                    exercise=exercise,
                    defaults={"audio_file": audio_file},
                )

                result = whisper_model.transcribe(submission.audio_file.path)
                transcript = result["text"].strip()
                score = fuzz.ratio(transcript.lower(), exercise.prompt.lower())

                submission.score = score
                submission.feedback = f"Transcript: {transcript}"
                submission.save()

                # 🎮 GAMIFICATION
                award_points(request.user, score // 2)
                check_badges(request.user)

                messages.success(request, "Recording submitted successfully!")
                return redirect('learning:exercise_detail', lesson.id, 'speak', exercise.id)

        context["submission"] = SpeakingSubmission.objects.filter(
            student=request.user, exercise=exercise
        ).first()

    # ================= QUIZ =================
    elif exercise_type == "quiz":
        exercise = get_object_or_404(Quiz, id=exercise_id)
        questions = exercise.questions.prefetch_related("choices").all()

        submission = QuizSubmission.objects.filter(
            student=request.user, quiz=exercise
        ).first()

        user_answers = submission.answers if submission else {}

        context.update({
            "exercise": exercise,
            "questions": questions,
            "submission": submission,
        })

        if request.method == "POST":
            total_score = 0
            user_answers = {}

            for q in questions:
                answer = request.POST.get(f"question_{q.id}")
                if answer:
                    user_answers[str(q.id)] = answer
                    if q.question_type == "MCQ":
                        try:
                            choice = Choice.objects.get(id=answer, question=q)
                            if choice.is_correct:
                                total_score += 1
                        except:
                            pass
                    elif q.question_type == "FILL":
                        if answer.lower().strip() == q.correct_answer.lower().strip():
                            total_score += 1

            QuizSubmission.objects.update_or_create(
                student=request.user,
                quiz=exercise,
                defaults={"score": total_score, "answers": user_answers},
            )

            # 🎮 GAMIFICATION
            award_points(request.user, total_score * 10)
            check_badges(request.user)

            messages.success(
                request, f"Quiz submitted! Score {total_score}/{questions.count()}"
            )
            return redirect('learning:exercise_detail', lesson.id, 'quiz', exercise.id)

    # ================= WRITING =================
    elif exercise_type in ["write", "writing"]:
        exercise = get_object_or_404(WritingExercise, id=exercise_id)

        # Check if this is an AJAX request for grammar checking
        if request.headers.get('X-Requested-With') == 'XMLHttpRequest':
            content = request.POST.get('content', '')

            # Count words in student's submission
            submitted_words = len(content.split())

            # Count words in exercise prompt
            exercise_words = len(exercise.prompt.split()) if exercise.prompt else 1

            # Simple scoring: percentage of words written relative to exercise
            score = min(100, int((submitted_words / exercise_words) * 100))

            # Placeholder: you can integrate grammar/spell checking here
            matches = []

            return JsonResponse({
                'score': score,
                'matches': matches
            })

        # Normal POST submission
        if request.method == "POST" and not request.headers.get('X-Requested-With') == 'XMLHttpRequest':
            content = request.POST.get("content", "").strip()
            if content:
                submission, created = WritingSubmission.objects.update_or_create(
                    student=request.user,
                    exercise=exercise,
                    defaults={"content": content},
                )

                # Scoring based on word count vs exercise length
                submitted_words = len(content.split())
                exercise_words = len(exercise.prompt.split()) if exercise.prompt else 1
                score = min(100, int((submitted_words / exercise_words) * 100))
                submission.score = score
                submission.save()

                # 🎮 GAMIFICATION: award points based on score
                award_points(request.user, score // 5)
                check_badges(request.user)

                messages.success(request, f"Writing submitted successfully! Score: {score}%")
                return redirect('learning:exercise_detail', lesson.id, 'write', exercise.id)

        # Load existing submission
        existing_submission = WritingSubmission.objects.filter(
            student=request.user, exercise=exercise
        ).first()

        context.update({
            "exercise": exercise,
            "submission": existing_submission,
            "initial_text": existing_submission.content if existing_submission else ""
        })


    # ================= LISTENING =================
    elif exercise_type in ["listen", "listening"]:
        exercise = get_object_or_404(ListeningExercise, id=exercise_id)

        if request.method == "POST":
            total_score = 0
            answers = {}

            ans = request.POST.get("answer", "").strip()
            answers['answer'] = ans
            if ans.lower() == (exercise.correct_answer or "").lower():
                total_score += 1

            ListeningSubmission.objects.update_or_create(
                exercise=exercise,
                student=request.user,
                defaults={"answer": ans, "score": total_score},
            )

            # 🎮 GAMIFICATION
            award_points(request.user, total_score * 5)
            check_badges(request.user)

            messages.success(
                request, f"Listening submitted! Score {total_score}/1"
            )
            return redirect('learning:exercise_detail', lesson.id, 'listen', exercise.id)

        context.update({
            "exercise": exercise,
            "submission": ListeningSubmission.objects.filter(
                student=request.user, exercise=exercise
            ).first(),
        })


    else:
        return HttpResponse("Invalid exercise type.", status=400)

    return render(request, "learning/exercise_detail.html", context)

@login_required
def leaderboard(request):
    users = User.objects.filter(role='STUDENT')
    leaderboard_data = []

    for user in users:
        # Safely get points
        try:
            points = user.points.total_points
        except UserPoints.DoesNotExist:
            points = 0

        # Safely get streak
        streak = user.streaks.first().current_streak if user.streaks.exists() else 0

        badges_count = user.badges.count()

        leaderboard_data.append({
            'username': user.full_name,
            'points': points,
            'streak': streak,
            'badges': badges_count,
        })

    # Sort by points then streak
    leaderboard_data.sort(key=lambda x: (x['points'], x['streak']), reverse=True)

    return render(request, 'learning/leaderboard.html', {'leaderboard': leaderboard_data})


@login_required
@teacher_required
def add_question(request, lesson_id, quiz_id):
    # Fetch the quiz
    quiz = get_object_or_404(Quiz, id=quiz_id, lesson_id=lesson_id)

    # Formset for choices (for MCQ or MATCH questions)
    ChoiceFormSet = inlineformset_factory(Question, Choice, fields=('text', 'is_correct'), extra=4, can_delete=True)

    if request.method == 'POST':
        form = QuestionForm(request.POST)
        if form.is_valid():
            # Save question but don't commit yet
            question = form.save(commit=False)
            question.quiz = quiz
            question.save()

            # Save choices if question type requires them
            if question.question_type in ['MCQ', 'MATCH']:
                formset = ChoiceFormSet(request.POST, instance=question)
                if formset.is_valid():
                    formset.save()

            # Count total questions after addition
            q_count = quiz.questions.count()  # <-- use related_name 'questions'
            messages.success(request, f"Question added successfully! Total questions: {q_count}")

            # Redirect back to add_question page to allow adding more questions
            return redirect('learning:add_question', lesson_id=lesson_id, quiz_id=quiz_id)
    else:
        form = QuestionForm()
        formset = ChoiceFormSet()

    # Current question count
    q_count = quiz.questions.count()

    return render(
        request,
        'learning/add_question.html',
        {
            'quiz': quiz,
            'form': form,
            'formset': formset,
            'q_count': q_count
        }
    )



# ---------------- EXERCISES ----------------
@login_required
def exercise_list(request, lesson_id):
    # Get the lesson or return 404
    lesson = get_object_or_404(Lesson, id=lesson_id)
    
    exercises = []

    # ----- Quizzes -----
    for quiz in lesson.quizzes.all():
        # Use the correct field for filtering
        submission = quiz.submissions.filter(student=request.user).first()
        submitted = submission is not None
        score = submission.score if submission else 0

        exercises.append({
            'id': quiz.id,
            'exercise_type': 'Quiz',
            'title': quiz.title,
            'quiz_type': quiz.questions.first().question_type if quiz.questions.exists() else 'MCQ',
            'questions_count': quiz.questions.count(),
            'submitted': submitted,
            'score': score,
        })


    # ----- Speaking Exercises -----
    for speaking in lesson.speaking_exercises.all():
        submission = SpeakingSubmission.objects.filter(student=request.user, exercise=speaking).first()
        exercises.append({
            'id': speaking.id,
            'exercise_type': 'speak',
            'title': speaking.prompt,
            'submitted': bool(submission),
            'score': submission.score if submission else None,
        })

  # Writing Exercises (always included)
    for write in lesson.writing_exercises.all():
        submission = WritingSubmission.objects.filter(student=request.user, exercise=write).first()
        exercises.append({
            'id': write.id,
            'exercise_type': 'write',  # Static
            'title': write.prompt,
            'submitted': bool(submission),
            'score': submission.score if submission else None,
        })

    context = {
        'lesson': lesson,
        'exercises': exercises,
    }


    # ----- Listening Exercises -----
    for listening in lesson.listening_exercises.all():
        submission = ListeningSubmission.objects.filter(
            student=request.user,
            exercise=listening
        ).first()

        exercises.append({
            'id': listening.id,
            'exercise_type': 'listen',
            'title': listening.title,
            'due_date': listening.due_date,
            'submission': submission,            # <-- REQUIRED
            'score': submission.score if submission else None,
        })

    
    context = {
        'lesson': lesson,
        'exercises': exercises,
        'today': date.today()
    }
    
    return render(request, 'learning/exercise_list.html', context)


@login_required
def writing_exercise_detail(request, lesson_id, exercise_id, exercise_type):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    exercise = get_object_or_404(WritingExercise, id=exercise_id)
    
    # Get previous submission if exists
    submission = WritingSubmission.objects.filter(exercise=exercise, student=request.user).first()
    initial_text = submission.content if submission else ''

    if request.method == 'POST':
        # Get content safely
        text = request.POST.get('content', '').strip()
        
        # Check grammar/spelling
        matches = tool.check(text)
        total_words = len(text.split())
        mistakes = len(matches)
        score_percentage = max(0, round((total_words - mistakes) / total_words * 100, 2)) if total_words else 0

        # Save submission
        sub, created = WritingSubmission.objects.get_or_create(exercise=exercise, student=request.user)
        sub.content = text
        sub.score = score_percentage
        sub.save()

        # Return JSON response
        return JsonResponse({
            'matches': [
                {
                    'error_text': text[m.offset: m.offset + m.errorLength],
                    'message': m.message,
                    'replacements': m.replacements
                } for m in matches
            ],
            'score': score_percentage
        })

    # GET request
    context = {
        'lesson': lesson,
        'exercise': exercise,
        'initial_text': initial_text
    }
    return render(request, 'learning/exercise_detail.html', context)
    
@login_required
def quiz_result(request, lesson_id, exercise_type, exercise_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    exercise = get_object_or_404(Quiz, id=exercise_id, lesson=lesson)

    submission = QuizSubmission.objects.filter(student=request.user, quiz=exercise).first()
    total_questions = exercise.questions.count()
    correct_count = submission.score if submission else 0

    # Optional: prepare detailed feedback
    question_feedback = []
    if submission:
        for q in exercise.questions.all():
            user_answer = submission.answers.get(str(q.id), None)
            correct_answer = None
            if q.question_type == "MCQ":
                correct_choice = q.choices.filter(is_correct=True).first()
                correct_answer = correct_choice.text if correct_choice else ''
            elif q.question_type == "FILL":
                correct_answer = q.correct_answer

            question_feedback.append({
                "question": q,
                "user_answer": user_answer,
                "correct_answer": correct_answer,
                "correct": str(user_answer).strip().lower() == str(correct_answer).strip().lower() if user_answer else False
            })

    context = {
        "quiz": exercise,
        "submission": submission,
        "total_questions": total_questions,
        "correct_count": correct_count,
        "question_feedback": question_feedback,
    }
    return render(request, "learning/quiz_result.html", context)


@login_required
@teacher_required
def exercise_create(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)

    # Select exercise type first
    exercise_type = request.GET.get('type')  # e.g., ?type=quiz

    if request.method == 'POST':
        if exercise_type == 'quiz':
            form = QuizForm(request.POST)
        elif exercise_type == 'speaking':
            form = SpeakingExerciseForm(request.POST)
        elif exercise_type == 'writing':
            form = WritingExerciseForm(request.POST)
        elif exercise_type == 'listening':
            form = ListeningExerciseForm(request.POST, request.FILES)
        else:
            form = None


        if form and form.is_valid():
            exercise = form.save(commit=False)
            exercise.lesson = lesson
            exercise.save()
            return redirect('learning:exercise_list', lesson_id=lesson.id)
    else:
        if exercise_type == 'quiz':
            form = QuizForm()
        elif exercise_type == 'speaking':
            form = SpeakingExerciseForm()
        elif exercise_type == 'writing':
            form = WritingExerciseForm()
        elif exercise_type == 'listening':
            form = ListeningExerciseForm()
        else:
            form = None

    context = {
        'lesson': lesson,
        'form': form,
        'exercise_type': exercise_type,
    }
    return render(request, 'learning/exercise_create.html', context)

@teacher_required
def exercise_edit(request, lesson_id, exercise_type, exercise_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    exercise_type = exercise_type.lower()
    
    # Get the exercise based on type
    if exercise_type == "quiz":
        exercise = get_object_or_404(Quiz, id=exercise_id, lesson=lesson)
        form_class = QuizForm
    elif exercise_type == "speak":
        exercise = get_object_or_404(SpeakingExercise, id=exercise_id, lesson=lesson)
        form_class = SpeakingExerciseForm
    elif exercise_type == "write":
        exercise = get_object_or_404(WritingExercise, id=exercise_id, lesson=lesson)
        form_class = WritingExerciseForm
    elif exercise_type == "listen":
        exercise = get_object_or_404(ListeningExercise, id=exercise_id, lesson=lesson)
        form_class = ListeningExerciseForm
    else:
        return HttpResponse("Invalid exercise type.", status=400)

    if request.method == "POST":
        form = form_class(request.POST, request.FILES, instance=exercise)
        if form.is_valid():
            form.save()
            return redirect("learning:exercise_list", lesson_id=lesson.id)
    else:
        form = form_class(instance=exercise)

    return render(request, "learning/exercise_edit.html", {
        "form": form,
        "lesson": lesson,
        "exercise": exercise,
        "exercise_type": exercise_type,
    })

@teacher_required
def exercise_delete(request, lesson_id, exercise_type, exercise_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    exercise_type = exercise_type.lower()

    # Get the correct exercise type
    if exercise_type == "quiz":
        exercise = get_object_or_404(Quiz, id=exercise_id, lesson=lesson)
    elif exercise_type == "speak":
        exercise = get_object_or_404(SpeakingExercise, id=exercise_id, lesson=lesson)
    elif exercise_type == "write":
        exercise = get_object_or_404(WritingExercise, id=exercise_id, lesson=lesson)
    elif exercise_type == "listen":
        exercise = get_object_or_404(ListeningExercise, id=exercise_id, lesson=lesson)
    else:
        return HttpResponse("Invalid exercise type.", status=400)

    if request.method == "POST":
        exercise.delete()
        return redirect("learning:exercise_list", lesson_id=lesson.id)

    return render(request, "learning/exercise_confirm_delete.html", {
        "lesson": lesson,
        "exercise": exercise,
        "exercise_type": exercise_type,
    })

@login_required
@teacher_required
def create_assignment(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)

    if request.user.role != 'TEACHER' and not request.user.is_superuser:
        messages.error(request, "You are not allowed to create assignments.")
        return redirect('learning:exercise_list', lesson_id=lesson.id)

    if request.method == "POST":
        form = AssignmentForm(request.POST, request.FILES)
        if form.is_valid():
            assignment = form.save(commit=False)
            assignment.lesson = lesson
            assignment.created_by = request.user  # set the teacher
            assignment.save()
            messages.success(request, "Assignment created successfully!")
            return redirect('learning:assignment_list', lesson_id=lesson.id)
    else:
        form = AssignmentForm()

    return render(request, 'learning/assignment_create.html', {'form': form, 'lesson': lesson})

@login_required
def assignment_list(request, lesson_id):
    lesson = get_object_or_404(Lesson, id=lesson_id)
    assignments = lesson.assignments.all()
    submissions = AssignmentSubmission.objects.filter(student=request.user)

    context = {
        'lesson': lesson,
        'assignments': assignments,
        'submissions': submissions,
        'today': date.today()
    }
    return render(request, 'learning/assignment_list.html', context)

@login_required
def assignment_detail(request, assignment_id):
    assignment = get_object_or_404(Assignment, id=assignment_id)

    # -------------------------------
    #  TEACHER VIEW (marking page)
    # -------------------------------
    if request.user.role == 'TEACHER' or request.user.is_superuser:
        submissions = assignment.submissions.select_related('student').all()

        if request.method == 'POST':

            # Helper functions for gamification
            def award_points(user, points):
                user_points, _ = UserPoints.objects.get_or_create(user=user)
                user_points.total_points += points
                user_points.save()

                # Update streak
                streak, _ = UserStreak.objects.get_or_create(user=user)
                today = date.today()

                if streak.last_active == today:
                    return
                elif streak.last_active == today - timedelta(days=1):
                    streak.current_streak += 1
                    streak.max_streak = max(streak.max_streak, streak.current_streak)
                else:
                    streak.current_streak = 1
                streak.last_active = today
                streak.save()

            def check_badges(user):
                user_points, _ = UserPoints.objects.get_or_create(user=user)
                badges_to_award = []
                if user_points.total_points >= 100:
                    badges_to_award.append("Rising Star")
                if user_points.total_points >= 500:
                    badges_to_award.append("Super Learner")
                for badge_name in badges_to_award:
                    badge = Badge.objects.filter(name=badge_name).first()
                    if badge:
                        UserBadge.objects.get_or_create(user=user, badge=badge)

            for key, value in request.POST.items():
                if key.startswith("marks_"):
                    sub_id = key.split("_")[1]

                    try:
                        submission = AssignmentSubmission.objects.get(
                            id=int(sub_id),
                            assignment=assignment
                        )

                        # Empty marks should be treated as None
                        if value.strip() != "":
                            submission.marks_awarded = int(value)
                        else:
                            submission.marks_awarded = None

                        submission.save()

                        # 🎮 Award points if marks are given
                        if submission.marks_awarded is not None:
                            # Scale points based on total marks (e.g., 1 point per mark)
                            points_to_award = submission.marks_awarded
                            award_points(submission.student, points_to_award)
                            check_badges(submission.student)

                    except (AssignmentSubmission.DoesNotExist, ValueError):
                        pass

            messages.success(request, "Marks updated successfully! Points awarded where applicable.")
            return redirect('learning:assignment_detail', assignment_id=assignment.id)

        return render(request, 'learning/assignment_marking.html', {
            'assignment': assignment,
            'submissions': submissions,
        })

    # -------------------------------
    #  STUDENT VIEW (submit page)
    # -------------------------------
    else:
        submissions_qs = AssignmentSubmission.objects.filter(student=request.user, assignment=assignment)
        submission = submissions_qs.first()  # Get the first (and only) submission, or None

        if request.method == "POST":
            uploaded_file = request.FILES.get("submitted_file")

            if uploaded_file:
                if submission:  # update existing submission
                    submission.submitted_file = uploaded_file
                    submission.submitted_at = timezone.now()
                    submission.save()
                else:  # create new submission
                    submission = AssignmentSubmission.objects.create(
                        assignment=assignment,
                        student=request.user,
                        submitted_file=uploaded_file
                    )

                messages.success(request, "Assignment submitted successfully!")
                return redirect('learning:assignment_detail', assignment_id=assignment.id)

        return render(request, 'learning/submit_assignment.html', {
            'assignment': assignment,
            'submission': submission,
        })


@csrf_exempt
def check_writing(request):
    if request.method == "POST":
        text = request.POST.get('text', '')
        matches = tool.check(text)
        suggestions = []

        for match in matches:
            suggestions.append({
                'message': match.message,
                'offset': match.offset,
                'error_text': text[match.offset: match.offset + match.errorLength],
                'replacements': match.replacements
            })

        return JsonResponse({'suggestions': suggestions})
    return JsonResponse({'error': 'Invalid request'}, status=400)

@login_required
def forum_list(request):
    topics = ForumTopic.objects.all().order_by('-created_at')
    return render(request, 'learning/forum_list.html', {'topics': topics})

# Create a topic (teacher only)
@login_required
def forum_create_topic(request):
    if request.user.role not in ['TEACHER', 'ADMIN'] and not request.user.is_superuser:
        return redirect('learning:forum_list')

    if request.method == 'POST':
        form = ForumTopicForm(request.POST)
        if form.is_valid():
            topic = form.save(commit=False)
            topic.created_by = request.user
            topic.save()
            return redirect('learning:forum_list')
    else:
        form = ForumTopicForm()

    return render(request, 'learning/forum_create.html', {'form': form})

# View topic and post comments
@login_required
def forum_topic_detail(request, topic_id):
    topic = get_object_or_404(ForumTopic, id=topic_id)
    posts = topic.posts.filter(parent__isnull=True).order_by('created_at')

    if request.method == 'POST':
        form = ForumPostForm(request.POST)
        if form.is_valid():
            post = form.save(commit=False)
            post.topic = topic
            post.author = request.user
            parent_id = request.POST.get('parent_id')
            if parent_id:
                parent_post = ForumPost.objects.get(id=parent_id)
                post.parent = parent_post
            post.save()
            return redirect('learning:forum_topic_detail', topic_id=topic.id)
    else:
        form = ForumPostForm()

    return render(request, 'learning/forum_topic_detail.html', {'topic': topic, 'posts': posts, 'form': form})
@login_required
def student_progress(request, student_id=None):

    if student_id:
        try:
            student = User.objects.get(id=student_id)
            is_teacher_view = True
        except User.DoesNotExist:
            return HttpResponse("Student not found", status=404)
    else:
        student = request.user
        is_teacher_view = False

    
    user_badges = UserBadge.objects.filter(user=student).select_related('badge')
    point_badges = [
        {
            'name': ub.badge.name,
            'icon': ub.badge.icon.url if ub.badge.icon else None,
        }
        for ub in user_badges
    ]

    courses = Course.objects.filter(created_by__isnull=False)
    course_progress_data = []

    for course in courses:
        lessons = course.lessons.all()
        lesson_progress_list = []

        for lesson in lessons:
            # --- Quizzes ---
            quiz_submissions = QuizSubmission.objects.filter(student=student, quiz__lesson=lesson)
            quiz_done = quiz_submissions.exists()
            quiz_total = lesson.quizzes.count()
            quiz_score_avg = quiz_submissions.aggregate(avg=Avg('score'))['avg'] or 0

            # --- Assignments ---
            assignment_submissions = AssignmentSubmission.objects.filter(student=student, assignment__lesson=lesson)
            assignment_done = assignment_submissions.exists()
            assignment_score_avg = assignment_submissions.aggregate(avg=Avg('marks_awarded'))['avg'] or 0

            # --- Speaking ---
            speaking_submissions = SpeakingSubmission.objects.filter(student=student, exercise__lesson=lesson)
            speaking_done = speaking_submissions.exists()
            speaking_score_avg = speaking_submissions.aggregate(avg=Avg('score'))['avg'] or 0

            # --- Writing ---
            writing_submissions = WritingSubmission.objects.filter(student=student, exercise__lesson=lesson)
            writing_done = writing_submissions.exists()
            writing_score_avg = writing_submissions.aggregate(avg=Avg('score'))['avg'] or 0

            # --- Listening ---
            listening_exercises = ListeningExercise.objects.filter(lesson=lesson)
            listening_submissions = ListeningSubmission.objects.filter(student=student, exercise__lesson=lesson)
            listening_done = (
                listening_submissions.count() == listening_exercises.count() and listening_exercises.exists()
            )
            listening_score_avg = listening_submissions.aggregate(avg=Avg('score'))['avg'] or 0

            lesson_completed = all([
                quiz_done,
                assignment_done,
                speaking_done,
                writing_done,
                listening_done
            ])

            lesson_progress_list.append({
                'lesson': lesson,
                'completed': lesson_completed,
                'details': {
                    'quiz_done': quiz_done,
                    'quiz_score_avg': round(quiz_score_avg, 2),
                    'quiz_total': quiz_total,

                    'assignment_done': assignment_done,
                    'assignment_score_avg': round(assignment_score_avg, 2),

                    'speaking_done': speaking_done,
                    'speaking_score_avg': round(speaking_score_avg, 2),

                    'writing_done': writing_done,
                    'writing_score_avg': round(writing_score_avg, 2),

                    'listening_done': listening_done,
                    'listening_score_avg': round(listening_score_avg, 2),
                    'listening_total': listening_exercises.count(),
                }
            })

        completed_lessons = sum(1 for lp in lesson_progress_list if lp['completed'])

        def calculate_average_score(score_key, done_key):
            scores = [lp['details'][score_key] for lp in lesson_progress_list if lp['details'][done_key]]
            return sum(scores) / len(scores) if scores else 0

        course_progress_data.append({
            'course': course,
            'lessons': lesson_progress_list,
            'total_lessons': lessons.count(),
            'completed_lessons': completed_lessons,
            'completion_percentage': round((completed_lessons / max(1, lessons.count())) * 100, 1),

            'avg_quiz_score': round(calculate_average_score('quiz_score_avg', 'quiz_done'), 2),
            'avg_assignment_score': round(calculate_average_score('assignment_score_avg', 'assignment_done'), 2),
            'avg_speaking_score': round(calculate_average_score('speaking_score_avg', 'speaking_done'), 2),
            'avg_writing_score': round(calculate_average_score('writing_score_avg', 'writing_done'), 2),
            'avg_listening_score': round(calculate_average_score('listening_score_avg', 'listening_done'), 2),
        })

    context = {
        'course_progress_data': course_progress_data,
        'viewing_student': student,
        'is_teacher_view': is_teacher_view,
        'is_own_progress': student == request.user,
        'point_badges': point_badges,  # ✅ always defined
    }

    return render(request, 'learning/student_progress.html', context)


def get_course_progress_data(student, course):
    """Helper function to calculate course progress with proper error handling"""
    lessons = course.lessons.all()
    lesson_progress_list = []
    
    for lesson in lessons:
        try:
            # --- Quizzes ---
            quiz_submissions = QuizSubmission.objects.filter(student=student, quiz__lesson=lesson)
            quiz_done = quiz_submissions.exists()
            quiz_score_avg = quiz_submissions.aggregate(avg=Avg('score'))['avg'] or 0 if quiz_done else 0

            # --- Assignments ---
            assignment_submissions = AssignmentSubmission.objects.filter(student=student, assignment__lesson=lesson)
            assignment_done = assignment_submissions.exists()
            assignment_score_avg = assignment_submissions.aggregate(avg=Avg('marks_awarded'))['avg'] or 0 if assignment_done else 0

            # --- Speaking ---
            speaking_submissions = SpeakingSubmission.objects.filter(student=student, exercise__lesson=lesson)
            speaking_done = speaking_submissions.exists()
            speaking_score_avg = speaking_submissions.aggregate(avg=Avg('score'))['avg'] or 0 if speaking_done else 0

            # --- Writing ---
            writing_submissions = WritingSubmission.objects.filter(student=student, exercise__lesson=lesson)
            writing_done = writing_submissions.exists()
            writing_score_avg = writing_submissions.aggregate(avg=Avg('score'))['avg'] or 0 if writing_done else 0

            # --- Listening ---
            listening_exercises = ListeningExercise.objects.filter(lesson=lesson)
            listening_submissions = ListeningSubmission.objects.filter(student=student, exercise__lesson=lesson)
            listening_done = (listening_submissions.count() == listening_exercises.count() and listening_exercises.exists())
            listening_score_avg = listening_submissions.aggregate(avg=Avg('score'))['avg'] or 0 if listening_submissions.exists() else 0

            # Determine lesson completion
            lesson_completed = all([
                quiz_done, 
                assignment_done, 
                speaking_done, 
                writing_done, 
                listening_done
            ])

            lesson_progress_list.append({
                'completed': lesson_completed,
                'quiz_done': quiz_done,
                'quiz_score_avg': float(quiz_score_avg),
                'assignment_done': assignment_done,
                'assignment_score_avg': float(assignment_score_avg),
                'speaking_done': speaking_done,
                'speaking_score_avg': float(speaking_score_avg),
                'writing_done': writing_done,
                'writing_score_avg': float(writing_score_avg),
                'listening_done': listening_done,
                'listening_score_avg': float(listening_score_avg),
            })
            
        except Exception as e:
            # If there's an error with a specific lesson, skip it and continue
            print(f"Error processing lesson {lesson.id}: {str(e)}")
            continue
    
    # Calculate course averages with proper error handling
    completed_lessons = sum(1 for lp in lesson_progress_list if lp['completed'])
    total_lessons = len(lesson_progress_list)
    
    # Helper function to safely calculate averages
    def calculate_average(score_key, done_key):
        valid_scores = [lp[score_key] for lp in lesson_progress_list if lp[done_key] and lp[score_key] is not None]
        return sum(valid_scores) / max(1, len(valid_scores)) if valid_scores else 0
    
    # Calculate overall course average
    all_scores = []
    for lp in lesson_progress_list:
        if lp['quiz_done'] and lp['quiz_score_avg'] is not None:
            all_scores.append(lp['quiz_score_avg'])
        if lp['assignment_done'] and lp['assignment_score_avg'] is not None:
            all_scores.append(lp['assignment_score_avg'])
        if lp['speaking_done'] and lp['speaking_score_avg'] is not None:
            all_scores.append(lp['speaking_score_avg'])
        if lp['writing_done'] and lp['writing_score_avg'] is not None:
            all_scores.append(lp['writing_score_avg'])
        if lp['listening_done'] and lp['listening_score_avg'] is not None:
            all_scores.append(lp['listening_score_avg'])
    
    overall_average = sum(all_scores) / max(1, len(all_scores)) if all_scores else 0
    
    # Calculate completion percentage
    completion_percentage = (completed_lessons / max(1, total_lessons)) * 100
    
    return {
        'course': course,
        'total_lessons': total_lessons,
        'completed_lessons': completed_lessons,
        'completion_percentage': round(completion_percentage, 1),
        'avg_quiz_score': round(calculate_average('quiz_score_avg', 'quiz_done'), 1),
        'avg_assignment_score': round(calculate_average('assignment_score_avg', 'assignment_done'), 1),
        'avg_speaking_score': round(calculate_average('speaking_score_avg', 'speaking_done'), 1),
        'avg_writing_score': round(calculate_average('writing_score_avg', 'writing_done'), 1),
        'avg_listening_score': round(calculate_average('listening_score_avg', 'listening_done'), 1),
        'overall_average': round(overall_average, 1),
        'is_course_completed': completed_lessons == total_lessons and total_lessons > 0,
    }


def generate_certificate_pdf(request, course_id):
    
    try:
        course = Course.objects.get(id=course_id)
        student = request.user

        
        # Get course progress data
        course_progress = get_course_progress_data(student, course)
        
        if course_progress['completed_lessons'] < course_progress['total_lessons']:
            return HttpResponse("Course not completed yet!", status=400)
        
        # Create response object
        response = HttpResponse(content_type='application/pdf')
        filename = f"certificate_{course.title.replace(' ', '_')}_{student.full_name}.pdf"
        response['Content-Disposition'] = f'attachment; filename="{filename}"'
        
        # Create PDF
        doc = SimpleDocTemplate(response, pagesize=A4)
        story = []
        
        # Custom styles
        styles = getSampleStyleSheet()
        title_style = ParagraphStyle(
            'CustomTitle',
            parent=styles['Heading1'],
            fontSize=24,
            textColor=colors.darkblue,
            spaceAfter=30,
            alignment=1  # Center alignment
        )
        
        normal_style = ParagraphStyle(
            'CustomNormal',
            parent=styles['Normal'],
            fontSize=14,
            textColor=colors.black,
            alignment=1
        )
        
        # Certificate content
        story.append(Spacer(1, 2*inch))
        
        # Certificate Title
        story.append(Paragraph("CERTIFICATE OF COMPLETION", title_style))
        story.append(Spacer(1, 0.5*inch))
        
        # This certifies text
        story.append(Paragraph("This certifies that", normal_style))
        story.append(Spacer(1, 0.3*inch))
        
        # Student name
        name_style = ParagraphStyle(
            'NameStyle',
            parent=styles['Heading2'],
            fontSize=28,
            textColor=colors.darkblue,
            alignment=1
        )
        story.append(Paragraph(student.full_name, name_style))
        story.append(Spacer(1, 0.3*inch))
        
        # Completion text
        story.append(Paragraph("has successfully completed the course", normal_style))
        story.append(Spacer(1, 0.3*inch))
        
        # Course title
        course_style = ParagraphStyle(
            'CourseStyle',
            parent=styles['Heading2'],
            fontSize=20,
            textColor=colors.darkgreen,
            alignment=1
        )
        story.append(Paragraph(f'"{course.title}"', course_style))
        story.append(Spacer(1, 0.3*inch))
        
        # Course description (only if it exists and is not None)
        if course.description:  # This will handle None and empty strings
            desc_style = ParagraphStyle(
                'DescStyle',
                parent=styles['Normal'],
                fontSize=12,
                textColor=colors.gray,
                alignment=1,
                fontStyle='italic'
            )
            story.append(Paragraph(course.description, desc_style))
            story.append(Spacer(1, 0.3*inch))
        
        # Performance metrics - ensure all values are properly formatted
        performance_text = f"""
        <br/><br/>
        Performance Summary:<br/>
        • Lessons Completed: {course_progress['completed_lessons']}/{course_progress['total_lessons']}<br/>
        • Average Quiz Score: {course_progress.get('avg_quiz_score', 0):.1f}%<br/>
        • Average Assignment Score: {course_progress.get('avg_assignment_score', 0):.1f}%<br/>
        • Average Speaking Score: {course_progress.get('avg_speaking_score', 0):.1f}%<br/>
        • Average Writing Score: {course_progress.get('avg_writing_score', 0):.1f}%<br/>
        • Overall Progress: {(course_progress['completed_lessons'] / course_progress['total_lessons']) * 100:.1f}%<br/>
        """
        
        performance_style = ParagraphStyle(
            'PerformanceStyle',
            parent=styles['Normal'],
            fontSize=10,
            textColor=colors.darkgray,
            alignment=1,
            leftIndent=50,
            rightIndent=50
        )
        story.append(Paragraph(performance_text, performance_style))
        story.append(Spacer(1, 0.5*inch))
        
        # Date and signatures
        date_style = ParagraphStyle(
            'DateStyle',
            parent=styles['Normal'],
            fontSize=12,
            textColor=colors.black,
            alignment=1
        )
        
        story.append(Paragraph(f"Date of Completion: {datetime.now().strftime('%B %d, %Y')}", date_style))
        story.append(Spacer(1, 0.8*inch))
        
        # Signature area
        signature_style = ParagraphStyle(
            'SignatureStyle',
            parent=styles['Normal'],
            fontSize=12,
            textColor=colors.black,
            alignment=1
        )
        
        # Create signature table (simplified)
        signature_text = """
        <br/>
        _________________________<br/>
        Director of Education<br/>
        Language Learning Platform<br/>
        """
        story.append(Paragraph(signature_text, signature_style))
        
        # Build PDF
        doc.build(story)
        
        return response
        
    except Course.DoesNotExist:
        return HttpResponse("Course not found!", status=404)
    except Exception as e:
        return HttpResponse(f"Error generating certificate: {str(e)}", status=500)

