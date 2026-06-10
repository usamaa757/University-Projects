<?php
include '../db.php';
include 'header.php';

$orders = $conn->query("SELECT o.order_id, u.user_id, a.art_name, u.name, o.art_id, a.price, o.status, o.order_date, o.payment_method, o.shipping_status
                        FROM orders o
                        JOIN arts a ON o.art_id = a.art_id
                        JOIN users u ON a.artist_id = u.user_id
                        ORDER BY o.order_id DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $shipping_status = $_POST['shipping_status'];

    // Fetch payment method for this order
    $stmt = $conn->prepare("SELECT payment_method FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $payment_method = $order['payment_method'];
    $stmt->close();

    // Determine the order status based on shipping_status and payment_method
    $status = null;
    if ($shipping_status === "Cancelled") {
        $status = "Cancelled";
    } elseif ($shipping_status === "Delivered") {
        $status = "Paid";
    }

    // Update the order record
    if ($status !== null) {
        $stmt = $conn->prepare("UPDATE orders SET shipping_status = ?, status = ? WHERE order_id = ?");
        $stmt->bind_param("ssi", $shipping_status, $status, $order_id);
    } else {
        $stmt = $conn->prepare("UPDATE orders SET shipping_status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $shipping_status, $order_id);
    }

    // Execute and check result
    if ($stmt->execute()) {
        echo "<script>
                alert('Shipping status updated successfully!');
                window.location.href = 'manage_orders.php';  
              </script>";
    } else {
        echo "<script>
                alert('Error updating shipping status.');
                window.location.href = 'manage_orders.php';
              </script>";
    }

    $stmt->close();
    $conn->close();
}


?>

<div class="container my-5">
    <div class="card shadow-lg border-0 rounded-4">
        <h3 class="mb-0 text-center py-3">All Orders</h3>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Art Name</th>
                            <th>Artist Name</th>
                            <th>Price</th>
                            <th>Payment Method</th>
                            <th>Order Status</th>
                            <th>Update Shipping Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders->num_rows > 0): ?>
                            <?php while ($row = $orders->fetch_assoc()): ?>

                                <tr>
                                    <td><?= $row['order_id'] ?></td>
                                    <td><?= htmlspecialchars($row['art_name']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= $row['price'] ?></td>
                                    <td><?= $row['payment_method'] ?></td>

                                    <td>
                                        <span
                                            class="badge 
                                    <?= $row['status'] == 'Cancelled' ? 'bg-danger' : ($row['status'] == 'Paid' ? 'bg-success' : ($row['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-secondary')) ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['shipping_status'] != 'Delivered') { ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                                <select name="shipping_status" class="form-select form-select-sm">
                                                    <option value="Pending"
                                                        <?= $row['shipping_status'] == 'Pending' ? 'selected' : '' ?>>Pending
                                                    </option>
                                                    <option value="Delivered"
                                                        <?= $row['shipping_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered
                                                    </option>
                                                    <option value="Cancelled"
                                                        <?= $row['shipping_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled
                                                    </option>
                                                </select>
                                                <button type="submit" class="btn btn-sm mt-2">Update</button>
                                            </form>
                                        <?php } else { ?>
                                            <span class="badge 
                                        <?=
                                            $row['shipping_status'] == 'Delivered' ? 'bg-success' : ($row['shipping_status'] == 'Cancelled' ? 'bg-danger' : 'bg-secondary')
                                        ?>">
                                                <?= ucfirst($row['shipping_status']) ?>
                                            </span>

                                        <?php } ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>