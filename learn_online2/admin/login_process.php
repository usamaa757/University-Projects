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
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, verify password
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Password is correct, allow login

                // Redirect to dashboard if combination exists
                header("Location: admin_dashboard.php");
                exit();
        
        
        } else {
            // Password is incorrect, display error message
            echo '<script>alert("Incorrect Email or Password!"); window.location="admin_login.php"; </script>)';

        }
    } else {
        // User does not exist, display error message
        $errorMsg = "Student is not registered or approved.";
    }

    // Close statement and connection
    $stmt->close();
}
    $conn->close();