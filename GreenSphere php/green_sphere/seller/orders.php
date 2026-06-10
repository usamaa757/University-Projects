<?php
include 'header.php';
// Include database connection
include '../db_connection.php';

// Fetch seller data
$seller_id = $_SESSION['seller_id'];
$orders_query = "
SELECT o.*, p.plant_name, p.plant_type, u.user_name, u.email AS user_email, u.phone AS user_phone
FROM orders o
JOIN plants p ON o.plant_id = p.plant_id
JOIN users u ON o.user_id = u.user_id
WHERE p.seller_id = '$seller_id'
";

$orders_result = mysqli_query($conn, $orders_query);

?>

<style>
.order-details strong {
    color: #28a745;
}
</style>

<div class="container mt-5 round shadow border p-3">
    <h3 class="mb-4">Orders for Your Plants</h3>

    <?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php
    if (mysqli_num_rows($orders_result) > 0) {
        while ($order = mysqli_fetch_assoc($orders_result)) {
    ?>
    <div class="order-card border mt-3 p-3 round">
        <h4 class="text-blue">Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h4>
        <div class="order-details">
            <p><strong>User Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
            <p><strong>User Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
            <p><strong>User Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?></p>
            <p><strong>Plant Name:</strong> <?php echo htmlspecialchars($order['plant_name']); ?></p>
            <p><strong>Plant Type:</strong> <?php echo htmlspecialchars($order['plant_type']); ?></p>
            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
            <p><strong>Total Price:</strong> Rs <?php echo htmlspecialchars($order['total_price']); ?></p>
            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['ship_address']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
            <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
            <p><strong>Dispatched At:</strong> <?php echo htmlspecialchars($order['dispatched_at']); ?></p>
            <p><strong>Out for Delivery At:</strong> <?php echo htmlspecialchars($order['out_for_delivery_at']); ?></p>
            <p><strong>Delivered At:</strong> <?php echo htmlspecialchars($order['delivered_at']); ?></p>
            <p><strong>Estimated Delivery Time:</strong>
                <?php echo htmlspecialchars($order['estimated_delivery_time']); ?></p>
        </div>
    </div>
    <?php
        }
    } else {
        echo "<div class='alert alert-info'>No orders found.</div>";
    }
    ?>
</div>