from django.shortcuts import render, redirect, get_object_or_404
from django.utils import timezone
from accounts.decorators import role_required
from assignments.models import Assignment, AssignmentSubmission
from assignments.forms import AssignmentForm, SubmissionForm
from django.db.models import Count
from django.contrib import messages

@role_required(['teacher'])
def create_assignment(request):
    if request.method == 'POST':
        form = AssignmentForm(request.POST, request.FILES)
        if form.is_valid():
            assignment = form.save(commit=False)
            assignment.teacher = request.user
            assignment.save()
            messages.success(request, 'Assignment created successfully.')
            return redirect('assignment_list')
    else:
        form = AssignmentForm()
    return render(request, 'create_assignment.html', {'form': form})

@role_required(['teacher'])
def edit_assignment(request, pk):
        assignment = get_object_or_404(Assignment, pk=pk, teacher=request.user)
        if request.method == 'POST':
            new_date = request.POST.get('due_date')
            total_marks = request.POST.get('total_marks')
            
            if new_date:
                assignment.due_date = new_date
            if total_marks:
                assignment.total_marks = total_marks
            
            assignment.save()
            messages.success(request, 'Assignment updated successfully.')
            return redirect('assignment_list')

        return render(request, 'edit_assignment.html', {'assignment': assignment})


@role_required(['student'])
def assignment_list(request):
    # Get all submissions by the current student
    submissions = AssignmentSubmission.objects.filter(
        student=request.user
    ).select_related('assignment').order_by('-assignment__due_date')

    return render(request, 'assignment_list.html', {
        'submissions': submissions
    })



@role_required(['student'])
def submit_assignment(request, assignment_id):
    assignment = get_object_or_404(Assignment, id=assignment_id)

    # Check if the student already has a submission
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
            messages.success(request, 'Assignment submitted successfully.')
            return redirect('assignment_list')
    else:
        form = SubmissionForm(instance=submission)

    return render(request, 'submit_assignment.html', {
        'form': form,
        'assignment': assignment
    })

@role_required(['teacher'])
def view_submissions(request, assignment_id):
    assignment = get_object_or_404(Assignment, id=assignment_id, teacher=request.user)
    submissions = AssignmentSubmission.objects.filter(assignment=assignment)


    if request.method == 'POST':
        for submission in submissions:
            mark = request.POST.get(f'mark_{submission.id}')
            if mark is not None:
                submission.marks = mark
                submission.save()
        messages.success(request, 'Marks updated successfully.')
        return redirect('view_submissions', assignment_id=assignment.id)

    return render(request, 'view_submissions.html', {
        'assignment': assignment,
        'submissions': submissions
    })
    
def assignment_list(request):
    user = request.user
    today = timezone.now().date()

    submissions = []
    assignments = []

    if user.role == 'student':
        # Student: show their own submissions
        submissions = AssignmentSubmission.objects.filter(student=user).select_related('assignment')

    elif user.role == 'teacher':
        # Teacher: always show all assignments they created
        assignments = Assignment.objects.filter(teacher=user)

    return render(request, 'assignment_list.html', {
        'submissions': submissions,
        'assignments': assignments,
        'today': today,
        'user': user,
    })