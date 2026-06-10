from functools import wraps
from flask import session, redirect, url_for, flash, request
from flask_dance.contrib.google import google
import os

class AuthManager:
    def __init__(self, app=None, db=None):
        self.app = app
        self.db = db
        if app:
            self.init_app(app, db)
    
    def init_app(self, app, db):
        self.app = app
        self.db = db
        
        # Configure OAuth
        client_id = os.environ.get('GOOGLE_OAUTH_CLIENT_ID')
        client_secret = os.environ.get('GOOGLE_OAUTH_CLIENT_SECRET')
        
        if client_id and client_secret:
            from flask_dance.contrib.google import make_google_blueprint
            
            # Create blueprint with explicit redirect
            google_bp = make_google_blueprint(
                client_id=client_id,
                client_secret=client_secret,
                scope=[
                    "https://www.googleapis.com/auth/userinfo.email",
                    "https://www.googleapis.com/auth/userinfo.profile",
                    "openid"
                ],
                redirect_to="oauth_callback"  # Send to our callback
            )
            app.register_blueprint(google_bp, url_prefix="/login")
            print("✅ Google OAuth initialized")
        else:
            print("⚠️ No Google OAuth credentials found")
        
        # Setup routes
        self.setup_routes()
    
    def setup_routes(self):
        """Setup authentication routes"""
        
        @self.app.route('/login')
        def login():
            """Login route"""
            # If already logged in, go to chat
            if session.get('user'):
                return redirect(url_for('chat_interface'))
            
            # If OAuth is configured, redirect to Google
            if os.environ.get('GOOGLE_OAUTH_CLIENT_ID'):
                return redirect(url_for('google.login'))
            else:
                # Development mode
                session['user'] = {
                    'email': 'dev@localhost.com',
                    'name': 'Development User'
                }
                if self.db:
                    user = self.db.get_user('dev@localhost.com')
                    if not user:
                        self.db.create_user('dev@localhost.com', 'Development User')
                return redirect(url_for('chat_interface'))
        
        @self.app.route('/logout')
        def logout():
            """Logout user"""
            # Clear Google OAuth token if exists
            if os.environ.get('GOOGLE_OAUTH_CLIENT_ID'):
                try:
                    from flask_dance.consumer import oauth_authorized
                    del session['google_oauth_token']
                except:
                    pass
            
            session.clear()
            flash('You have been logged out.', 'info')
            return redirect(url_for('landing'))
        
        # OAuth callback route
        @self.app.route('/oauth-callback')
        def oauth_callback():
            """Handle OAuth callback"""
            if not google.authorized:
                flash('Authorization failed.', 'error')
                return redirect(url_for('login'))
            
            # Get user info
            resp = google.get("/oauth2/v2/userinfo")
            if not resp.ok:
                flash('Failed to get user info.', 'error')
                return redirect(url_for('login'))
            
            user_info = resp.json()
            
            # Store in session
            session['user'] = {
                'email': user_info['email'],
                'name': user_info.get('name', user_info['email']),
                'picture': user_info.get('picture', '')
            }
            
            # Save to database
            if self.db:
                user = self.db.get_user(user_info['email'])
                if not user:
                    self.db.create_user(user_info['email'], user_info.get('name', ''))
            
            flash(f'Welcome, {session["user"]["name"]}!', 'success')
            return redirect(url_for('chat_interface'))

# Global auth manager
auth_manager = None

def setup_auth(app, db=None):
    """Setup authentication for the app"""
    global auth_manager
    auth_manager = AuthManager(app, db)
    return auth_manager

def get_user():
    """Get current user from session"""
    return session.get('user')

def login_required(f):
    """Decorator to require login"""
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not get_user():
            flash('Please login to access this page.', 'warning')
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

def is_dev_mode():
    """Check if running in development mode"""
    return not os.environ.get('GOOGLE_OAUTH_CLIENT_ID')