<?php
include '../db_connection.php';
session_start();

// Check if ID is set
if (isset($_GET['id'])) {
    $plant_id = $_GET['id'];

    // Delete query
    $delete_query = "DELETE FROM plants WHERE id='$plant_id' AND seller_id='{$_SESSION['user_id']}'";

    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success_msg'] = "Plant listing deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Failed to delete the plant listing.";
    }
}

// Redirect back to dashboard
header("Location: plants_list.php");
exit();
?>