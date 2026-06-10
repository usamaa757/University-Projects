<?php
include 'header.php';
require 'mail_config.php';
include 'db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

    // Check if user exists and is verified
    $check_query = "SELECT is_verified FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user['is_verified'] == 0) {
            $msg = "<p class='text-warning'>Email not verified. Please verify your email first.</p>";
        } else {
            // Send reset email
            $reset_link = "http://localhost/connect_converse/reset_password.php?token=$token";
            $subject = "Reset Your Password";
            $body = "<h3>Click below to reset your password</h3><a href='$reset_link'>$reset_link</a>";

            if (sendEmail($email, $subject, $body) === true) {
                $update_query = "UPDATE users SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
                if (mysqli_query($conn, $update_query)) {
                    $msg = "<p class='text-success'>Password reset link has been sent to your email.</p>";
                } else {
                    $msg = "<p class='text-danger'>Error updating token. Please try again.</p>";
                }
            } else {
                $msg = "<p class='text-danger'>Email sending failed. Please try again later.</p>";
            }
        }
    } else {
        $msg = "<p class='text-danger'>Email not found. Please register first.</p>";
    }

    mysqli_close($conn);
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
                    <?= $msg ?>
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
