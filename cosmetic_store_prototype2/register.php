<?php
include 'header.php';
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $second_name = $_POST['second_name'];
    $address = $_POST['address'];
    $email_id = $_POST['email_id'];
    $phone_number = $_POST['phone_number'];
    $town = $_POST['town'];
    $region = $_POST['region'];
    $postcode_zip = $_POST['postcode_zip'];
    $country = $_POST['country'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location='register.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists in users or admins
    $check_email = "SELECT * FROM users WHERE email_id='$email_id' UNION SELECT * FROM admins WHERE email_id='$email_id'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='register.php';</script>";
        exit();
    }

    // Insert based on role selection
    if ($role == "admin") {
        $sql = "INSERT INTO admins (first_name, second_name, address, password, email_id, phone_number, town, region, postcode_zip, country)
                VALUES ('$first_name', '$second_name', '$address', '$hashed_password', '$email_id', '$phone_number', '$town', '$region', '$postcode_zip', '$country')";
    } else {
        $sql = "INSERT INTO users (first_name, second_name, address, password, email_id, phone_number, town, region, postcode_zip, country)
                VALUES ('$first_name', '$second_name', '$address', '$hashed_password', '$email_id', '$phone_number', '$town', '$region', '$postcode_zip', '$country')";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>


<div class="form-container">
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="second_name" placeholder="Second Name" required>
        <input type="email" name="email_id" placeholder="Email" required>

        <input type="text" name="phone_number" placeholder="Phone Number">
        <input type="text" name="address" placeholder="Address">
        <input type="text" name="town" placeholder="Town">
        <input type="text" name="region" placeholder="Region">
        <input type="text" name="postcode_zip" placeholder="Postcode/ZIP">
        <input type="text" name="country" placeholder="Country">
        <select name="role" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" class="btn-register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>

</html>