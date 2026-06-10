from django.shortcuts import render, redirect, get_object_or_404
from django.forms import modelformset_factory
from .models import Quiz, Question, Choice, MatchingPair, SpeakingExercise, QuizResult, SpeakingResult, WritingExerciseResult, WritingExercise
from .forms import QuizForm, QuestionForm, ChoiceForm, MatchingPairForm, MatchingPairFormSet, ChoiceFormSet, SpeakingExerciseForm
import os, textstat, random, language_tool_python, whisper, tempfile
from difflib import SequenceMatcher
from django.views.decorators.csrf import csrf_exempt
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from django.utils import timezone
from django.utils.timezone import now, timedelta

tool = language_tool_python.LanguageTool('en-US')  # English grammar checker
os.environ["PATH"] += os.pathsep + r"C:\ffmpeg\bin"
model = whisper.load_model("base")

def create_quiz(request):
    if request.method == "POST":
        form = QuizForm(request.POST)
        if form.is_valid():
            quiz = form.save()
            return redirect('add_questions', quiz_id=quiz.id)
    else:
        form = QuizForm()
    return render(request, 'create_quiz.html', {'form': form})


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
 
def quiz_list(request):
    quizzes = Quiz.objects.select_related("lesson").all()
    taken_quiz_ids = []
    quiz_results = {}

    if request.user.is_authenticated and request.user.role == "student":
        taken_quiz_ids = QuizResult.objects.filter(student=request.user).values_list("quiz_id", flat=True)
        results = QuizResult.objects.filter(student=request.user)
        quiz_results = {r.quiz_id: r for r in results}

    # attach result to each quiz for easy template access
    for quiz in quizzes:
        quiz.result = quiz_results.get(quiz.id)
    today = timezone.localdate()
    
    return render(request, "quiz_list.html", {
        "quizzes": quizzes,
        "taken_quiz_ids": taken_quiz_ids,
        "today": today,
    })

def delete_quiz(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)
    
    if request.user.role != "teacher":  # prevent students
        messages.error(request, "You are not authorized to delete quizzes.")
        return redirect("quiz_list")

    quiz.delete()
    messages.success(request, f"Quiz '{quiz.title}' deleted successfully.")
    return redirect("quiz_list")

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
                if user_answer == correct_answer:
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

        # ✅ Save result for this student
        if request.user.is_authenticated:
            QuizResult.objects.create(
                student=request.user,
                quiz=quiz,
                score=score,
                total=total
            )

        return render(request, "quiz_result.html", {"quiz": quiz, "score": score, "total": total})

    return render(request, 'take_quiz.html', {'quiz': quiz})

def quiz_results_list(request):
    results = QuizResult.objects.select_related("student", "quiz__lesson").all()
    return render(request, "quiz_results_list.html", {"results": results})

def my_quiz_results(request):
    results = QuizResult.objects.filter(student=request.user).select_related("quiz__lesson")
    return render(request, "my_quiz_results.html", {"results": results})


# def quiz_result_detail(request, quiz_id):
#     quiz = get_object_or_404(Quiz, id=quiz_id)
#     result = get_object_or_404(QuizResult, quiz=quiz, student=request.user)

#     return render(request, "quiz_result_detail.html", {
#         "quiz": quiz,
#         "result": result,
#     })
    
    
def quiz_result_detail(request, quiz_id):
    quiz = get_object_or_404(Quiz, id=quiz_id)
    
    # Get the most recent QuizResult for this student and quiz
    result = QuizResult.objects.filter(quiz=quiz, student=request.user).order_by('-taken_at').first()
    if not result:
        messages.error(request, "No results found for this quiz.")
        return redirect('quiz_list')

    passed = result.score >= (result.total * 0.6)  # example pass threshold 60%

    return render(request, "quiz_result_detail.html", {
        "quiz": quiz,
        "result": result,
        "passed": passed,
    })


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
    
def speaking_exercise_list(request):
    exercises = SpeakingExercise.objects.all()
    return render(request, "speaking_exercise_list.html", {"exercises": exercises})

def delete_speaking_exercise(request, ex_id):
    exercise = get_object_or_404(SpeakingExercise, id=ex_id)

    # Only allow teacher or admin
    if request.user.role not in ['teacher', 'admin']:
        messages.error(request, "You do not have permission to delete this exercise.")
        return redirect('speaking_exercise_list')

    # Delete immediately
    exercise.delete()
    messages.success(request, "Exercise deleted successfully.")
    return redirect('speaking_exercise_list')


@csrf_exempt
def practice_speaking(request, pk):
    exercise = get_object_or_404(SpeakingExercise, pk=pk)
    pronunciation_score, matching_score, transcript = None, None, None

    if request.method == "POST" and request.FILES.get("audio_file"):
        audio_file = request.FILES["audio_file"]

        # Save uploaded audio temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as tmp:
            for chunk in audio_file.chunks():
                tmp.write(chunk)
            tmp_path = tmp.name

        # Transcribe audio using your model
        result = model.transcribe(tmp_path, language="en")
        transcript = result["text"].strip()

        # 1️⃣ Matching score: how close transcript is to expected text
        matching_score = round(SequenceMatcher(None, exercise.text.lower(), transcript.lower()).ratio() * 100, 2)

        # 2️⃣ Pronunciation score: optional - could be from model confidence
        # For demo, let's simulate a pronunciation score as a random number or some metric
        # You should replace this with actual pronunciation evaluation logic
        pronunciation_score = round(result.get("confidence", 0.9) * 100, 2)  # example: use confidence from transcription

        os.remove(tmp_path)

        # Save attempt
        SpeakingResult.objects.create(
            exercise=exercise,
            student=request.user,
            audio_file=audio_file,
            transcript=transcript,
            feedback=f"Pronunciation score: {pronunciation_score}%, Matching score: {matching_score}%",
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

        similarity = SequenceMatcher(None, expected_text.lower(), transcript.lower()).ratio()
        score = round(similarity * 100, 2)

        os.remove(tmp_path)

    return render(request, "speaking.html", {
        "score": score,
        "transcript": transcript,
        "expected_text": expected_text
    })



@login_required
def writing_practice(request):
    essay = None
    matches = []
    corrected_text = None
    readability = None
    score = None

    # create or reuse a self-practice exercise
    exercise, _ = WritingExercise.objects.get_or_create(
        title="Self Practice",
        prompt="Free writing"
    )

    if request.method == "POST":
        essay = request.POST.get("essay", "").strip()

        if essay:
            # Grammar/Spell checks
            matches = tool.check(essay)
            corrected_text = tool.correct(essay)

            # Readability metrics
            readability = {
                "flesch_reading_ease": textstat.flesch_reading_ease(essay),
                "grade_level": textstat.flesch_kincaid_grade(essay),
                "word_count": textstat.lexicon_count(essay)
            }

            # ✅ Similarity score between essay and corrected_text
            similarity = SequenceMatcher(None, essay.lower(), corrected_text.lower()).ratio()
            score = round(similarity * 100, 2)

            # Save result
            WritingExerciseResult.objects.create(
                exercise=exercise,
                student=request.user,
                submission_text=essay,
                score=score,
                feedback=f"Auto-evaluated. Similarity to corrected text: {score}%"
            )

    return render(request, "writing_practice.html", {
        "essay": essay,
        "matches": matches,
        "readability": readability,
        "corrected_text": corrected_text,
        "score": score,
    })


