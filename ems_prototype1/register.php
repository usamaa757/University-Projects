<?php
require 'db.php';

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cnic = $_POST['cnic'];
    $study_program = $_POST['study_program'];
    $about_me = $_POST['about_me'];

    // Check if email or CNIC already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR cnic = ?");
    $stmt->bind_param("ss", $email, $cnic);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = " Error: Email or CNIC already exists!";
    } else {
        // Password validation
        if (!preg_match('/^(?=.*[A-Z])(?=.*\W).{8,}$/', $password)) {
            $error = " Password must be at least 8 characters, with 1 uppercase and 1 special character.";
        } elseif (!preg_match('/^\d{13}$/', $cnic)) {
            $error = " CNIC must be exactly 13 digits.";
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Profile Picture Upload
            $profile_pic = "";
            if (!empty($_FILES["profile_pic"]["name"])) {
                $target_dir = "images/";

                // Ensure directory exists
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                // Generate unique file name
                $profile_pic = uniqid() . "_" . basename($_FILES["profile_pic"]["name"]);
                $target_file = $target_dir . $profile_pic;

                // Validate file type (only images allowed)
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                if (in_array($file_type, ["jpg", "jpeg", "png", "gif"])) {
                    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
                } else {
                    $error = " Only JPG, JPEG, PNG, and GIF files are allowed.";
                    $profile_pic = "";
                }
            }

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, profile_pic, password_hash, cnic, study_program, about_me) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $full_name, $email, $profile_pic, $password_hash, $cnic, $study_program, $about_me);

            if ($stmt->execute()) {
                $msg = " Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = " Database Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">EMS Prototype</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <div class="container">
        <h3>Register Here</h3>

        <!-- Display Message -->
        <?php if (!empty($msg)) { ?>
        <div class="message"><?= $msg; ?></div>
        <?php }
        ?>
        <?php if (!empty($error)) { ?>
        <div class="error"><?= $error; ?></div>
        <?php } ?>


        <form action="register.php" method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="full_name" pattern="^[A-Za-z\s]{3,30}$" required
                title="Must be 3-30 characters (letters & spaces only)" placeholder="Enter your full name">

            <label>Email:</label>
            <input type="email" name="email" required placeholder="Enter your email">

            <label>Profile Picture:</label>
            <input type="file" name="profile_pic" required accept="image/*">

            <label>Password:</label>
            <input type="password" name="password" required pattern="^(?=.*[A-Z])(?=.*\W).{8,}$"
                title="At least 8 characters, 1 uppercase, 1 special character" placeholder="Enter your password">

            <label>CNIC:</label>
            <input type="text" name="cnic" maxlength="13" pattern="\d{13}" required title="CNIC must be 13 digits"
                placeholder="Enter your CNIC">

            <label>Study Program:</label>
            <select name="study_program" required>
                <option value="">Select</option>
                <option>BSCS</option>
                <option>BSIT</option>
                <option>BSSE</option>
                <option>BSDS</option>
                <option>BSMGT</option>
                <option>BSAI</option>
                <option>MCS</option>
                <option>MIT</option>
                <option>ADPCS</option>
                <option>MSCS</option>
            </select>

            <label>About Me:</label>
            <textarea name="about_me" required placeholder="About me"></textarea>
            <div class="button">
                <button type="submit">Signup</button>
                <button type="reset">Clear All</button>
            </div>
        </form>
    </div>

</body>

</html>