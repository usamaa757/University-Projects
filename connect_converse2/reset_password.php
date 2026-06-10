<?php
include "db.php";
include "header.php";

$msg = "";
$show_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $token = mysqli_real_escape_string($conn, $token);
    $query = "SELECT * FROM users WHERE reset_token = '$token'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $show_form = true;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $new_password = $_POST["new_password"];
            $confirm_password = $_POST["confirm_password"];

            if ($new_password === $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET password_hash = '$hashed', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
                if (mysqli_query($conn, $update_query)) {
                    $msg = "<p class='text-success'>Password reset successful. <a href='login.php'>Login</a></p>";
                    $show_form = false;
                } else {
                    $msg = "<p class='text-danger'>Error updating password. Please try again later.</p>";
                }
            } else {
                $msg = "<p class='text-warning'>Passwords do not match.</p>";
            }
        }
    } else {
        $msg = "<p class='text-danger'>Invalid or expired token.</p>";
    }
} else {
    $msg = "<p class='text-danger'>Token is missing.</p>";
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Reset Your Password</h4>
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <?php if ($show_form): ?>

                        <form method="post">
                            <div class="mb-3">
                                <label>New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-dark">Reset</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>