<?php
include '../other/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $parentName = $_POST['Name'];
    $phone = $_POST['phone'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize the inputs
    $parentName = $conn->real_escape_string($parentName);
    $phone = $conn->real_escape_string($phone);
    $student_id = $conn->real_escape_string($student_id);
    $email = $conn->real_escape_string($email);

    // Check if student ID exists in the students table
    $student_check_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
    $student_check_result = $conn->query($student_check_sql);

    if ($student_check_result->num_rows > 0) {
        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the parents table
        $sql = "INSERT INTO parents (parent_name, phone, student_id, email, password, created_at, status)
                VALUES ('$parentName', '$phone', '$student_id', '$email', '$hashed_password', NOW(), 'pending')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration Successful!";
            header("Location: parent_reg.php?msg=" . urlencode($message) . "&success=1");
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            header("Location: parent_reg.php?msg=" . urlencode($message) . "&success=0");
        }
    } else {
        // Student ID not found
        $message = "Error: Student ID does not exist.";
        header("Location: parent_reg.php?msg=" . urlencode($message) . "&success=0");
    }

    // Close the connection
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Registration Form | School Automation System</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <nav>
        <div class="menu">
            <div class="logo">
                <a href="#"><i class="fas fa-school"></i> School Automation System</a>
            </div>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../other/about.php">About</a></li>
                <li><a href="../other/contact.php">Contact</a></li>
                <li><a href="../other/login.php">Login</a></li>
                <li><a href="../other/register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="center" id="content">
        <h3 style="text-align: center; margin-top:20px">Parent Registration Form</h3>

       

        <form action="" method="POST" style="max-width: 600px; margin: auto; padding:10px; color:black;">
             <!-- Display message if available -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="<?= $_GET['success'] == 1 ? 'success' : 'error' ?>">
                <?= htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>
            <label for="name">Parent Name:</label>
            <input type="text" id="name" name="Name" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" maxlength="11" required>

            <label for="student_id">Child's ID:</label>
            <input type="text" id="student_id" name="student_id" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
