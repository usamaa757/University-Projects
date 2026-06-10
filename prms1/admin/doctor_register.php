<?php
include 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $department = trim($_POST['department']);
    $phone      = trim($_POST['phone']);
    $password   = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Connect to DB
    include '../config/database.php';
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "<script>alert('Email already registered'); window.history.back();</script>";
        exit;
    }
    $stmt->close();

    // Insert doctor
    $stmt = $conn->prepare("INSERT INTO doctors (name, email, department, phone, password, status='accepted') VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $department, $phone, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful'); window.location.href='manage_doctor.php';</script>";
    } else {
        echo "<script>alert('Error: Registration failed'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">🩺 Doctor Registration</h2>
    <div class="row justify-content-center">
        <div class="col-md-6 border rounded p-4 shadow ">
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
        </div>
    </div>
</div>


</body>

</html>