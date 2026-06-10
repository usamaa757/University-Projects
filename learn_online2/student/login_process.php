<?php
session_start();


include '../include_files/db_connection.php';

// Initialize error message
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve email and password from login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM registration WHERE student_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, verify password
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct, allow login

            // Start session and set session variables
        
            $_SESSION['student_name'] = $row['student_name']; 
            $_SESSION['student_id'] = $row['student_id']; 
            $_SESSION['student_email'] = $email; 
            $_SESSION['course_id'] = $row['course_id']; 

            $student_id =  $_SESSION['student_id'];
            $check_stmt = $conn->prepare("SELECT * FROM student_course WHERE student_id = ?");
            $check_stmt->bind_param("i", $student_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            if ($check_result->num_rows > 0) {
                // Redirect to dashboard if combination exists
                header("Location: student_dashboard.php");
                exit();
        
            } 
        } else {
            // Password is incorrect, display error message
            $errorMsg = "Invalid password.";
        }
    } else {
        // User does not exist, display error message
        $errorMsg = "Student is not registered or approved.";
    }

    // Close statement and connection
    $stmt->close();
}
    $conn->close();