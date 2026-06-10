<?php
session_start();
require("db_connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query the database for the user
    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session for the user
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];

            // Redirect to the user profile page or dashboard
            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta email="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="CSS/user-login-form.css"> <!-- Include your CSS file -->
</head>

<body>
    <div class="container access-container">
        <!-- Admin Login Form -->
        <div class="access-box">
            <h2>Admin Login</h2>
            <form method="POST">
                <label for="email">email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" name="signin">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
