<?php
include("header.php");
include("db_connect.php");

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = trim($_POST['role']);
    $city = trim($_POST['role']);
    $city = trim($_POST['city']);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, phone, city, address, role)
                VALUES ('$full_name', '$email', '$hashed_password', '$phone', '$city', '$address', '$role')";
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="form-container">
    <h2>Parent Registration</h2>
    <?php

    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } else {
        echo "<div class='message'>$message</div>";
    }
    ?>


    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="phone" placeholder="Phone Number">
        <input type="text" name="city" placeholder="City">
        <textarea name="address" placeholder="Address" rows="3"></textarea>
        <select name="role" id="role">
            <option value="" selected disabled>-- Select Role --</option>
            <option value="parent">Parent</option>
            <option value="worker">Worker</option>
        </select>
        <div class="text-center">

            <button type="submit">Register</button>
        </div>
    </form>
    <p style="text-align:center;margin-top:10px;">Already registered? <a href="login.php">Login here</a></p>
</div>

<?php

include('footer.php');

?>
</body>

</html>