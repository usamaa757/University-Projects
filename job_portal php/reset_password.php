<?php
include "db.php";
include 'header.php';
$token = $_GET['token'] ?? null;
$type = $_GET['type'] ?? null;

if (!$token || !$type) {
    echo "<script>alert('Invalid password reset link.'); window.location.href='forgot_password.php';</script>";
    exit;
}

// Determine the table
$table = $type === 'job_seeker' ? 'job_seekers' : ($type === 'employer' ? 'employers' : null);

if (!$table) {
    echo "<script>alert('Invalid user type.'); window.location.href='forgot_password.php';</script>";
    exit;
}

// Fetch user by token
$stmt = $conn->prepare("SELECT * FROM $table WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Check token expiry
    if (strtotime($user['token_expiry']) < time()) {
        echo "<script>alert('Token has expired.'); window.location.href='forgot_password.php';</script>";
        exit;
    }

    // Handle password reset form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        if ($new_password === $confirm_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE $table SET password_hash = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
            $update->bind_param("ss", $hashed, $token);

            if ($update->execute()) {
                echo "<script>alert('Password reset successful.'); window.location.href='login.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error updating password.');</script>";
            }
        } else {
            echo "<script>alert('Passwords do not match.');</script>";
        }
    }

} else {
    echo "<script>alert('Invalid or expired token.'); window.location.href='forgot_password.php';</script>";
}
?>
    <!-- Password Reset Form -->
<div class="form-container">

  
                    <form method="post" class="forms">
                        <h2>Enter New Password</h2>
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                            <div class="text-center">

                                <button type="submit" class="btn">Reset Password</button>
                            </div>
                    </form>
         
</div>
