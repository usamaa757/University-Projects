from django.http import HttpResponseForbidden

def teacher_required(view_func):
    def wrapper(request, *args, **kwargs):
        if request.user.role == 'TEACHER' or request.user.is_superuser:
            return view_func(request, *args, **kwargs)
        return HttpResponseForbidden("You do not have permission")
    return wrapper
