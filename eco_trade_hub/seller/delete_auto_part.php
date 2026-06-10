<?php
include("../db_connection.php");

session_start();

$part_id = isset($_GET['part_id']) ? intval($_GET['part_id']) : 0;

if ($part_id === 0) {
    echo "Invalid part ID.";
    exit();
}

$sql = "UPDATE auto_parts SET status = 'hide' WHERE part_id = ? AND seller_id = ? AND status = 'show'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $part_id, $_SESSION['seller_id']);

if ($stmt->execute()) {
    header("Location: parts_list.php?error=" . urlencode("Part deleted successfully."));
} else {
    header("Location: parts_list.php?error=" . urlencode("Error deleting part: " . $stmt->error));
}
$stmt->close();
$conn->close();
?>
