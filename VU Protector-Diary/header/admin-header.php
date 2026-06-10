<?php

session_start();
include '../connection.php';


// Auto restore availability when leave ends

mysqli_query($con, "
    UPDATE user u
    JOIN leaves l ON u.id = l.user_id
    SET u.status='Available'
    WHERE l.status='Approved'
    AND CURDATE() > l.leave_to
");


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Document</title>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="Dashboard.php">
            <b style="font-size:25px;"><i>Admin Dashboard</i></b>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/View_User.php"><b>View User</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/add_exam.php"><b>Add Exams</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/assign_exams.php"><b>Assign Exams</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/verify_attendance.php"><b>Attendance</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/verify_leave.php"><b>Leaves</b></a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/payments.php"><b>Payment</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../Admin/view_reports.php"><b>Reports</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../index.php"><b>Logout</b></a>
                </li>

            </ul>
        </div>
    </nav>

    <!-- Bootstrap JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>