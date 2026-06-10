<?php
include("../db_connection.php");

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if ($request_id === 0) {
    echo "Invalid request ID.";
    exit();
}

$msg = '';
// Update the part request status to 'completed'
$sql = "UPDATE part_requests SET status = 'completed' WHERE request_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
    $msg = "Part request completed successfully.";
} else {
    $error = "Failed to complete part request.";
}

header("Location: part_request_list.php?msg=" . urlencode($msg) . "&error=" . urlencode($error));
exit();