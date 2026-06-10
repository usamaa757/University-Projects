<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Fetch quick stats
$user_id = $_SESSION['user_id'];

// Total seeds available
$seed_count = $conn->query("SELECT COUNT(*) AS total FROM seeds WHERE status='Available'")->fetch_assoc()['total'];

// Total orders placed by this user
$order_count = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id='$user_id'")->fetch_assoc()['total'];

// Distinct categories
$category_count = $conn->query("SELECT COUNT(DISTINCT category) AS total FROM seeds")->fetch_assoc()['total'];
?>

<style>

</style>
</head>

<body>

    <div class="dashboard-container">

        <h2>👨‍🌾 Welcome, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</h2>

        <!-- 📊 Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Seeds Available</h3>
                <p><?php echo $seed_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Orders Placed</h3>
                <p><?php echo $order_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Seed Categories</h3>
                <p><?php echo $category_count; ?></p>
            </div>
        </div>

        <!-- ⚙️ Quick Actions -->
        <div class="dashboard-actions">
            <a href="seeds.php" class="btn">🌱 Browse Seeds</a>
            <a href="my_orders.php" class="btn">📦 View My Orders</a>
            <a href="profile.php" class="btn">👤 Profile</a>
            <a href="logout.php" class="btn" style="background-color:#c62828;">🚪 Logout</a>
        </div>

        <!-- 🧾 Recent Orders -->
        <div class="recent-orders">
            <h3>🧾 Recent Orders</h3>
            <?php
            $orders = $conn->query("SELECT * FROM orders WHERE user_id='$user_id' ORDER BY order_date DESC LIMIT 5");
            if ($orders->num_rows > 0) {
                echo "<table>
                    <tr>
                        <th>Order ID</th>
                        <th>Seed Name</th>
                        <th>Quantity</th>
                        <th>Total (PKR)</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>";
                while ($row = $orders->fetch_assoc()) {
                    echo "
                <tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['seed_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['total_price']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['order_date']}</td>
                </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>You have no recent orders.</p>";
            }
            ?>
        </div>

    </div>

</body>

</html>