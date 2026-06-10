<?php
include 'header.php';
// Include database connection
include '../db_connection.php';

// Fetch seller data
$seller_id = $_SESSION['seller_id'];
$query = "SELECT * FROM sellers WHERE seller_id = '$seller_id'";
$result = mysqli_query($conn, $query);
$seller = mysqli_fetch_assoc($result);

?>

<div class="container mt-5 border round shadow p-3">
    <h2>Welcome, <?php echo htmlspecialchars($seller['seller_name']); ?>!</h2>
    <hr>

    <!-- Seller Profile -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Your Profile</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['seller_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($seller['contact_number']); ?></p>
        </div>
    </div>

    <!-- Order Management  -->
    <div class="card mb-4">
        <div class="card-body">
            <h3>Orders for Your Plants</h3>
            <table class="table">
                <?php

                if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Order ID</th>
                        <th>Plant Name</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch orders for the seller's plants
                    $orders_query = "SELECT o.*, p.plant_name AS plant_name 
                                         FROM orders o
                                         JOIN plants p ON o.plant_id = p.plant_id
                                         WHERE p.seller_id = '$seller_id'";
                    $orders_result = mysqli_query($conn, $orders_query);

                    if (mysqli_num_rows($orders_result) > 0) {
                        while ($order = mysqli_fetch_assoc($orders_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['plant_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['quantity']) . "</td>";
                            echo "<td>$" . htmlspecialchars($order['total_price']) . "</td>";
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