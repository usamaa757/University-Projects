<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

$pageTitle = "Register";

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// Handle registration form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = 'student'; // Default user type
    
    // Validation
    $errors = [];
    
    if(empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if(empty($username)) {
        $errors[] = "Username is required";
    } elseif(strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    }
    
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if(empty($password)) {
        $errors[] = "Password is required";
    } elseif(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if username or email already exists
    if(empty($errors)) {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if($checkResult->num_rows > 0) {
            $errors[] = "Username or email already exists";
        }
        $checkStmt->close();
    }
    
    if(empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $username, $email, $hashed_password, $user_type);
        
        if($stmt->execute()) {
            $success = "Registration successful! You can now login.";
            // Clear form
            $_POST = [];
        } else {
            $error = "Registration failed. Please try again.";
        }
        $stmt->close();
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-user-plus"></i> Create Account</h1>
            <p>Join our academic community today</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php" class="auth-form">
            <div class="form-group">
                <label for="full_name">
                    <i class="fas fa-user-circle"></i> Full Name
                </label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       class="form-control" 
                       placeholder="Enter your full name"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-at"></i> Username
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-control" 
                       placeholder="Choose a username"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required>
                <small class="form-text">Minimum 3 characters</small>
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Enter your email"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="password-input">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Create a password"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="form-text">Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <div class="password-input">
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control" 
                               placeholder="Confirm your password"
                               required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="checkbox">
                    <input type="checkbox" name="newsletter">
                    <span>Subscribe to newsletter for updates</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            
            <div class="auth-divider">
                <span>or sign up with</span>
            </div>
            
            <div class="social-login">
                <button type="button" class="btn-social google">
                    <i class="fab fa-google"></i> Google
                </button>
                <button type="button" class="btn-social github">
                    <i class="fab fa-github"></i> GitHub
                </button>
            </div>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    
    
</div>

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleButton = passwordInput.parentElement.querySelector('.toggle-password i');
    
    if(passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleButton.className = 'fas fa-eye';
    }
}

// Password strength indicator (optional enhancement)
document.getElementById('password')?.addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthBar = document.getElementById('password-strength');
    
    if(!strengthBar) return;
    
    let strength = 0;
    if(password.length >= 6) strength++;
    if(password.length >= 8) strength++;
    if(/[A-Z]/.test(password)) strength++;
    if(/[0-9]/.test(password)) strength++;
    if(/[^A-Za-z0-9]/.test(password)) strength++;
    
    strengthBar.style.width = (strength * 20) + '%';
    strengthBar.className = 'strength-bar strength-' + Math.min(strength, 5);
});
</script>

<?php include 'includes/footer.php'; ?>