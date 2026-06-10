<?php

session_start();
$base_url = 'http://localhost/cosmetic_store/';
if (!isset($_SESSION['owner_id'])) {
    header("Location: {$base_url}login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cosmetic Store</title>
    <link rel="stylesheet" href="<?= $base_url . 'asset/styles.css' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>


<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="dashboard.php" class="dashboard-logo">
            <h2>Dashboard</h2>
        </a>
        <ul>

            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="view_product.php">Manage Product</a></li>
            <li><a href="add_service.php">Add Service</a></li>
            <li><a href="order_list.php">Orders List</a></li>

            <li><a href="profile.php">View Profile</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="<?= $base_url . 'logout.php' ?>">Logout</a></li>

        </ul>
    </div>