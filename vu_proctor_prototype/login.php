<?php
include 'navbar.php';
include 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if ($row['approved'] == 0) {
            $error = "Your account is not approved yet by admin.";
        } elseif (password_verify($password, $row['password'])) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['role']      = $row['role'];
            $_SESSION['full_name'] = $row['full_name'];
            header("Location: profile.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>
    <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>
    <p style="text-align:center; margin-top:10px;">No account? <a href="register.php">Register</a></p>
</div>
</body>

</html>