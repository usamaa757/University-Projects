from django.contrib.auth.backends import ModelBackend
from django.contrib.auth import get_user_model

CustomUser = get_user_model()

class CustomUserAuthBackend(ModelBackend):
    def authenticate(self, request, username=None, password=None, **kwargs):
        try:
            # Check if the input is a mobile number
            if username.isdigit():  # If input is numeric, assume it's a mobile number
                user = CustomUser.objects.get(mobile_no=username)
            else:  # Otherwise, assume it's a username
                user = CustomUser.objects.get(username=username)
            
            if user.check_password(password):
                return user
        except CustomUser.DoesNotExist:
            return None
        return None
