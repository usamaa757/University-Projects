<?php
include 'header.php';
// Include database connection
include '../db_connection.php';

// Fetch admin data
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE admin_id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

?>

<div class="container mt-5 border round shadow p-3">
    <h2>Welcome, <?php echo htmlspecialchars($admin['admin_name']); ?>!</h2>
    <hr>

    <!-- admin Profile -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Your Profile</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['admin_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($admin['phone']); ?></p>
        </div>
    </div>

    <!-- Order Management  -->
    <div class="card mb-4">
        <div class="card-body">
            <h3>Orders for Your Cloths</h3>
            <table class="table">

                <thead class="bg-primary text-white">
                    <tr>
                        <th>Order ID</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Fetch orders for the admin's cloths
                    $orders_query = "SELECT o.*,  
       c.category_name 
FROM orders o
JOIN cloths p ON o.cloth_id = p.cloth_id
JOIN categories c ON p.category_id = c.category_id
LIMIT 3;
";

                    $orders_result = mysqli_query($conn, $orders_query);

                    if (mysqli_num_rows($orders_result) > 0) {
                        while ($order = mysqli_fetch_assoc($orders_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['category_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['quantity']) . "</td>";
                            echo "<td>Rs" . htmlspecialchars($order['total_price']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['order_status']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No orders yet.</td></tr>";
                    }

                    // Close the connection
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>

</html>