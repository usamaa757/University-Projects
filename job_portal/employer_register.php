<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone    = trim($_POST['phone']);

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Basic email check (no prepared statements)
    $email = mysqli_real_escape_string($conn, $email);
    $checkQuery = "
        SELECT email FROM job_seekers WHERE email = '$email'
        UNION
        SELECT email FROM employers WHERE email = '$email'
    ";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already exists!');window.location.href='employer_register.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Escape inputs
    $name = mysqli_real_escape_string($conn, $name);
    $phone = mysqli_real_escape_string($conn, $phone);

    $insertQuery = "
        INSERT INTO employers (name, email, password_hash, phone)
        VALUES ('$name', '$email', '$hashed_password', '$phone')
    ";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Registration successful!');window.location.href='login.php';</script>";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>




<?php
include 'header.php';
?>
<div class="form-container">


    <form method="post" class="forms">
        <h2> Employer Sign Up</h2>
        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Phone</label>
        <input type="text" name="phone">

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