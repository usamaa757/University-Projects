<?php

session_start();
if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'user') {
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <img src="../logo.png" alt="Earth Con Logo" class="site-logo">
        </a>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="calculator.php">Calculator</a></li>
            <li><a href="listings.php">Listings</a></li>
            <li><a href="installments.php">Installments</a></li>
            <li><a href="purchase_list.php">Purchase Property</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>