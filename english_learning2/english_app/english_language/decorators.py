from django.contrib.auth.decorators import user_passes_test

def admin_required(view_func):
    decorated_view_func = user_passes_test(lambda u: u.is_authenticated and u.is_admin())(view_func)
    return decorated_view_func

def teacher_required(view_func):
    decorated_view_func = user_passes_test(lambda u: u.is_authenticated and u.is_teacher())(view_func)
    return decorated_view_func

def student_required(view_func):
    decorated_view_func = user_passes_test(lambda u: u.is_authenticated and u.is_student())(view_func)
    return decorated_view_func
def admin_or_teacher_required(view_func):
    decorated_view_func = user_passes_test(lambda u: u.is_authenticated and (u.is_admin() or u.is_teacher()))(view_func)
    return decorated_view_func