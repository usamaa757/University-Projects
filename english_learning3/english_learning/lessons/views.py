from django.shortcuts import render, redirect, get_object_or_404
from django.utils import timezone
from accounts.decorators import role_required
from lessons.models import LiveSession, Lesson, LessonMedia
from lessons.forms import LiveSessionForm, LessonForm, LessonMediaForm
from django.db.models import Count
from django.forms import modelformset_factory
from django.contrib import messages
from accounts.decorators import role_required   # if you have this decorator
from lessons.models import Lesson

@role_required(['teacher', 'admin'])
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


@role_required(['teacher', 'admin'])
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
    return render(request, "lesson_detail.html", {"lesson": lesson})

# Create your views here.
@role_required(['teacher'])
def create_live_session(request):
    if request.method == 'POST':
        form = LiveSessionForm(request.POST)
        if form.is_valid():
            session = form.save(commit=False)
            session.teacher = request.user
            session.save()
            return redirect('teacher_live_sessions')
    else:
        form = LiveSessionForm()
    return render(request, 'create_live_session.html', {'form': form})


def live_session_list(request):
    sessions = LiveSession.objects.filter(date_time__gte=timezone.now()).order_by('date_time')
    return render(request, 'live_session_list.html', {'sessions': sessions})
