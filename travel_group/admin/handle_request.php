<?php
session_start();
require '../db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($request_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    echo "<script>alert('Invalid request.'); window.location.href='my_created_groups.php';</script>";
    exit;
}

$status = ($action === 'approve') ? 'approved' : 'rejected';

$update = "UPDATE group_requests SET status = '$status' WHERE id = $request_id";
if (mysqli_query($conn, $update)) {
    echo "<script>alert('Request $status successfully.'); window.location.href='my_created_groups.php';</script>";
} else {
    echo "<script>alert('Error updating request.'); window.location.href='my_created_groups.php';</script>";
}

mysqli_close($conn);
