<?php

include 'db.php';
include 'navbar.php';



$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        // Escape inputs to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);

        // Query user
        $sql = "SELECT * FROM users WHERE university_email='$email' AND is_active=1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                $msg = 'Login successful! Redirecting...';
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header('refresh:2; url=admin_dashboard.php');
                } elseif ($user['role'] == 'faculty') {
                    header('refresh:2; url=faculty_dashboard.php');
                } else {
                    header('refresh:2; url=student_dashboard.php');
                }
            } else {
                $error = 'Incorrect password.';
            }
        } else {
            $error = 'Email not found or account not activated.';
        }
    }
}
?>

<div class="container">
    <h2>Login</h2>

    <?php if (!empty($msg)) { ?>
    <p class="msg" style="color:green"><?php echo $msg; ?></p>
    <?php } ?>

    <?php if (!empty($error)) { ?>
    <p class="msg" style="color:red"><?php echo $error; ?></p>
    <?php } ?>

    <form action="" method="POST">
        <label for="email">University Email</label>
        <input type="email" id="email" name="email" placeholder="example@vu.edu.pk" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p class="note">Don't have an account? <a href="register.php">Register</a></p>
</div>

</body>

</html>