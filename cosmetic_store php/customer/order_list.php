<?php
include 'header.php';
include '../db.php';

// Replace with actual logged-in customer ID
$customer_id = $_SESSION['customer_id'] ?? 1;

$query = "SELECT 
    p.product_name, 
    p.brand, 
    p.category,
    p.image_path,
    oi.quantity, 
    oi.price, 
    o.order_date,
    o.status
FROM orders o 
JOIN order_items oi ON o.order_id = oi.order_id 
JOIN products p ON oi.product_id = p.product_id 
WHERE o.customer_id = $customer_id
ORDER BY o.order_date DESC
";

$result = mysqli_query($conn, $query);
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Purchased Products</h2>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Purchase Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>

                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['brand']) ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="product-img">
                        </td>
                        <td><?= date('d M, Y', strtotime($row['order_date'])) ?></td>
                        <td><?= $row['status'] ?></td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No purchases found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .product-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        transition: transform 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .product-img:hover {
        transform: scale(2);
        z-index: 9999;
        position: relative;
    }
</style>