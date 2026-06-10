<?php
include '../db.php';
include 'header.php';

$order_id = $_GET['order_id'];

$order = $conn->query("SELECT o.*, c.model FROM orders o 
    JOIN cars c ON o.car_id = c.car_id 
    WHERE o.order_id = $order_id")->fetch_assoc();

if (!$order) {
    echo "<div class='container mt-5 alert alert-danger'>Order not found.</div>";
    exit;
}

$remaining = $order['total_amount'] - $order['amount_paid'];
$installment_plan = $order['installment_plan'];
$installment_amount = $installment_plan > 0 ? round($order['total_amount'] / $installment_plan, 2) : 0;
$installments_paid = $installment_amount > 0 ? floor($order['amount_paid'] / $installment_amount) : 0;
$installments_left = $installment_plan - $installments_paid;
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border rounded p-3">

                <h3 class="mb-0 text-center">Order Confirmation</h3>

                <div class="card-body p-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Order ID:</span>
                            <span><?= $order['order_id'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Car:</span>
                            <span><?= htmlspecialchars($order['model']) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Total Amount:</span>
                            <span>Rs. <?= number_format($order['total_amount'], 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Amount Paid:</span>
                            <span>Rs. <?= number_format($order['amount_paid'], 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Payment Status:</span>
                            <span class="text-capitalize"><?= $order['payment_status'] ?></span>
                        </li>
                    </ul>

                    <?php if ($order['payment_option'] === 'installment' && $remaining > 0): ?>
                    <hr class="my-4">
                    <h5 class="text-center text-secondary mb-3">Installment Summary</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Installment Plan:</span>
                            <span><?= $installment_plan ?> installments</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Each Installment:</span>
                            <span>Rs. <?= number_format($installment_amount, 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Installments Paid:</span>
                            <span><?= $installments_paid ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Installments Remaining:</span>
                            <span><?= $installments_left ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-semibold">Remaining Amount:</span>
                            <span>Rs. <?= number_format($remaining, 2) ?></span>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>