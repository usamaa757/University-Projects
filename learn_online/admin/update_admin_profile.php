<?php
// Establish connection
include "../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $admin_id = $_POST['admin_id'];
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Prepare SQL statement based on whether password is provided or not
    if (!empty($password)) {
        // Validate password and confirm password
        if ($password !== $confirm_password) {
            // Passwords don't match, redirect with error message
            header("Location: admin_profile_process.php?admin_id=$admin_id&error=password_mismatch");
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to update admin record including the password
        $stmt = $conn->prepare("UPDATE admin SET name = ?, password = ? WHERE admin_id = ?");
        $stmt->bind_param("ssi", $name, $hashed_password, $admin_id);
    } else {
        // Prepare SQL statement to update admin record without changing the password
        $stmt = $conn->prepare("UPDATE admin SET name = ? WHERE admin_id = ?");
        $stmt->bind_param("si", $name, $admin_id);
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        // Redirect back to edit page with success message
        header("Location: admin_profile.php?admin_id=$admin_id&success=true");
        exit();
    } else {
        // Redirect back to edit page with error message
        header("Location: admin_profile.php?admin_id=$admin_id&error=update_failed");
        exit();
    }
} else {
    // Redirect back to edit page if accessed directly without POST request
    header("Location: admin_profile.php");
    exit();
}
?>
