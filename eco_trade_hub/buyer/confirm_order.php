<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    echo "Invalid order ID.";
    exit();
}

// Update the order status to 'shipped'
$sql = "UPDATE orders SET status = 'shipped' WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    echo "Order confirmed and marked as shipped successfully.";
} else {
    echo "Failed to confirm order.";
}

// Redirect back to the orders page after a few seconds
header("Refresh: 2; URL=seller_orders.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Order</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <div class="alert alert-info">
            You are being redirected back to the orders page...
        </div>
    </div>
</body>

</html>
