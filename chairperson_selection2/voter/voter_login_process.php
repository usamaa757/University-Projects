<?php
session_start();
include '../db_connection.php';

$errorMsg = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voter_id = $_POST['voter_id'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM voter_registration WHERE voter_id = ? AND registration_status = 'approved'");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Login successful, set session variables
            $_SESSION['voter_id'] = $row['voter_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['department'] = $row['department'];

            // Redirect to the voter dashboard
            header("Location: voter_dashboard.php");
            exit();
        } else {
            $errorMsg = "Invalid Voter ID or password.";
        }
    } else {
        $errorMsg = "Invalid Voter ID or password.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>