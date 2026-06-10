<?php
include("../db_connection.php");
include("header.php");

session_start(); // Ensure session is started

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    echo "Invalid order ID.";
    exit();
}

$msg = '';
$error = '';

// Update the order status to 'shipped'
$sql = "UPDATE orders SET status = 'shipped' WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    $msg = "Order shipped successfully.";
} else {
    $error = "Failed to confirm order.";
}

// Close the statement
$stmt->close();

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
