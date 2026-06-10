<?php
session_start();

$admin_id = isset($_SESSION['admin_id']);
$student_id = isset($_SESSION['student_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Seating System</title>
    <link rel="stylesheet" href="styles.css">
    <style>


    </style>
</head>

<body>

    <?php if ($admin_id) {

    ?>
    <h1>Exam Seating Arrangement System</h1>
    <div class="menu">
        <a href="add_data.php" class="nav-link" id="nav-link">Add Data</a>
        <a href="assign_course.php" class="nav-link">Assign Courses</a>
        <a href="assign_seat.php" class="nav-link">Assign Seats</a>
        <a href="view_seating.php" class=" nav-link">View Seating Plan</a>
        <a href="generate_pdf.php" class="nav-link">Download Seating PDF</a>
        <a href="reminder.php" class="nav-link">Send Exam Reminders</a>
        <a href="logout.php" class="logout">Logout</a>


    </div>

    <?php } else { ?>


    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a class="brand" href="index.php">Exam Seating System</a>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>
    <?php } ?>
    </div>