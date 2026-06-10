<?php
include 'header.php';

// Include database connection
include '../db_connection.php';

// Start PHP block for data fetching
$user_id = $_SESSION['user_id'];

// Fetch the user's orders along with category name
$order_query = "
    SELECT o.*, cat.category_name 
    FROM orders o
    JOIN cloths c ON o.cloth_id = c.cloth_id
    JOIN categories cat ON c.category_id = cat.category_id
    WHERE o.user_id = ? 
    ORDER BY o.order_date DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Initialize an empty array to store orders
$orders = [];
if ($order_result->num_rows > 0) {
    while ($row = $order_result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Close the connection
$stmt->close();
$conn->close();
?>

<!-- Start HTML rendering -->
<div class="container mt-5 round border shadow p-2">
    <h3>Your Orders</h3>
    <?php if (!empty($orders)): ?>
    <table class="table table-bordered table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>Order ID</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Payment Status</th>
                <th>Order Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['category_name']); ?></td>
                <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                <td>Rs <?php echo htmlspecialchars($order['total_price']); ?></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-warning">You have no orders yet.</div>
    <?php endif; ?>
</div>