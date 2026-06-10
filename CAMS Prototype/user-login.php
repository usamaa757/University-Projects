<?php
// Start the session
session_start();

// Include the database connection file
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query the database for the user
    $sql = "SELECT * FROM users_reg WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session for the user
            $_SESSION['student_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect to the user profile page or dashboard
            header("Location: stud_dashboard.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-login</title>
    <!-- <link rel="stylesheet" href="CSS/style.css"> -->
    <link rel="stylesheet" href="CSS/user-login-form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    
    <div class="container access-container">
        <!-- Login Form -->
        <div class="access-box">
            <h2>User Login</h2>
            <form action="user-login.php" method="POST">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" name="email" placeholder="Enter your email" required>

                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
                <div class="click">
                    <p>Not Registered Yet?</p>
                    <a href="user-register.php">Click here</a>
                </div>

            </form>
        </div>
</body>
</html>