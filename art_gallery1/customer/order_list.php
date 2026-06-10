<?php
include '../db.php';
include 'header.php';

$orders = $conn->query("SELECT o.order_id, u.user_id, a.art_name, o.art_id, a.price, o.status, o.order_date, o.payment_method, o.shipping_status, o.status
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
                            <th>Price</th>
                            <th>Payment Method</th>
                            <th>Shipping Status</th>
                            <th>Order Status</th>
                            <th>Reviews</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>

                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <td><?= htmlspecialchars($row['art_name']) ?></td>
                            <td><?= $row['price'] ?></td>
                            <td><?= $row['payment_method'] ?></td>
                            <td>

                                <span
                                    class="badge 
    <?= $row['shipping_status'] == 'Cancelled' ? 'bg-danger' : ($row['shipping_status'] == 'Delivered' ? 'bg-success' : ($row['shipping_status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-secondary')) ?>">
                                    <?= ucfirst($row['shipping_status']) ?>
                                </span>

                            </td>
                            <td>
                                <span
                                    class="badge 
    <?= $row['status'] == 'Cancelled' ? 'bg-danger' : ($row['status'] == 'Paid' ? 'bg-success' : ($row['status'] == 'Pending' ? 'bg-warning text-dark' : 'bg-secondary')) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>


                            </td>
                            <td>
                                <a href="add_reviews.php?art_id= <?php echo $row['art_id']; ?>"
                                    class="btn btn-sm">Review</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan=" 8">No orders found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>