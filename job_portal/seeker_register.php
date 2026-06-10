<?php
include 'header.php';

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST["name"];
    $email    = $_POST["email"];
    $phone    = $_POST["phone"];
    $location = $_POST["location"];
    $experience = $_POST["experience"];
    $skills   = $_POST["skills"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Password match check
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Email check in both job_seekers and employer tables
    $checkQuery = "SELECT email FROM job_seekers WHERE email = '$email'
                   UNION
                   SELECT email FROM employers WHERE email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='seeker_register.php';</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO job_seekers 
            (name, email, phone, location, experience, skills, password_hash) 
            VALUES 
            ('$name', '$email', '$phone', '$location', '$experience', '$skills', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}




?>
<div class="form-container">


    <form method="post" enctype="multipart/form-data" class="forms">
        <h2>Job Seeker Sign Up</h2>

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Email Address</label>
        <input type="email" name="email" required>

        <label>Phone Number</label>
        <input type="text" name="phone" required>

        <label>Location / City</label>
        <input type="text" name="location" required>

        <label>Experience Level</label>
        <select name="experience" required>
            <option value="">Select</option>
            <option value="Fresher">Fresher</option>
            <option value="1-3 years">1-3 years</option>
            <option value="3-5 years">3-5 years</option>
            <option value="5+ years">5+ years</option>
        </select>

        <label>Skills (comma separated)</label>
        <input type="text" name="skills" required>


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