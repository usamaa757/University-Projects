<?php
include "header/header.php";
include 'connection.php';

if (isset($_POST['submit'])) {
    $email    = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Check if approved user exists
    $sql = "SELECT * FROM user WHERE email='$email' AND password='$password' AND (status='Available' OR status='Leave')";
    $run = mysqli_query($con, $sql);

    if (mysqli_num_rows($run) > 0) {
        $row = mysqli_fetch_assoc($run);

        // Store session data
        $_SESSION['id']          = $row['id'];
        $_SESSION['employee_id'] = $row['employee_id'];
        $_SESSION['role']        = $row['role']; // Superintendent OR Invigilator
        $_SESSION['name']        = $row['name'];
        $_SESSION['email']       = $row['email'];
        $_SESSION['city']        = $row['city'];

        // Redirect based on role
        if ($row['role'] == "Superintendent") {
            echo "<script>alert('Superintendent Login Successfully!'); window.location='superintendent/dashboard.php';</script>";
        } elseif ($row['role'] == "Invigilator") {
            echo "<script>alert('Invigilator Login Successfully!'); window.location='invigilator/dashboard.php';</script>";
        } else {
            echo "<script>alert('Role not assigned properly. Contact Admin.'); window.location='User_Login.php';</script>";
        }
        exit;
    }

    // If user exists but not approved
    $sql2 = "SELECT * FROM user WHERE email='$email' AND password='$password' AND status=0";
    $run2 = mysqli_query($con, $sql2);

    if (mysqli_num_rows($run2) > 0) {
        echo "<script>alert('Your account is pending approval by admin. Please wait.'); window.location='User_Login.php';</script>";
        exit;
    }

    // Invalid login
    echo "<script>alert('Invalid Email or Password'); window.location='User_Login.php';</script>";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title> User Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>

    <br><br>

    <div class="container p-3 my-3 bg-info" style="border-radius:15px; width:40%">
        <br>
        <h2 style="color:black;text-align:center;">User Login</h2>
        <br>




        <form action="" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" name="submit" class="btn btn-success ">Login</button>
        </form>

    </div>
    </div>
</body>

</html>