<?php
include 'db.php';
include 'header.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check in Admins table
    $admin_query = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");
    $admin = mysqli_fetch_assoc($admin_query);

    // Check in Students table
    $student_query = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");
    $student = mysqli_fetch_assoc($student_query);

    if ($admin && password_verify($password, $admin['password'])) {
        // Admin Login
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['admin_name'];
        $_SESSION['user_type'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($student && password_verify($password, $student['password'])) {
        // Student Login
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['student_name'] = $student['student_name'];
        $_SESSION['user_type'] = 'student';
        header("Location: student_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password!');</script>";
    }
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg p-4">
                <h2 class="text-center mb-4">Login</h2>
                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="register.php">Don't have an account?</a>
                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>