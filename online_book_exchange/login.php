<?php
include("header.php");
include("db_connection.php");
session_start();

$msg = '';
if (isset($_GET['msg'])) {
    $msg = urldecode($_GET['msg']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    // Check if the role is admin or user and query the respective table
    if ($role === 'admin') {
        // Check if the user exists in the admin table
        $user_result = $conn->query("SELECT * FROM admins WHERE email='$email'");
    } elseif ($role === 'user') {
        // Check if the user exists in the users table
        $user_result = $conn->query("SELECT * FROM users WHERE email='$email'");
    } else {
        // Invalid role selected
        header("Location: " . $base_url . "/login.php?msg=" . urlencode("Invalid role selected."));
        exit();
    }

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {

            // Set session variables for admin or user
            if ($role === 'admin') {
                $_SESSION['admin_id'] = $user['admin_id'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_name'] = $user['admin_name'];
                $_SESSION['admin_role'] = $user['role'];

                header("Location: " . $base_url . "admin/admin_dashboard.php");
            } elseif ($role === 'user') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: " . $base_url . "user/user_dashboard.php");
            }
            exit();
        } else {
            // Invalid password
            header("Location: " . $base_url . "/login.php?msg=" . urlencode("Invalid password."));
            exit();
        }
    } else {
        // Email not found in the selected role
        header("Location: " . $base_url . "/login.php?msg=" . urlencode("Email not found for selected role."));
        exit();
    }
}
?>

<!-- Login Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h2 class="text-center heading-bg bg-dark text-white p-2">Login</h2>
                <div class="p-4">
                    <?php if ($msg != ''): ?>
                    <div class="text-danger">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>