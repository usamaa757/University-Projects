<?php
include 'header.php';
// Include database connection
include '../db_connection.php';

// Fetch admin data
$admin_id = $_SESSION['admin_id'];

// Update order status and timestamps if POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $dispatched_at = $_POST['dispatched_at'];
    $out_for_delivery_at = $_POST['out_for_delivery_at'];
    $delivered_at = $_POST['delivered_at'];
    $estimated_delivery_time = $_POST['estimated_delivery_time'];
    $query = "SELECT user_id FROM orders WHERE order_id = '$order_id'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];
    $update_order_query  = "UPDATE orders SET 
                     order_status = '$new_status', 
                     dispatched_at = '$dispatched_at', 
                     out_for_delivery_at = '$out_for_delivery_at', 
                     delivered_at = '$delivered_at',
                     estimated_delivery_time = '$estimated_delivery_time'
                     WHERE order_id = '$order_id'";
    if ($delivered_at) {
        $update_order_query  = "UPDATE orders SET 
    order_status = '$new_status', 
    payment_status = 'Paid', 
    dispatched_at = '$dispatched_at', 
    out_for_delivery_at = '$out_for_delivery_at', 
    delivered_at = '$delivered_at',
    estimated_delivery_time = '$estimated_delivery_time'
    WHERE order_id = '$order_id'";
    }
    if (mysqli_query($conn, $update_order_query)) {
        // Send notification to the user
        $message = "Your order has been $new_status. Estimated delivery time: $estimated_delivery_time.";

        $notification_query = "INSERT INTO notifications (user_id, message, type) VALUES ('$user_id', '$message', 'order_update')";

        if (mysqli_query($conn, $notification_query)) {
            // Success message
            $success_message = "Order ID ($order_id) status updated successfully!";
        } else {
            // Error handling
            $error_message = "Error updating order status: " . mysqli_error($conn);
        }
        if ($new_status === 'Canceled') {
            // SQL query to delete the order
            $delete_order_query = "DELETE FROM orders WHERE order_id = ?";

            // Prepare and execute the query
            $delete_stmt = $conn->prepare($delete_order_query);
            $delete_stmt->bind_param('i', $order_id);  // Assuming $order_id is already set
            $delete_stmt->execute();

            // Optionally, check for errors
            if ($delete_stmt->affected_rows > 0) {
                $success_message = "Order has been successfully canceled and removed.";
            } else {
                $error_message = "Error canceling the order. Please try again.";
            }
        }
    }
}
?>
<style>
    strong {
        color: green;
    }
</style>
<!-- Order Management -->
<div class="container mt-5 round border shadow">
    <div class="card-body">
        <h3>Orders for Your Plants</h3>
        <table class="table">
            <?php
            if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif;

            ?>
            <thead class="bg-primary text-white">
                <tr>
                    <th>Order </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orders_query = "
                SELECT o.*, p.plant_name, p.plant_type, u.user_name, u.email AS user_email, u.phone AS user_phone
                FROM orders o
                JOIN plants p ON o.plant_id = p.plant_id
                JOIN users u ON o.user_id = u.user_id
                WHERE p.admin_id = '$admin_id'
            ";

                $orders_result = mysqli_query($conn, $orders_query);

                if (mysqli_num_rows($orders_result) > 0) {
                    while ($order = mysqli_fetch_assoc($orders_result)) {
                        echo "<tr>";

                        // Check if 'delivered_at' is either '0000-00-00 00:00:00' or empty
                        if ($order['delivered_at'] !== '0000-00-00 00:00:00' && $order['payment_status'] == 'Paid') {
                            echo "<td colspan ='2' class='text-left'>";
                ?>
                            <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?><br>
                            <strong>Plant Name:</strong> <?php echo htmlspecialchars($order['plant_name']); ?><br>
                            <strong>Plant Type:</strong> <?php echo htmlspecialchars($order['plant_type']); ?><br>
                            <strong>Delivered At:</strong> <?php echo htmlspecialchars($order['delivered_at']); ?><br>
                            <strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?><br>

                        <?php
                        } else {

                            echo "<td class='text-left'>";
                        ?>
                            <p class="card-text">
                                <strong>User Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?><br><br>
                                <strong>User Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?><br><br>
                                <strong>User Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?><br><br>
                                <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?><br><br>
                                <strong>Plant Name:</strong> <?php echo htmlspecialchars($order['plant_name']); ?><br><br>
                                <strong>Plant Type:</strong> <?php echo htmlspecialchars($order['plant_type']); ?><br><br>
                                <strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?><br><br>
                                <strong>Total Price:</strong> Rs <?php echo htmlspecialchars($order['total_price']); ?><br><br>
                                <strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?><br><br>
                                <strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['ship_address']); ?><br><br>
                                <strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?><br><br>
                                <strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?><br><br>
                                <strong>Dispatched At:</strong> <?php echo htmlspecialchars($order['dispatched_at']); ?><br><br>
                                <strong>Out for Delivery At:</strong>
                                <?php echo htmlspecialchars($order['out_for_delivery_at']); ?><br><br>
                                <strong>Delivered At:</strong> <?php echo htmlspecialchars($order['delivered_at']); ?><br><br>
                                <strong>Estimated Delivery Time:</strong>
                                <?php echo htmlspecialchars($order['estimated_delivery_time']); ?>
                            </p>
                            <?php
                            echo "</td>";
                            echo "<td>";
                            ?>
                            <form action="orders_list.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                <div class="form-group">
                                    <select name="status" class="form-control form-control-sm" required>
                                        <option value="Pending"
                                            <?php echo ($order['order_status'] == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                                        <option value="Shipped"
                                            <?php echo ($order['order_status'] == 'Shipped' ? 'selected' : ''); ?>>Shipped</option>
                                        <option value="Completed"
                                            <?php echo ($order['order_status'] == 'Completed' ? 'selected' : ''); ?>>Completed
                                        </option>
                                        <option value="Canceled"
                                            <?php echo ($order['order_status'] == 'Canceled' ? 'selected' : ''); ?>>Canceled
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dispatched_at">Dispatched At:</label>
                                    <input type="datetime-local" name="dispatched_at" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($order['dispatched_at']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="out_for_delivery_at">Out For Delivery At:</label>
                                    <input type="datetime-local" name="out_for_delivery_at" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($order['out_for_delivery_at']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="delivered_at">Delivered At:</label>
                                    <input type="datetime-local" name="delivered_at" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($order['delivered_at']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="estimated_delivery_time">Estimated Delivery Time:</label>
                                    <input type="text" name="estimated_delivery_time" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($order['estimated_delivery_time']); ?>"
                                        placeholder="Day, Hour">
                                </div>
                                <button type="submit" class="btn bg-primary text-white btn-sm">Update</button>
                            </form>
                <?php
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='2' class='text-center'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>