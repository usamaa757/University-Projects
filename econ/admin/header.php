<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Earth Con – Real Estate Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <img src="../logo.png" alt="Earth Con Logo" class="site-logo">
        </a>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="add_agent.php">Add Agent</a></li>
            <li><a href="manage_agents.php">Manage Agent</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>