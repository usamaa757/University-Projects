<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!-- 🧾 Recent Orders -->
<div class="recent-orders">
    <h3>🧾 Recent Orders</h3>

    <?php
    // Check user role safely
    if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
        // ✅ For normal user: show their own orders
        $user_id = $_SESSION['user_id'];
        $orders = $conn->query("
            SELECT o.*, s.seed_name 
            FROM orders o 
            LEFT JOIN seeds s ON o.seed_id = s.seed_id 
            WHERE o.user_id = '$user_id' 
            ORDER BY o.order_date DESC
        ");
    } elseif (isset($_SESSION['agent_id'])) {
        // ✅ For agent: show orders for seeds uploaded by them
        $agent_id = $_SESSION['agent_id'];
        $orders = $conn->query("
            SELECT o.*, s.seed_name 
            FROM orders o 
            JOIN seeds s ON o.seed_id = s.seed_id 
            WHERE s.agent_id = '$agent_id' 
            ORDER BY o.order_date DESC
        ");
    }

    // ✅ Display orders table
    if (isset($orders) && $orders->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Order ID</th>
                    <th>Seed Name</th>
                    <th>Quantity</th>
                    <th>Total (PKR)</th>
                    <th>Status</th>
                    <th>Date</th>";
        echo "</tr>";

        while ($row = $orders->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>" . htmlspecialchars($row['seed_name'] ?? 'N/A') . "</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['total_price']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['order_date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recent orders found.</p>";
    }
    ?>
</div>