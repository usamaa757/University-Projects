<?php
session_start();

// Check if the user is logged in and is a job seeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header("Location: ../login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../style.css">
    <title>Online Job Portal</title>

</head>

<body>


    <body>

        <header>
            <h1>Welcome to the Job Portal</h1>
            <p>Employer Dashboard</p>
        </header>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="job_list.php">Manage Job</a>
            <a href="post_job.php">Post Job</a>
            <a href="../logout.php">Logout</a>
        </nav>