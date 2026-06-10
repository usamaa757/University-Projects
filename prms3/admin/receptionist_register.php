<?php
include 'header.php';
include '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO receptionists (name, email, password, status) 
        VALUES (?, ?, ?, 'accepted')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='manage_receptionist.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
}
$conn->close();
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">📝 Receptionist Registration</h2>
    <div class="row justify-content-center ">
        <div class="col-md-6 border rounded p-4 shadow ">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>


                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
</div>