<?php
include '../db_connection.php';
session_start();

// Check if ID is set
if (isset($_GET['cloth_id'])) {
    $cloth_id = $_GET['cloth_id'];

    // Delete query
    $delete_query = "DELETE FROM cloths WHERE cloth_id='$cloth_id'";

    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success_msg'] = "Cloth listing deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to delete the cloth listing.";
    }
}

// Redirect back to dashboard
header("Location: cloths_list.php");
exit();