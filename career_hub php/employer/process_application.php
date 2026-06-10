<?php
session_start();
require '../db_connect.php';

// Ensure employer is logged in
if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'employer') {
    header("Location: login.php");
    exit();
}

// Check if application ID is set
if (!isset($_POST['application_id'])) {
    header("Location: view_applications.php?error=Invalid request");
    exit();
}

$application_id = intval($_POST['application_id']);
$new_status = '';

if (isset($_POST['approve'])) {
    $new_status = 'Approved';
} elseif (isset($_POST['reject'])) {
    $new_status = 'Rejected';
} elseif (isset($_POST['review'])) {
    $new_status = 'Reviewed';
}
if ($new_status) {
    $stmt = $conn->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $application_id);
    if ($stmt->execute()) {
        header("Location: view_applications.php?success=Status updated");
    } else {
        header("Location: view_applications.php?error=Failed to update");
    }
    $stmt->close();
} else {
    header("Location: view_applications.php?error=Invalid action");
}

$conn->close();