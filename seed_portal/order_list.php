<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// Fetch quick stats
$user_id = $_SESSION['user_id'];
?>

<!-- 🧾 Recent Orders -->
<div class="recent-orders">
    <h3>🧾 Recent Orders</h3>
    <?php

    $orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
    if ($orders->num_rows > 0) {
        echo "<table>
                    <tr>
                        <th>Order ID</th>
                        <th>User Email</th>
                        <th>User Address</th>
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
                    <td>{$row['user_email']}</td>
                    <td>{$row['user_address']}</td>
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