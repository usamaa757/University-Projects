<?php

include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_name = $_POST['teacher_name'];
    $gender = $_POST['gender'];
    $qualification = $_POST['qualification'];
    $experience = $_POST['experience'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert data into the database
    $sql = "INSERT INTO teachers (teacher_name, gender, qualification, experience, email, password, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $teacher_name, $gender, $qualification, $experience, $email, $password);

    if ($stmt->execute()) {
        $message = "Registration complete! We are currently reviewing your details and will be in touch with you soon.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect to the registration page with a message
    header("Location: teacher_reg.php?msg=" . urlencode($message));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Layout | School Automation System</title> 
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <nav>
        <div class="menu">
            <div class="logo">
                <a href="#"><i class="fas fa-school"></i> School Automation System</a>
            </div>
            <ul>
                <li><a href="../index.php" >Home</a></li>
                <li><a href="../ohter/about.php" >About</a></li>
                <li><a href="../ohter/contact.php" >Contact</a></li>
                <li><a href="../ohter/login.php" >Login</a></li>
                <li><a href="../ohter/register.php" >Register</a></li>
            </ul>
        </div>
    </nav>



    <br><br><br><br><br>
<div class="container" id="formContainer">
    <h2>Please fill out this form</h2>

    <!-- Display message if available -->
    <?php if (isset($_GET['msg'])): ?>
        <p class="message"><?= htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <form id="teacherForm" action="process_registration.php" method="post">
        <label for="teacher_name">Name:</label>
        <input type="text" id="teacher_name" name="teacher_name" required><br><br>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select><br><br>

        <label for="qualification">Qualification:</label>
        <input type="text" id="qualification" name="qualification" required><br><br>

        <label for="experience">Experience (years):</label>
        <input type="number" id="experience" name="experience" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>
</div>

</body>
</html>
