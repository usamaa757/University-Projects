<?php
include 'header.php';
include "db.php";

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email' AND is_verified = 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION['user_id'] =  $user['user_id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>



<!-- HTML Login Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center bg-dark text-white">
                    <h4>Login</h4>

                </div>
                <div class="card-body">
                    <?php if (!empty($msg)): ?>
                        <p class="text-success mt-2"><?= $msg ?></p>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <p class="text-danger mt-2"><?= $error ?></p>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-dark">Login</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">
                        Don't have an account? <a href="register.php">Register</a><br>
                        <a href="forgot_password.php">Forgot password</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>