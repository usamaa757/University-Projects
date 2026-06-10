<?php
include 'header.php';
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];


    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location='register.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists in users or admins
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email= ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='register.php';</script>";
        exit();
    }


    $stmt = $conn->prepare("INSERT INTO customer (name, address, password, email, phone_number) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $address, $hashed_password, $email, $phone_number);


    if ($stmt->execute() === TRUE) {
        echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

?>


<div class="form-container">
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Enter your name" required>
        <input type="email" name="email" placeholder="Email" required>

        <input type="text" name="phone_number" placeholder="Phone Number">
        <input type="text" name="address" placeholder="Address">

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" class="btn-register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>

</html>