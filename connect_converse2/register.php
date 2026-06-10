<?php
include 'header.php';
include "db.php";
require 'mail_config.php';

$msg = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $token = bin2hex(random_bytes(16));

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Profile picture upload
            $profile_pic = "";
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
                $targetDir = "user/uploads/";
                if (!is_dir($targetDir)) {
                    mkdir('user/uploads', 0777, true);
                }

                $fileName = basename($_FILES["profile_pic"]["name"]);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($fileExt, $allowedTypes)) {
                    $newFileName = uniqid() . "." . $fileExt;
                    $targetFilePath = 'uploads/' . $newFileName;

                    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                        $profile_pic = $targetFilePath;
                    } else {
                        $error = "Failed to upload profile picture.";
                    }
                } else {
                    $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                }
            }

            // Insert only if no upload error
            if (empty($error)) {
                // Send email
                $verify_link = "http://localhost/connect_converse/verify_email.php?token=$token";
                $subject = "Verify Your Email";
                $body = "<h3>Click below to verify your email</h3><a href='$verify_link'>$verify_link</a>";
                $result = sendEmail($email, $subject, $body);

                if ($result === true) {
                    // Insert into DB
                    $query = "INSERT INTO users (name, email, password_hash, profile_pic, verification_token)
                              VALUES ('$name', '$email', '$hashed_password', '$profile_pic', '$token')";
                    if (mysqli_query($conn, $query)) {
                        $msg = "Registration successful! Check your email for the verification link.";
                    } else {
                        $error = "Database error: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Email sending failed: $result";
                }
            }
        }
    }

    mysqli_close($conn);
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

                    <?php if (!empty($msg)): ?>
                        <p class="text-success mt-2"><?= $msg ?></p>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <p class="text-danger mt-2"><?= $error ?></p>
                    <?php endif; ?>

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
                            <label>Profile Picture</label>
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