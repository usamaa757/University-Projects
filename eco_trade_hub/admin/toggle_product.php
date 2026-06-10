<?php
include("../db_connection.php");
session_start();

$part_id = isset($_GET['part_id']) ? intval($_GET['part_id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($part_id === 0 || !in_array($action, ['show', 'hide'])) {
    echo "Invalid part ID or action.";
    exit();
}

// Determine the new status based on the action
$new_status = $action === 'show' ? 'show' : 'hide';

// Update the status in the database
$sql = "UPDATE auto_parts SET status = ? WHERE part_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $part_id);

if ($stmt->execute()) {
    header("Location: manage_products.php?msg=" . urlencode("Product status updated successfully."));
} else {
    header("Location: manage_products.php?error=" . urlencode("Error updating product status: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>
