<?php
include 'header.php';

// Include database connection
include '../db_connection.php';
$user_id = $_SESSION['user_id'];

// Fetch the user's orders
$order_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if there are any orders
if ($order_result->num_rows > 0) {
    echo "<div class='container mt-5 round border shadow p-2'>";
    echo "<h3>Your Orders</h3>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='bg-primary text-white'><tr><th>Order ID</th><th>Plant Name</th><th>Quantity</th><th>Total Price</th><th>Payment Method</th><th>Order Date</th><th>Payment Status</th><th>Order Status</th></tr></thead><tbody>";

    // Loop through each order and display details
    while ($order = $order_result->fetch_assoc()) {
        // Fetch the plant details for each order
        $plant_query = "SELECT * FROM plants WHERE plant_id = ?";
        $plant_stmt = $conn->prepare($plant_query);
        $plant_stmt->bind_param('i', $order['plant_id']);
        $plant_stmt->execute();
        $plant_result = $plant_stmt->get_result();
        $plant = $plant_result->fetch_assoc();

        echo "<tr>";
        echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
        echo "<td>" . htmlspecialchars($plant['plant_name']) . "</td>";
        echo "<td>" . htmlspecialchars($order['quantity']) . "</td>";
        echo "<td>Rs " . htmlspecialchars($order['total_price']) . "</td>";
        echo "<td>" . htmlspecialchars($order['payment_method']) . "</td>";
        echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
        echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";
        echo "<td>" . htmlspecialchars($order['order_status']) . "</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
    echo "</div>";
} else {
    echo "<div class='container mt-5'><div class='alert alert-warning'>You have no orders yet.</div></div>";
}

// Close the connection
mysqli_close($conn);
