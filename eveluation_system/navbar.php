<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Research System - Registration</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <!-- NAVIGATION BAR -->
    <nav class="navbar">
        <div class="logo">Research Submission System</div>
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="admin_dashboard.php">Home</a></li>
            <li><a href="admin_assignment_list.php">Assignment List</a></li>
            <li><a href="report.php">Report</a></li>
            <li><a href="manage_user.php">Manage User</a></li>
            <?php elseif ($_SESSION['role'] == 'faculty'): ?>
            <li><a href="faculty_dashboard.php">Home</a></li>
            <li><a href="evaluation_history.php">All Evaluation</a></li>
            <li><a href="faculty_report.php">Report</a></li>
            <?php elseif ($_SESSION['role'] == 'student'): ?>
            <li><a href="student_dashboard.php">Home</a></li>
            <li><a href="assignment_list.php">Assignment List</a></li>
            <li><a href="my_submissions.php">My Submissions</a></li>
            <?php endif; ?>
            <li><a href="research_paper.php">Research Paper</a></li>
            <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="research_paper.php">Research Paper</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>