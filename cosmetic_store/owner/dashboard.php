<?php
include 'header.php';
include '../db.php';

$name = $_SESSION['name'] ?? 'Admin';
// Count total products
$product_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
$product_count = mysqli_fetch_assoc($product_result)['total'] ?? 0;

// Count total orders
$order_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$order_count = mysqli_fetch_assoc($order_result)['total'] ?? 0;

// Count total users
$user_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM customer");
$user_count = mysqli_fetch_assoc($user_result)['total'] ?? 0;

?>
<style>

</style>
</head>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Admin Dashboard - Welcome, <?= htmlspecialchars($name) ?></h2>
    </div>

    <div class="admin-cards">
        <div class="card">
            <h3><i class="fas fa-box"></i> Total Products</h3>
            <p><?= $product_count ?> Products</p>
        </div>
        <div class="card">
            <h3><i class="fas fa-shopping-cart"></i> Orders</h3>
            <p><?= $order_count ?> Orders</p>
        </div>
        <div class="card">
            <h3><i class="fas fa-users"></i> Users</h3>
            <p><?= $user_count ?> Registered Users</p>
        </div>
    </div>


    <div class="actions">
        <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Product</a>
        <a href="order_list.php"><i class="fas fa-receipt"></i> View Orders</a>
        <a href="view_booking.php"><i class="fas fa-calendar-check"></i> View Bookings</a>
    </div>

</div>

</body>

</html>