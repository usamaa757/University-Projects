<?php
include("header.php");
include("db_connect.php");


$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user by email
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>

<div class="form-main-container" style="margin-bottom: 143px;">
    <div class="form-container">
        <h2>User Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="text-center">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="error"><?php echo $error; ?></div>
        <p style="text-align:center;margin-top:10px;">Don’t have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<?php

include('footer.php');

?>
</body>

</html>