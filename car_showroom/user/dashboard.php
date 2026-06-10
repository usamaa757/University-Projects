<?php

include '../db.php';
include 'header.php';

// Get user details
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

// Get orders with installment plans
$installments = $conn->query("SELECT order_id, order_date, total_amount, amount_paid, installment_plan 
                              FROM orders 
                              WHERE user_id = $user_id AND payment_option = 'installment'");
?>

<div class="container mt-5">
    <div class="card shadow-lg rounded-4 border p-4">
        <h2 class="text-center">Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h2>

        <div class="text-center">
            <?php if (!empty($user['profile_pic'])): ?>
            <img src="<?= '../' . $user['profile_pic'] ?>" alt="Profile Picture" class="rounded-circle mb-3"
                style="width: 120px; height: 120px;">
            <?php endif; ?>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>

        <?php
        if ($installments->num_rows > 0): ?>

        <hr>
        <h4 class="mt-4 text-center">🔔 Installment Reminders</h4>
        <ul class="list-group list-group-flush">
            <?php while ($row = $installments->fetch_assoc()):

                    $installment_amount = $row['installment_plan'] > 0 ? $row['total_amount'] / $row['installment_plan'] : 0;
                    $installments_paid = $installment_amount > 0 ? floor($row['amount_paid'] / $installment_amount) : 0;

                    $order_date = new DateTime($row['order_date']);
                    $next_due_date = clone $order_date;
                    $next_due_date->modify("+$installments_paid month");
                    $today = new DateTime();
                    $interval = $today->diff($next_due_date);
                    $days_left = (int)$interval->format('%r%a'); // days until next due date


                    if ($days_left >= 0 && $days_left <= 3): // due within 3 days

                ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Order #<?= $row['order_id'] ?> — Next installment due:
                <strong><?= $next_due_date->format('d M, Y') ?></strong>
                <span class="badge bg-warning text-dark"><?= $days_left ?> day(s) left</span>
            </li>
            <?php endif; ?>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <div class="alert alert-info mt-4">No installment plans found.</div>
        <?php endif; ?>
    </div>
</div>