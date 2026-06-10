<?php
session_start();
include 'header.php';
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'user') {
        $sql = "SELECT * FROM users WHERE email='$email'";
    } else {
        $sql = "SELECT * FROM admin WHERE email='$email'";
    }

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            if ($role == 'user') {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['role'] = $role;

                header("Location: user_dashboard.php");
                exit();
            } else {
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['role'] = $role;

                header("Location: admin_dashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid Credentials');</script>";
        }
    } else {
        echo "<script>alert('Invalid Credentials');</script>";
    }
}
?>

<div class="login-image-container">
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="role">Select Role:</label>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="admin">Admin</option>
                <option value="user">Customer</option>
            </select>
            <input type="email" name="email" placeholder="Email Address" required>

            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

</body>

</html>