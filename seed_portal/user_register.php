<?php
include 'header.php';
include 'db_connect.php';
$message = '';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // simple encryption
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $message = "⚠️ Email already registered. Please log in.";
    } else {
        $sql = "INSERT INTO users (name, email, password, contact, address)
                VALUES ('$name', '$email', '$password', '$contact', '$address')";
        if ($conn->query($sql) === TRUE) {
            $message = "✅ Registration successful! You can now log in.";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>
<div class="container">

    <h2>🌱 User Registration</h2>
    <p style="color:green; text-align:center;"><?php echo $message; ?></p>

    <form method="POST" class="register-form">
        <label>Full Name:</label>
        <input type="text" name="name" placeholder="Enter your full name" required>

        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password" required>

        <label>Contact No:</label>
        <input type="text" name="contact" placeholder="03xx-xxxxxxx" required>

        <label>Address:</label>
        <textarea name="address" placeholder="Your location / city" required></textarea>

        <button type="submit" name="register" class="btn">Register</button>
    </form>

    <p style="text-align:center;">Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>

</html>