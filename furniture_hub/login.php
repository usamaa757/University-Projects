<?php
include 'navbar.php';
include "config.php";

$error = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $passwordHash = md5($password);

        $sql = "SELECT * FROM users WHERE email='$email' AND password='$passwordHash' AND status ='active'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>

<div class="form-container">
    <h2>Sign In</h2>

    <?php if (!empty($error)): ?>
    <div class="error-box"> <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <button type="submit" name="login">Login</button>
    </form>
    <p>New user? <a href="index.php">Register here</a></p>
</div>

</body>

</html>