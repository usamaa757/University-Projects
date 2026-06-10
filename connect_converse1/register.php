<?php
include 'header.php';
include "db.php";
require 'mail_config.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and get input
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $token = bin2hex(random_bytes(16));

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Check if email exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!');window.location.href='register.php';</script>";
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle profile picture upload
    $profile_pic = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $targetDir = "user/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["profile_pic"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedTypes)) {
            $newFileName = uniqid() . "." . $fileExt;
            $targetFilePath = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                $profile_pic = $targetFilePath;
            } else {
                echo "<script>alert('Failed to upload profile picture.');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG, and GIF files are allowed.');</script>";
            exit;
        }
    }

    // Insert into database
    $verify_link = "http://localhost/connect_converse/verify_email.php?token=$token";
    $subject = "Verify Your Email";
    $body = "<h3>Click below to verify your email</h3><a href='$verify_link'>$verify_link</a>";

    $result = sendEmail($email, $subject, $body);


    if ($result === true) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, profile_pic, verification_token) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashed_password, $profile_pic, $token);

        if ($stmt->execute()) {

            echo "<script>alert('Registration successful! Check your email for verification link.');window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "<script>alert( 'Email sending failed: $result');window.location.href='register.php';</script>";
    }

    $conn->close();
}
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
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_pic" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
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