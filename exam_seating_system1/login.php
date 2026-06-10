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




<div class="main">

    <h2>Login</h2>


    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login" class="btn">Login</button>
    </form>
    <a href="register.php">Don't have account</a>
</div>

</body>

</html>