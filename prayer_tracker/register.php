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
    $address = $_POST["address"];


    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Check if email exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ? ");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!');window.location.href='register.php';</script>";
        exit;
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, address ) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $address);

        if ($stmt->execute()) {

            echo "<script>alert('Registration successful! You can login now.');window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
$conn->close();
?>

<!-- Registration Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    <!-- IMPORTANT: Add enctype -->
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                        <div class=" mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <div class="text-center">

                            <button type="submit" class="btn btn-dark">Register</button>
                        </div>
                    </form>
                    <p class="text-center mt-3">
                        Already have an account? <a href="login.php">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>