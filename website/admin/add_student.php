<?php
ob_start(); // Start output buffering
include 'header.php';
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($password != $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: add_student.php?status=error&error=" . urlencode($_SESSION['error']));
        exit();
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into students table
        $sql = "INSERT INTO students (name, email, degree, semester, password) VALUES ('$name', '$email', '$degree', '$semester', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Registration successful!";
            header("Location: add_student.php?status=success&success=" . urlencode($_SESSION['success']));
        } else {
            $_SESSION['error'] = "Error: Could not register.";
            header("Location: add_student.php?status=error&error=" . urlencode($_SESSION['error']));
        }
        exit();
    }
}
?>
<br><br><br><br><br>
<div class="container border rounded shadow p-0 w-50">
    <div class="bg-primary text-center">
        <h3 class="text-center text-white p-2">Add New Student</h3>
    </div>
    <div class="p-2">

        <a href="students.php" class="btn btn-block btn-primary">Student List</a>
    </div>
    <div class="p-3">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="text text-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']); // Clear message after displaying
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="text text-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']); // Clear message after displaying
        }
        ?>
    </div>

    <form method="POST" class="p-4">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="degree" class="form-label">Degree:</label>
            <input type="text" name="degree" id="degree" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="semester" class="form-label">Semester:</label>
            <input type="number" name="semester" id="semester" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="confirm_password" class="form-label">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block">Add Student</button>
        </div>
    </form>
</div>

<?php

ob_end_flush(); // Flush the output buffer and turn off output buffering
?>