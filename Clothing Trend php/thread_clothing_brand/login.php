<?php
include 'header.php';

// Include the database connection file
include 'db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect user inputs and sanitize them
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine which table to query based on the role
    if ($role == 'User') {
        $query = "SELECT * FROM users WHERE email = '$email'";
    } elseif ($role == 'Admin') {
        $query = "SELECT * FROM admins WHERE email = '$email'";
    } else {
        $_SESSION['error_msg'] = "Invalid role selected. Please try again.";
        header("Location: login.php");
        exit();
    }

    // Execute the query
    $result = mysqli_query($conn, $query);

    // Check if the email exists in the selected table
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            if ($role === 'Admin') {

                // Set session variables for the logged-in user
                $_SESSION['admin_id'] = $user['admin_id'];
                $_SESSION['admin_name'] = $user['admin_name'];
                $_SESSION['admin_role'] = $role;

                header("Location: admin/dashboard.php");
                exit();
            } else {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_role'] = $role;
                header("Location: user/dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error_msg'] = "Invalid password. Please try again.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "No user found with the provided email and role.";
        header("Location: login.php");
        exit();
    }
}

// Close the database connection
mysqli_close($conn);
?>

<div class="container round border shadow p-3 mt-5" style="max-width: 500px;">
    <h3>Login</h3>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_msg']; ?>
        <?php unset($_SESSION['error_msg']); ?>
    </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <!-- Email -->
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Role Selection -->
        <div class="form-group">
            <label for="role">Login as</label>
            <select class="form-control" id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <div class="text-center">

            <button type="submit" class="btn bg-primary text-white">Login</button>
        </div>
    </form>

    <p class="text-center mt-3">Don't have an account? <a href="register_user.php">Register</a></p>
</div>