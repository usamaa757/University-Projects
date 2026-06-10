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
    <title>Exam Seating Arrangement System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .hero-section {
        background: url('exam_seating.jpg') no-repeat center center/cover;
        height: 50vh;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 1.5rem;
    }

    .card-hover:hover {
        transform: scale(1.05);
        transition: 0.3s;
    }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Exam Seating System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">


                    <?php if ($admin_id) {
                        if (!isset($_SESSION['admin_id'])) {
                            header('Location: login.php');
                            exit();
                        } ?>
                    <li class="nav-item">
                        <a href="add_data.php" class="nav-link">Add Data</a>
                    </li>
                    <li class="nav-item">
                        <a href="assign_exam.php" class="nav-link">Assign Exams</a>
                    </li>
                    <li class="nav-item">
                        <a href="assign_seats.php" class="nav-link">Assign Seats</a>
                    </li>
                    <li class="nav-item">
                        <a href="view_seating.php" class="nav-link">View Seating Plan</a>
                    </li>
                    <li class="nav-item">
                        <a href="generate_pdf.php" class="nav-link">Download Seating PDF</a>
                    </li>
                    <li class="nav-item">
                        <a href="send_reminder.php" class="nav-link">Send Exam Reminders</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link btn btn-outline-danger">Logout</a>
                    </li>
                    <?php
                    } elseif ($student_id) {
                        if (!isset($_SESSION['student_id'])) {
                            header('Location: login.php');
                            exit();
                        } ?>

                    <li class="nav-item">
                        <a class="nav-link" href="generate_pdf.php">Download PDF</a>
                    </li>

                    <li class="nav-item">
                        <a href="logout.php" class="nav-link btn btn-outline-danger">Logout</a>
                    </li>
                    <?php
                    } else {
                    ?>
                    <li class="nav-item"><a class="nav-link btn btn-outline-primary" href="login.php">Login</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-success px-3" href="register.php">Register</a>
                    </li>
                    <?php
                    } ?>

                </ul>

                </li>
                </ul>
            </div>
        </div>
    </nav>