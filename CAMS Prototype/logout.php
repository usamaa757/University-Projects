<?php
// Start the session
session_start();

// Check if the user is logged in as an admin
if (isset($_SESSION['admin_id'])) {
    // Admin is logged in, redirect to admin login page after session is destroyed
    $redirect_to = 'admin-login.php';
} elseif (isset($_SESSION['student_id'])) {
    // Student is logged in, redirect to student login page after session is destroyed
    $redirect_to = 'user-login.php';
} else {
    // Default redirection (in case no session is found, could be an error page)
    $redirect_to = 'index.php'; // Default page
}

// Destroy the session
session_unset();
session_destroy();

// Redirect based on the role detected from the session
header("Location: $redirect_to");
exit();
