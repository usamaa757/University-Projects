<?php
include 'header.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $disease = trim($_POST['disease']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $email = $_POST['email'];

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match'); window.location='register_patient.php';</script>";
        exit;
    }

    $check = $conn->prepare("SELECT * FROM patients WHERE contact = ? OR email = ?");
    $check->bind_param("ss", $contact, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Patient already registered with this contact or Email!'); window.location='register_patient.php';</script>";
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO patients (name, email, age, gender, contact, address, disease, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssss", $name, $email, $age, $gender, $contact, $address, $disease, $password_hash);

    if ($stmt->execute()) {
        echo "<script>alert('Patient registration successful!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error occurred');</script>";
    }
}
?>

<!-- HTML Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4">
                <h4 class="text-center">Patient Registration</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" maxlength="11" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Disease / Symptoms</label>
                        <input type="text" name="disease" class="form-control" required>
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