<?php
// Establish connection
include "../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_id = $_POST['student_id'];
    $phone = $_POST['phone'];
    $student_email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($password)) {
        // Validate password and confirm password
        if ($password !== $confirm_password) {
            // Passwords don't match, redirect with error message
            header("Location: edit_student_profile.php?student_id=$student_id&error=password_mismatch");
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to update student record including the password
        $stmt = $conn->prepare("UPDATE registration SET student_name = ?, student_email = ?, phone = ?, password = ? WHERE student_id = ?");
        $stmt->bind_param("ssssi", $name, $student_email, $phone, $hashed_password, $student_id);
    } else {
        // Prepare SQL statement to update student record without the password
        $stmt = $conn->prepare("UPDATE registration SET student_name = ?, student_email = ?, phone = ? WHERE student_id = ?");
        $stmt->bind_param("sssi", $name, $student_email, $phone, $student_id);
    }

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Redirect back to edit page with success message
        header("Location: edit_student_profile.php?student_id=$student_id&success=true");
        exit();
    } else {
        // Redirect back to edit page with error message
        header("Location: edit_student_profile.php?student_id=$student_id&error=update_failed");
        exit();
    }
} else {
    // Redirect back to edit page if accessed directly without POST request
    header("Location: edit_student_profile.php");
    exit();
}
?>
