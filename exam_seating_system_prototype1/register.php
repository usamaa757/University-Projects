<?php
include 'db.php';

include 'header.php';
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')");
        if ($insert) {
            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error in registration!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="main">
        <h2>Register</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="register" class="btn">Register</button>
        </form>
    </div>

</body>

</html>