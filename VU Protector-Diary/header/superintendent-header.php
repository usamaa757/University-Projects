<?php

session_start();

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


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href=""><b style="font-size:25px;"><i>Superintendent Dashboard</i></b></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="../superintendent/update_profile.php"><b>Update Profile</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../superintendent/mark_attendance.php"><b>Mark Attendance</b></a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="../superintendent/apply_leave.php"><b>Apply Leave</b></a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link" href="../superintendent/payments.php"><b>Payments</b></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="../superintendent/upload_report.php"><b>Upload Reports</b></a>
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