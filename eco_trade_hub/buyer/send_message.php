<?php
include("../db_connection.php");

$buyer_id = isset($_POST['buyer_id']) ? intval($_POST['buyer_id']) : 0;
$seller_id = isset($_POST['seller_id']) ? intval($_POST['seller_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$sender_type = isset($_POST['sender_type']) ? $_POST['sender_type'] : '';

if ($buyer_id === 0 || $seller_id === 0 || $message === '' || $sender_type === '') {
    echo "Invalid data.";
    exit();
}

$sql = "INSERT INTO messages (buyer_id, seller_id, message, sender_type) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $buyer_id, $seller_id, $message, $sender_type);

if ($stmt->execute()) {
    echo "Message sent successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
