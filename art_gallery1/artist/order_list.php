<?php
include '../db.php';
include 'header.php';

$orders = $conn->query("SELECT o.order_id, u.user_id, a.art_name, a.art_id, a.price, o.status, o.order_date, o.payment_method, o.shipping_status, o.status
                        FROM orders o
                        JOIN arts a ON o.art_id = a.art_id
                        JOIN users u ON o.customer_id = u.user_id
                        WHERE o.customer_id = {$_SESSION['user_id']}
                        ORDER BY o.order_id DESC");
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
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>

                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <td><?= htmlspecialchars($row['art_name']) ?></td>
                            <td>Rs. <?= number_format($row['payment_method'], 2) ?></td>
                            <td>Rs. <?= number_format($row['amount_paid'], 2) ?></td>
                            <td class="text-capitalize"><?= $row['shipping_status'] ?></td>
                            <td>
                                <span
                                    class="badge 
                                <?= $row['status'] == 'cancelled' ? 'bg-danger' : ($row['status'] == 'paid' ? 'bg-primary' : ($row['status'] == 'pending' ? 'bg-warning text-dark' : ($row['status'] == 'delivered' ? 'bg-success' : 'bg-secondary'))) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>

                            </td>
                            <td>
                                <?php if ($row['status'] == 'cancelled') { ?>
                                <span
                                    class="badge 
                                <?= $row['status'] == 'cancelled' ? 'bg-danger' : ($row['status'] == 'shipped' ? 'bg-primary' : ($row['status'] == 'pending' ? 'bg-warning text-dark' : ($row['status'] == 'delivered' ? 'bg-success' : 'bg-secondary'))) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                                <?php } else {
                                            $timezone = new DateTimeZone('Asia/Kolkata');

                                            $order_time = new DateTime($row['order_date'], $timezone);
                                            $cancel_deadline = clone $order_time;
                                            $cancel_deadline->modify('+24 hours');

                                            $now = new DateTime('now', $timezone);

                                            if ($now < $cancel_deadline && $row['status'] !== 'cancelled') {
                                                $interval = $now->diff($cancel_deadline);
                                                $total_hours = ($interval->days * 24) + $interval->h;
                                                $remaining_text = "$total_hours hour(s) {$interval->i} minute(s)";

                                                echo "<a href='cancel_order.php?order_id={$row['order_id']}&action=cancel' class='btn btn-sm' onclick=\"return confirm('Are you sure you want to cancel this order?');\">Cancel</a>";
                                                echo "<div class='text-muted small mt-1'>You have <strong>$remaining_text</strong> left to cancel</div>";
                                            } elseif ($row['status'] !== 'cancelled') {
                                                echo "<div class='text-muted small'>Cancellation period expired</div>";
                                            }
                                        }
                                        ?>


                            </td>


                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8">No orders found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>