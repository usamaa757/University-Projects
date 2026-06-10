<?php
include '../db.php';
include 'header.php';

$orders = $conn->query("SELECT o.order_id, c.model, o.total_amount, o.amount_paid, o.payment_status, o.installment_plan, o.payment_option
                        FROM orders o
                        JOIN cars c ON o.car_id = c.car_id
                      
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
                            <th>Car Model</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Payment Status</th>
                            <th>Total Installments</th>
                            <th>Remaining Installments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                        <?php
                                $installment_plan = $row['installment_plan'];
                                $installment_amount = $installment_plan > 0 ? $row['total_amount'] / $installment_plan : 0;
                                $installments_paid = $installment_amount > 0 ? floor($row['amount_paid'] / $installment_amount) : 0;
                                $installments_left = $installment_plan - $installments_paid;
                                ?>
                        <tr>
                            <td><?= $row['order_id'] ?></td>
                            <td><?= htmlspecialchars($row['model']) ?></td>
                            <td>Rs. <?= number_format($row['total_amount'], 2) ?></td>
                            <td>Rs. <?= number_format($row['amount_paid'], 2) ?></td>
                            <td class="text-capitalize"><?= $row['payment_status'] ?></td>
                            <td>
                                <?= $row['payment_option'] === 'installment' ? $installment_plan : '-' ?>
                            </td>
                            <td>
                                <?= $row['payment_option'] === 'installment' ? $installments_left : '-' ?>
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