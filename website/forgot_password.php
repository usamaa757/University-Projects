<?php

include 'db_connection.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the database
    $sql = "SELECT * FROM students WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        // Generate a unique reset token
        $reset_token = bin2hex(random_bytes(32));
        $expire_time = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expiration time (1 hour)

        // Store the reset token and expiration time in the database
        $update_sql = "UPDATE students SET reset_token = '$reset_token', reset_token_expiry = '$expire_time' WHERE student_id = '{$student['student_id']}'";
        mysqli_query($conn, $update_sql);

        // Send password reset email with the token
        $reset_link = "https://yourwebsite.com/reset_password.php?token=$reset_token";
        $subject = "Password Reset Request";
        $message = "We received a request to reset your password. Click the link below to reset your password:\n\n$reset_link";
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            $message = "An email has been sent to $email with instructions to reset your password.";
            header("Location: forgot_password.php?status=success&message=" . urlencode($message));
            exit();
        } else {
            $message = "Failed to send reset email. Please try again later.";
            header("Location: forgot_password.php?status=error&message=" . urlencode($message));
            exit();
        }
    } else {
        $message = "No account found with that email address.";
        header("Location: forgot_password.php?status=error&message=" . urlencode($message));
        exit();
    }
}
?>

<?php include "header.php"; ?>

<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center"><span></span>
                Forgot Password<span></span></p>
            <h1 class="text-center mb-5">Forgot Your Password?</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="wow fadeInUp" data-wow-delay="0.3s">
                    <p class="text-center mb-4">Enter your email address and we'll send you a link to reset your
                        password.</p>

                    <?php
                    if (isset($_GET['message'])) {
                        $status = $_GET['status'];
                        $message = htmlspecialchars($_GET['message']);

                        if ($status == 'success') {
                            echo "<p style='color: green;'>" . $message . "</p>";
                        } elseif ($status == 'error') {
                            echo "<p style='color: red;'>" . $message . "</p>";
                        }
                    }
                    ?>

                    <form method="POST" action="forgot_password.php">
                        <div class="col-md-12 mb-4">
                            <div class="form-floating">
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="Your Email" required>
                                <label for="email">Email</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100 py-3" type="submit">Send Reset Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>