<?php
include '../header/superintendent-header.php';
include '../connection.php';
$user_id = $_SESSION['id'];
// 3️⃣ Fetch all payments including past months
$payments = mysqli_query($con, "
    SELECT p.*, u.name, u.role 
    FROM payments p
    JOIN user u ON p.user_id = u.id
    WHERE u.id=$user_id
    ORDER BY p.payment_month DESC
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
                        <th>Month</th>
                        <th>Verified Duties</th>
                        <th>Rate per Duty</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($payments)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
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

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>