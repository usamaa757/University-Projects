<?php
session_start();
$base_url = "http://localhost/online_book_exchange/";

$admin_name = $_SESSION['admin_name'];

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['Admin_role'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as Admin first."));
    exit();
}
?>
<style>
body {
    zoom: 90%;
}
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Exchange</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-white">
        <a class="navbar-brand" href="admin_dashboard.php">
            <h3>Online Book Exchange
        </a></h3>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="user_query.php">User Queries</a>
                </li>


            </ul>
            <span class="navbar-text text-white">
                Welcome, <?php echo htmlspecialchars($admin_name); ?>
            </span>
            <ul class="navbar-nav ml-3">
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to Logout');">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>