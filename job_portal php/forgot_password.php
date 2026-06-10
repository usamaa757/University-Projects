<?php

include 'header.php';
require 'mail_config.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    // Check if the email exists in job_seekers or employers
    $check_job_seeker = mysqli_query($conn, "SELECT * FROM job_seekers WHERE email = '$email'");
    $check_employer = mysqli_query($conn, "SELECT * FROM employers WHERE email = '$email'");

    if (mysqli_num_rows($check_job_seeker) > 0) {
        // Update token in job_seekers table
        $update = mysqli_query($conn, "UPDATE job_seekers SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'");
        $user_type = "job_seeker";
    } elseif (mysqli_num_rows($check_employer) > 0) {
        // Update token in employers table
        $update = mysqli_query($conn, "UPDATE employers SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'");
        $user_type = "employer";
    } else {
        echo "<script>alert('Email not found. Please try different email.');window.location.href='forgot_password.php';</script>";
        exit;
    }

    // Send reset link
    $verify_link = "http://localhost/job_portal/reset_password.php?token=$token&type=$user_type";
    $subject = "Reset Your Password";
    $body = "<h3>Click below to reset your password</h3><a href='$verify_link'>$verify_link</a>";

    $result = sendEmail($email, $subject, $body);

    if ($result === true) {
        echo "<script>alert('Password reset link has been sent to your email.');window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Email sending failed: $result');window.location.href='forgot_password.php';</script>";
    }

    mysqli_close($conn);
}
?>



<div class="form-container">

    <form method="POST" class="forms">
        <h2>Reset the Password</h2>
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
        <div class="text-center">

            <button type="submit" class="btn btn-dark">Send Reset Link</button>
        </div>
</div>