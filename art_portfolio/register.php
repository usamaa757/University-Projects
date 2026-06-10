<?php
include 'header.php';

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST["name"];
    $email    = $_POST["email"];
    $role    = $_POST["role"];
    $city    = $_POST["city"];

    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Password match check
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Email check in users
    $checkQuery = "SELECT email FROM users WHERE email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='register.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO users
            (name, email, role, city, password_hash) 
            VALUES 
            ('$name', '$email', '$role', '$city', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}




?>

<div class="form-container">


    <form method="post" class="forms">

        <h2>Registraion</h2>


        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>City</label>

        <input type="text" name="city" required>

        <label>Email Address</label>
        <input type="email" name="email" required>


        <label>Role</label>
        <select name="role" id="role">
            <option value="artist">Artist</option>
            <option value="user">User</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <div class="text-center">
            <button type="submit" class="btn">Register</button>
        </div>
</div>
</form>
</body>

</html>