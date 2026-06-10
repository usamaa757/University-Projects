<?php

include 'header.php';
include '../db.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo "<p>Invalid order ID.</p>";
    exit;
}

// Fetch order info
$order_query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if ($order = mysqli_fetch_assoc($order_result)) {
    $customer_id = $order['customer_id'];
    $total_price = $order['total_price'];
    $order_date = $order['order_date'];

    // Fetch customer info (optional)
    $cust_query = mysqli_query($conn, "SELECT name FROM customer WHERE customer_id = $customer_id");
    $cust = mysqli_fetch_assoc($cust_query);

    // Fetch ordered items
    $items_query = "
        SELECT oi.*, p.product_name, p.brand, p.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = $order_id
    ";
    $items_result = mysqli_query($conn, $items_query);
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Order Confirmation</h2>
    </div>

    <p><strong>Order ID:</strong> <?= $order_id ?></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($cust['name']) ?></p>
    <p><strong>Order Date:</strong> <?= date("d M, Y", strtotime($order_date)) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>

    <h3>Items Ordered:</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['brand']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                <td><strong>$<?= number_format($total_price, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

<?php
} else {
    echo "<p>Order not found.</p>";
}
?>