<?php
include '../db.php';
include 'header.php';

// Handle delivery status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    if ($conn->query("UPDATE orders SET status = '$new_status' WHERE order_id = $order_id")) {
        echo "<script>alert('Order status updated.'); window.location.href = 'manage_orders.php';</script>";
    } else {
        echo "<script>alert('Failed to update status.'); window.location.href = 'manage_orders.php';</script>";
    }
    exit();
}

$orders = $conn->query("
    SELECT o.*, c.city_name, c.delivery_charge
    FROM orders o
    JOIN cities c ON o.city_id = c.city_id
    ORDER BY o.order_id DESC
");
?>

<div class="container my-5">
    <div class="card shadow border-0 rounded-4">
        <h3 class="text-center mb-4">Manage Orders</h3>
        <div class="card-body table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>City</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $order['order_id'] ?></td>
                        <td><?= htmlspecialchars($order['name']) ?></td>
                        <td><?= htmlspecialchars($order['city_name']) ?></td>
                        <td>Rs. <?= number_format($order['total_amount']) ?></td>

                        <td>
                            <span
                                class="badge 
                                <?= $order['status'] == 'cancelled' ? 'bg-danger' : ($order['status'] == 'shipped' ? 'bg-primary' : ($order['status'] == 'pending' ? 'bg-warning text-dark' : ($order['status'] == 'delivered' ? 'bg-success' : 'bg-secondary'))) ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>

                        </td>
                        <td>
                            <form method="post" class="d-flex justify-content-center gap-2">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>
                                        Pending</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>
                                        Shipped</option>
                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>
                                        Delivered</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>
                                        Cancalled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm">Update</button>
                            </form>
                        </td>


                    </tr>
                    <?php endwhile; ?>
                    <?php if ($orders->num_rows == 0): ?>
                    <tr>
                        <td colspan="7">No orders found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>