<?php
$base_url = "http://localhost/career_hub/";

session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id']) && $_SESSION['role'] != 'employer') {
    echo "<script>alert('User is not authorised!'); window.location='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Career Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= $base_url ?>employer/dashboard.php">Career Hub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>employer/post_job.php">Post Job</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>employer/job_list.php">Job List</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>employer/view_applications.php">View
                            Applications</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-danger"
                            href="<?= $base_url ?>logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>