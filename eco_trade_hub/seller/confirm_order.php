<?php
include("../db_connection.php");

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    echo "Invalid order ID.";
    exit();
}

$msg = '';
$error = '';
$status = 'shipped';
$new_status = 'completed';

// Update the order status to 'completed' if it is currently 'shipped'
$sql = "UPDATE orders SET status = ? WHERE order_id = ? AND status = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sis", $new_status, $order_id, $status);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $msg = "Order confirmed successfully.";
} else {
    $error = "Failed to confirm order. Make sure the order is currently shipped.";
}

$redirectUrl = 'Location: view_orders.php?';
if (!empty($msg)) {
    $redirectUrl .= 'msg=' . urlencode($msg);
}
if (!empty($error)) {
    $redirectUrl .= '&error=' . urlencode($error);
}

// Redirect back to the orders page
header($redirectUrl);
exit();
?>
