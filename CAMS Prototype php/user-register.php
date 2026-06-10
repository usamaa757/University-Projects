<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $retype_password = mysqli_real_escape_string($conn, $_POST['retype_password']);

    // Check if passwords match
    if ($password !== $retype_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href = 'user-login.php';
                  </script>";
    } 
    else {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if email is already registered
        $checkUser = "SELECT * FROM users_reg WHERE email = '$email'";
        $result = mysqli_query($conn, $checkUser);
        $count = mysqli_num_rows($result);

        if ($count > 0) {
            // Use JavaScript to alert and then redirect
            echo "<script>
                    alert('This Email is Already Registered!');
                    window.location.href = 'user-login.php';
                  </script>";
            exit(); // Stop further execution
        } 
        else {
            // Insert the data into the database
            $sql = "INSERT INTO users_reg (full_name, email, password) VALUES ('$full_name', '$email', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {
                // Use JavaScript to show success message
                echo "<script>alert('Registration successful! You can now log in.');
                window.location.href = 'user-login.php';
                  </script>";
            } 
            else {
                echo "<script>alert('Error: Could not register. Please try again.');
                window.location.href = 'user-login.php';
                  </script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- <link rel="stylesheet" href="CSS/style.css"> -->
    <link rel="stylesheet" href="CSS/user-login-form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <div class="container access-container">
        <!-- Registration Form -->
        <div class="access-box">
            <h2>User Registration</h2>
            <form action="user-register.php" method="POST">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" name="full_name" placeholder="Enter your full name" required>

                <label for="reg-email">Email</label>
                <input type="email" id="reg-email" name="email" placeholder="Enter your email" required>

                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" name="password" placeholder="Enter your password" required>

                <label for="retype-password">Retype Password</label>
                <input type="password" id="retype-password" name="retype_password" placeholder="Retype your password"
                    required>

                <button type="submit">Register</button>
                <div class="click">
                    <p>Already have an account?</p>
                    <a href="user-login.php">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>