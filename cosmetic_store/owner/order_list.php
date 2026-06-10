<?php
include 'header.php';
include '../db.php';

// Fetch the data of purchased products with current status and customer info
$query = "SELECT 
    p.product_name, 
    p.brand, 
    p.category,
    p.image_path,
    oi.quantity, 
    oi.price, 
    o.order_id,
    o.order_date,
    o.status,
    o.phone_number,
    o.address,
    c.name AS customer_name,
    c.email AS customer_email

FROM orders o 
JOIN order_items oi ON o.order_id = oi.order_id 
JOIN products p ON oi.product_id = p.product_id 
JOIN customer c ON o.customer_id = c.customer_id
ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $query);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    mysqli_stmt_execute($stmt);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Purchased Products</h2>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="order-card">
                <img src="<?= '../owner/' . htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="order-img">

                <div class="order-details">
                    <h3><?= htmlspecialchars($row['product_name']) ?> (<?= htmlspecialchars($row['brand']) ?>)</h3>
                    <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
                    <p><strong>Quantity:</strong> <?= $row['quantity'] ?> | <strong>Price:</strong>
                        $<?= number_format($row['price'], 2) ?></p>
                    <p><strong>Order Date:</strong> <?= date('d M, Y', strtotime($row['order_date'])) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
                    <p><strong>Customer:</strong> <?= htmlspecialchars($row['customer_name']) ?>
                        (<?= htmlspecialchars($row['customer_email']) ?>)</p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone_number']) ?></p>

                </div>

                <div class="status-update">
                    <?php if ($row['status'] === 'delivered' || $row['status'] === 'cancelled'): ?>
                        <p class="final-status"><?= ucfirst($row['status']) ?></p>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="status" class="status-select">
                                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="shipped" <?= $row['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $row['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn">Update</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-records">No purchases found.</p>
    <?php endif; ?>
</div>