<?php
include 'header.php';
require 'mail_config.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));


    $stmt = $conn->prepare("SELECT is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 0) {
            echo "<script>alert('Email not verified. Please verify your email first.');window.location.href='register.php';</script>";
            exit;
        } else {
            // Email is verified, continue processing
        }
    } else {
        echo "<script>alert('Email not found. Please register.');window.location.href='register.php';</script>";
        exit;
    }




    // Insert into database
    $verify_link = "http://localhost/connect_converse/reset_password.php?token=$token";
    $subject = "Verify Your Email";
    $body = "<h3>Click below to reset your password</h3><a href='$verify_link'>$verify_link</a>";

    $result = sendEmail($email, $subject, $body);


    if ($result === true) {
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        if ($stmt->execute()) {

            echo "<script>alert('Password reset link has been sent to you given email.');window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "<script>alert( 'Email sending failed: $result');window.location.href='register.php';</script>";
    }

    $conn->close();
}


?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Forgot Password</h4>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn btn-dark">Send Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>