<?php
session_start();

$errorMsg = ''; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    include '../db_connection.php';


    // Retrieve email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Perform the database query for admin login
    $admin_query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($admin_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Admin exists, verify password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session and redirect to admin dashboard
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $row['name'];
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['profile_pic'] = $row['pic'];
            header("Location: ../Admin/admin_dashboard.php");
            exit();
        } else {
            // Password is incorrect
            $errorMsg = 'Password is incorrect';
        }
    } else {
        // Admin not found
        $errorMsg = 'Admin not found';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} 
?>
