<?php
include '../header/admin-header.php';
include '../connection.php';

$rate_per_duty = 500;
$current_month = date('Y-m'); // YYYY-MM

// Handle Mark as Paid
if (isset($_GET['pay'])) {
    $payment_id = intval($_GET['pay']);
    mysqli_query($con, "
        UPDATE payments
        SET payment_status='Paid', payment_date=NOW()
        WHERE payment_id='$payment_id'
    ");
    header("Location: payments.php");
    exit;
}

// 1️⃣ Fetch all proctors and their verified duties for current month
$proctors = mysqli_query($con, "
    SELECT u.id AS user_id, u.name, u.role,
           COUNT(a.assignment_id) AS total_duties,
           SUM(CASE WHEN at.status='Present' THEN 1 ELSE 0 END) AS verified_duties
    FROM user u
    LEFT JOIN assignments a ON a.user_id = u.id
    LEFT JOIN attendance at
        ON at.assignment_id = a.assignment_id
        AND at.status='Present'
        AND DATE_FORMAT(at.marked_at,'%Y-%m') = '$current_month'
    WHERE u.role IN ('Superintendent','Invigilator')
    GROUP BY u.id
");

// 2️⃣ Insert payment record if not exists for the month AND verified_duties > 0
while ($row = mysqli_fetch_assoc($proctors)) {
    $verified_duties = (int)$row['verified_duties'];
    if ($verified_duties > 0) {
        $total_amount = $verified_duties * $rate_per_duty;

        $payCheck = mysqli_query($con, "
            SELECT * FROM payments
            WHERE user_id='{$row['user_id']}' AND payment_month='$current_month'
        ");
        $payment = mysqli_fetch_assoc($payCheck);

        if (!$payment) {
            mysqli_query($con, "
                INSERT INTO payments
                (user_id, verified_duties, rate_per_duty, total_amount, payment_month, payment_status)
                VALUES
                ('{$row['user_id']}', '$verified_duties', '$rate_per_duty', '$total_amount', '$current_month', 'Pending')
            ");
        }
    }
}

// 3️⃣ Fetch all payments including past months
$payments = mysqli_query($con, "
    SELECT p.*, u.name, u.role 
    FROM payments p
    JOIN user u ON p.user_id = u.id
    WHERE p.verified_duties > 0
    ORDER BY p.payment_month DESC, u.name ASC
");

?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Proctors Payment Management</h3>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Proctor</th>
                        <th>Role</th>
                        <th>Month</th>
                        <th>Verified Duties</th>
                        <th>Rate per Duty</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($payments)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td><?= date('F, Y', strtotime($row['payment_month'] . '-01')) ?></td>
                        <td><?= $row['verified_duties'] ?></td>
                        <td><?= number_format($row['rate_per_duty'], 2) ?></td>
                        <td><?= number_format($row['total_amount'], 2) ?></td>
                        <td>
                            <?php if ($row['payment_status'] == 'Paid'): ?>
                            <span class="badge badge-success">Paid</span>
                            <?php else: ?>
                            <span class="badge badge-warning">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['payment_status'] != 'Paid'): ?>
                            <a href="payments.php?pay=<?= $row['payment_id'] ?>" class="btn btn-success btn-sm">Mark as
                                Paid</a>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>Paid</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>