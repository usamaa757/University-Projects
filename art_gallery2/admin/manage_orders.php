<?php
include 'header.php';
include '../db.php';


// Handle Status Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status  = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated!'); window.location='manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status.');</script>";
    }
    $stmt->close();
}

// Handle Shipping Status Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_shipping_status'])) {
    $order_id = $_POST['order_id'];
    $new_shipping_status = $_POST['shipping_status'];

    $update_sql = "UPDATE orders SET shipping_status  = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_shipping_status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Shipping status updated!'); window.location='manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating shipping status.');</script>";
    }
    $stmt->close();
}

// Handle Order Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    $delete_sql = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order $order_id deleted successfully!'); window.location='manage_orders.php';</script>";
    } else {
        echo "<script>alert('Error deleting order.');</script>";
    }
    $stmt->close();
}

// Fetch Orders
$sql = "SELECT u.username, orders.order_id, orders.customer_id, orders.customer_email, orders.address, orders.payment_method, orders.status, orders.order_date, orders.shipping_status, 
        art_items.art_name, art_items.image, art_items.price
        FROM orders
        LEFT JOIN users u ON orders.customer_id = u.user_id
        INNER JOIN art_items ON orders.art_id = art_items.art_id
        ORDER BY orders.order_date DESC";
$result = $conn->query($sql);
?>


<!-- Orders List -->
<div class="container-fluid mt-5 border rounded shadow">
    <h3 class="text-center">Manage Orders</h3>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Artwork</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Address</th>
                <th>Payment Method</th>
                <th>Order Status</th>
                <th>Shipping Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td>
                    <img src="<?php echo $base_url . '/seller/' . htmlspecialchars($row['image']); ?>" width="50">
                    <br>
                    <?php echo htmlspecialchars($row['art_name']); ?>
                    <br>
                    <strong>$<?php echo number_format($row['price'], 2); ?></strong>
                </td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                <td>
                    <?php if ($row['status'] == "Paid") {
                            echo "<span class='badge bg-success'>Paid</span>";
                        } elseif ($row['status'] == "Cancelled") {
                            echo "<span class='badge bg-danger'>Cancelled</span>";
                        } else { ?>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">

                        <select name="status" class="form-select">
                            <option value="Pending" <?php if ($row['status'] == "Pending") echo "selected"; ?>>
                                Pending</option>
                            <option value="Paid" <?php if ($row['status'] == "Paid") echo "selected"; ?>>
                                Paid
                            </option>
                            <option value="Cancelled" <?php if ($row['status'] == "Cancelled") echo "selected"; ?>>
                                Cancelled
                            </option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary btn-sm mt-1">Update</button>
                    </form>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($row['shipping_status'] == "Delivered") {
                            echo "<span class='badge bg-success'>Delivered</span>";
                        } else { ?>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">

                        <select name="shipping_status" class="form-select">
                            <option value="Pending" <?php if ($row['shipping_status'] == "Pending") echo "selected"; ?>>
                                Pending
                            </option>
                            <option value="Shipped" <?php if ($row['shipping_status'] == "Shipped") echo "selected"; ?>>

                                Shipped
                            </option>
                            <option value="Delivered"
                                <?php if ($row['shipping_status'] == "Delivered") echo "selected"; ?>>

                                Delivered</option>
                        </select>
                        <button type="submit" name="update_shipping_status"
                            class="btn btn-sm btn-primary mt-1">Update</button>
                    </form>
                    <?php } ?>
                </td>
                <td><?php echo date("d M Y, H:i", strtotime($row['order_date'])); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <button type="submit" name="delete_order" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            <?php }

            if ($result->num_rows == 0) {
                echo "<tr><td colspan='10' class = 'text-center'> No order found</td></td>";
            } ?>
        </tbody>
    </table>
</div>


</body>

</html>

<?php
// Close Connection
$conn->close();
?>