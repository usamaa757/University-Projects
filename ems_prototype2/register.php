<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $cnic = $_POST["cnic"];
    $study_program = $_POST["study_program"];
    $about_me = $_POST["about_me"];
    $profile_pic = "";

    // Validate inputs
    if (!preg_match("/^[a-zA-Z]{3,12}$/", $fullname)) {
        echo "<script>alert('Invalid Full Name! No spaces allowed.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid Email format');</script>";
    } elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[\W]/", $password)) {
        echo "<script>alert('Password must be at least 8 characters, with an uppercase letter and a special character.');</script>";
    } elseif (!preg_match("/^\d{13}$/", $cnic)) {
        echo "<script>alert('Invalid CNIC format. It must contain 13 digits.');</script>";
    } else {
        // Check if email or CNIC already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR cnic = ?");
        $stmt->bind_param("ss", $email, $cnic);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email or CNIC already exists! Please use a different one.');</script>";
        } else {
            // Handle profile picture upload
            if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
                $upload_dir = "uploads/";
                $file_name = basename($_FILES["profile_pic"]["name"]);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_exts = ["jpg", "jpeg", "png", "gif"];

                if (in_array($file_ext, $allowed_exts)) {
                    $new_file_name = uniqid() . "." . $file_ext; // Generate unique file name
                    $target_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_path)) {
                        $profile_pic = $new_file_name;
                    } else {
                        echo "<script>alert('Error uploading profile picture.');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid image format. Only JPG, JPEG, PNG, and GIF allowed.');</script>";
                }
            }

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into database
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, cnic, study_program, about_me, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $fullname, $email, $password_hash, $cnic, $study_program, $about_me, $profile_pic);

            if ($stmt->execute()) {
                echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Error registering user');</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Log In</a>
    </nav>

    <div class="container">
        <h2>Register</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="fullname" placeholder="Full Name (No Spaces)" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password (8+ chars, 1 Uppercase, 1 Special Char)"
                required>
            <input type="text" name="cnic" maxlength="13" placeholder="CNIC (13 digits)" required>

            <select name="study_program">
                <option value="BSCS">BSCS</option>
                <option value="BSIT">BSIT</option>
                <option value="BSSE">BSSE</option>
                <option value="BSDS">BSDS</option>
                <option value="MIT">MIT</option>
                <option value="BSMGT">BSMGT</option>
                <option value="BSAI">BSAI</option>
                <option value="MCS">MCS</option>
                <option value="ADPCS">ADPCS</option>
                <option value="MSCS">MSCS</option>
            </select>

            <input type="file" name="profile_pic" required accept="image/*">

            <textarea name="about_me" placeholder="Tell us about yourself"></textarea>

            <button type="submit">Signup</button>
            <button type="reset">Clear All</button>
        </form>
    </div>

</body>

</html>