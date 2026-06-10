<?php
include '../header/admin-header.php';
include '../connection.php';

// 1. Duty Allocation
$duties = mysqli_query($con, "
    SELECT u.name, u.role, COUNT(a.assignment_id) AS total_duties
    FROM user u
    LEFT JOIN assignments a ON u.id = a.user_id
    WHERE u.role IN ('Superintendent','Invigilator')
    GROUP BY u.id
");

// 2. Attendance
$attendance = mysqli_query($con, "
    SELECT u.name,
           COUNT(a.assignment_id) AS assigned,
           SUM(CASE WHEN at.status='Present' THEN 1 ELSE 0 END) AS present,
           SUM(CASE WHEN at.status='Absent' THEN 1 ELSE 0 END) AS absent,
           ROUND(SUM(CASE WHEN at.status='Present' THEN 1 ELSE 0 END) / COUNT(a.assignment_id) * 100,2) AS attendance_percent
    FROM user u
    LEFT JOIN assignments a ON u.id = a.user_id
    LEFT JOIN attendance at ON at.assignment_id = a.assignment_id
    WHERE u.role IN ('Superintendent','Invigilator')
    GROUP BY u.id
");

// 3. Payment Summary
$payments = mysqli_query($con, "
    SELECT u.name,
           SUM(p.total_amount) AS total_payment,
           SUM(CASE WHEN p.payment_status='Paid' THEN p.total_amount ELSE 0 END) AS paid,
           SUM(CASE WHEN p.payment_status='Pending' THEN p.total_amount ELSE 0 END) AS pending
    FROM payments p
    JOIN user u ON p.user_id = u.id
    GROUP BY u.id
");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Admin Dashboard - Reporting & Analytics</h3>

    <!-- Duty Allocation -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5>Duty Allocation</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Proctor</th>
                        <th>Role</th>
                        <th>Total Duties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($duties)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['role'] ?></td>
                        <td><?= $row['total_duties'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attendance -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5>Attendance</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Proctor</th>
                        <th>Assigned Duties</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($attendance)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['assigned'] ?></td>
                        <td><?= $row['present'] ?></td>
                        <td><?= $row['absent'] ?></td>
                        <td><?= $row['attendance_percent'] ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payments -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <h5>Payment Summary</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Proctor</th>
                        <th>Total Payment</th>
                        <th>Paid</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($payments)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= number_format($row['total_payment'], 2) ?></td>
                        <td><?= number_format($row['paid'], 2) ?></td>
                        <td><?= number_format($row['pending'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>

</html>