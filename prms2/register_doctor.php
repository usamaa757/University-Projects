<?php
include 'header.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match!'); window.location='register_doctor.php';</script>";
        exit;
    }

    // Check existing
    $check = $conn->prepare("SELECT * FROM doctors WHERE email = ? OR phone = ?");
    $check->bind_param("ss", $email, $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email or Phone already exists!'); window.location='register_doctor.php';</script>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $specialization, $email, $phone, $password_hash);

    if ($stmt->execute()) {
        echo "<script>alert('Doctor registered successfully!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error occurred.');</script>";
    }
}
?>

<!-- HTML Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4">
                <h4 class="text-center">Doctor Registration</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" maxlength="11" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary" type="submit">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>