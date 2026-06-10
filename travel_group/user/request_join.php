<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// Check if group exists and is private
$group_check = "SELECT * FROM groups WHERE id = $group_id";
$group_result = mysqli_query($conn, $group_check);

if (!$group_result || mysqli_num_rows($group_result) !== 1) {
    echo "<script>alert('Invalid or non-private group.'); window.location.href='view_groups.php';</script>";
    exit;
}

// Check if already requested
$check_request = "SELECT * FROM group_requests WHERE user_id = $user_id AND group_id = $group_id";
$request_result = mysqli_query($conn, $check_request);

if ($request_result && mysqli_num_rows($request_result) > 0) {
    echo "<script>alert('You have already sent a request for this group.'); window.location.href='view_groups.php?group_id=$group_id';</script>";
    exit;
}

// Insert request
$insert = "INSERT INTO group_requests (user_id, group_id) VALUES ($user_id, $group_id)";
if (mysqli_query($conn, $insert)) {
    echo "<script>alert('Join request sent successfully.'); window.location.href='view_groups.php?group_id=$group_id';</script>";
} else {
    echo "<script>alert('Failed to send request.'); window.location.href='view_groups.php?group_id=$group_id';</script>";
}

mysqli_close($conn);