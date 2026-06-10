<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VU Proctor-Diary</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <nav>

        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php">My Profile</a>
        <?php if ($_SESSION['role'] === 'superintendent' || $_SESSION['role'] === 'invigilator'): ?>
        <a href="my_duties.php">My Duty</a>
        <a href="attendance.php">My Attendance</a>
        <a href="my_payment.php">My Payment</a>
        <a href="apply_leave.php">Apply Leave</a>
        <a href="payments.php">Payment Request</a>

        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'superintendent'): ?>

        <a href="upload_reports.php">Reports</a>

        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="approvel.php">User Approvals</a>
        <a href="assign_duties.php">Assign Duty</a>
        <a href="add_exam.php">Add Exam</a>
        <a href="attendance_verify.php">Attendance</a>
        <a href="manage_leaves.php">Manage Leaves</a>
        <a href="view_reports.php">Reports</a>
        <a href="process_payment.php">Payment</a>

        <?php endif; ?>
        <span class="nav-right"><a href="logout.php">Logout</a></span>
        <?php else: ?>

        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>



        <?php endif; ?>
    </nav>
</body>

</html>