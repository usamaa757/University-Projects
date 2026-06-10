<?php
include '../connection.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $sql = "UPDATE user SET status = 'Available' WHERE id = $id";
    } elseif ($action == 'reject') {
        $sql = "UPDATE user SET status = 'Block' WHERE id = $id";
    }

    if (mysqli_query($con, $sql)) {
        echo "<script>alert('User status updated successfully');window.location='View_User.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}