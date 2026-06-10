<?php
// Establish connection
include "../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
 

    if (!empty($password)) {
        // Validate password and confirm password
        if ($password !== $confirm_password) {
            // Passwords don't match, redirect with error message
            header("Location: student_profile_process.php?student_id=$student_id&error=password_mismatch");
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to update student record including the password
        $stmt = $conn->prepare("UPDATE registration SET phone = ?, password = ? WHERE student_id = ?");
        $stmt->bind_param("ssi", $phone, $hashed_password, $student_id);
    } else {
        $stmt = $conn->prepare("UPDATE registration SET phone = ? WHERE student_id = ?");
        $stmt->bind_param("si",$phone, $student_id);
    }

    if ($stmt->execute()) {
        // Redirect back to edit page with success message
        header("Location: student_profile.php?student_id=$student_id&success=true");
        exit();
    } else {
        // Redirect back to edit page with error message
        header("Location: student_profile.php?student_id=$student_id&error=update_failed");
        exit();
    }
} else {
    // Redirect back to edit page if accessed directly without POST request
    header("Location: student_profile.php");
    exit();
}
?>
