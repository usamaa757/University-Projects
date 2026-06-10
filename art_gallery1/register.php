<?php
include 'header.php';
include "db.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $address = trim($_POST["address"]);
    $phone = trim($_POST["phone"]);
    $role = trim($_POST["role"]);


    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='register.php';</script>";
        exit;
    }

    // Check if email or phone already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $check->bind_param("ss", $email, $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email or Phone already exists!'); window.location.href='register.php';</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO users 
    (name, email, password, address, phone, role) 
    VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssss", $name, $email, $hashed_password, $address, $phone, $role);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Registration successful!'); window.location.href='register.php';</script>";
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='register.php';</script>";
        exit;
    }
}

?>


<!-- Registration Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-3">
                <h4 class="text-center">Register</h4>
                <div class="card-body">


                    <form method="post" enctype="multipart/form-data" class="form">

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" name="phone" id="phone" maxlength="11" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="" disabled selected>Select Role</option>
                                <option value="artist">Artist</option>
                                <option value="customer">Customer</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>


                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                                required>
                        </div>

                        <div class="text-center">

                            <button type="submit" class="btn">Register</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>