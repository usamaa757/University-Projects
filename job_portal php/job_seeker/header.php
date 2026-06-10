<?php
session_start();

// Check if the user is logged in and is a job seeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seeker') {
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
            <p>Job Seeker Dashboard</p>
        </header>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="search_jobs.php">Search Job</a>
            <a href="applied_jobs.php">View Applied Jobs</a>
            <a href="../logout.php">Logout</a>
        </nav>