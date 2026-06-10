<?php
include 'header.php';
include '../db.php';

$name = $_SESSION['name'];
$customer_id = $_SESSION['customer_id'];

// Fetch order statistics
$order_stats_query = "
    SELECT status, COUNT(*) AS total
    FROM orders
    WHERE customer_id = ?
    GROUP BY status
";
$stmt = mysqli_prepare($conn, $order_stats_query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$order_counts = ['pending' => 0, 'cancelled' => 0, 'delivered' => 0];
while ($row = mysqli_fetch_assoc($result)) {
    $status = strtolower($row['status']);
    if (isset($order_counts[$status])) {
        $order_counts[$status] = $row['total'];
    }
}

// Fetch 3 random products
$random_products_query = "SELECT product_name, brand, price, image_path FROM products ORDER BY RAND() LIMIT 3";
$stmt = mysqli_prepare($conn, $random_products_query);
mysqli_stmt_execute($stmt);
$random_products_result = mysqli_stmt_get_result($stmt);
$random_products = mysqli_fetch_all($random_products_result, MYSQLI_ASSOC);
?>

<!-- Link CSS -->
<link rel="stylesheet" href="dashboard.css">

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Welcome, <?= htmlspecialchars($name) ?>!</h1>
    </div>

    <div class="dashboard-cards">
        <div class="dashboard-card">
            <h3>Pending Orders</h3>
            <p><?= $order_counts['pending'] ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Cancelled Orders</h3>
            <p><?= $order_counts['cancelled'] ?></p>
        </div>
        <div class="dashboard-card">
            <h3>Delivered Orders</h3>
            <p><?= $order_counts['delivered'] ?></p>
        </div>
    </div>

    <div class="random-products">
        <h3>Recommended Products</h3>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Image</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($random_products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= htmlspecialchars($product['brand']) ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image"
                                class="product-img">
                        </td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>

</html>