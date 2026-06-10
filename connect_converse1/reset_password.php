<?php
include "db.php";

// 1. After user clicks the reset link in their email with a token like:
// http://yourdomain.com/reset_password.php?token=abc123

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? ");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    var_dump($user['token_expiry']);
    die;

    // After form submission:
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        if ($new_password === $confirm_password) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
            $update->bind_param("ss", $hashed, $token);
            if ($update->execute()) {
                echo "<script>alert('Password reset successful.');window.location.href='login.php';</script>";
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "<script>alert('Passwords do not match.');</script>";
        }
    }
} else {
    echo "<script>alert('Invalid or expired token.');window.location.href='forgot_password.php';</script>";
}
?>

<!-- Registration Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Rest Your Password</h4>
                </div>
                <div class="card-body">

                    <form method="post">

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark">Reset</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>