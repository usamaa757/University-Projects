<?php
require '../db.php';
session_start();


$admin_id = $_SESSION['admin_id'];
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($group_id <= 0) {
    echo "<script>alert('Invalid group ID.'); window.location.href='my_created_groups.php';</script>";
    exit;
}

// Verify the group belongs to the logged-in user
$check_query = "SELECT * FROM groups WHERE id = $group_id AND created_by = $admin_id";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    echo "<script>alert('You are not authorized to delete this group.'); window.location.href='my_created_groups.php';</script>";
    exit;
}

// Delete related chat messages
mysqli_query($conn, "DELETE FROM group_chat WHERE group_id = $group_id");

// Delete related join requests
mysqli_query($conn, "DELETE FROM group_requests WHERE group_id = $group_id");

// Delete the group itself
mysqli_query($conn, "DELETE FROM groups WHERE id = $group_id");

echo "<script>alert('Group deleted successfully.'); window.location.href='my_created_groups.php';</script>";
exit;