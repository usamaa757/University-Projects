<?php
include 'navbar.php';
include "config.php";

$error = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $role = $_POST['role']; // buyer or seller

    // Backend Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm) || empty($role)) {
        $error = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    }

    if (!$error) {
        // ✅ Check if email already exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "This email is already registered. Please login instead.";
        } else {
            $hash = md5($password);
            $sql = "INSERT INTO users (name, email, password, role) 
                    VALUES ('$name','$email','$hash','$role')";
            if (mysqli_query($conn, $sql)) {
                $msg = "Registration Successful! You can now login.";
            } else {
                $error = "Something went wrong. Try again!";
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>

        <select name="role" required>
            <option value="" selected disabled>-- Select Role --</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login here</a></p>
</div>

</body>

</html>