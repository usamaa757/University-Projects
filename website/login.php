<?php
require_once 'includes/config.php';
require_once 'includes/db_connection.php';

$pageTitle = "Login";

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check user credentials
        $stmt = $conn->prepare("SELECT id, username, password, user_type, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];
                if ($_SESSION['is_admin'] == 1) {
                    // Redirect to dashboard
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    // Redirect to dashboard
                    header("Location: user_dashboard.php");
                    exit();
                }
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-sign-in-alt"></i> Login</h1>
            <p>Sign in to your account to access resources</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="auth-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Username or Email
                </label>
                <input type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Enter your username or email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="password-input">
                    <input type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="auth-divider">
                <span>or continue with</span>
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
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
        </div>
    </div>


</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.toggle-password i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleButton.className = 'fas fa-eye';
        }
    }
</script>

<?php include 'includes/footer.php'; ?>