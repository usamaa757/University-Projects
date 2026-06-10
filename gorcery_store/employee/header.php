<?php
session_start();

// Check if the employee is logged in
if (!isset($_SESSION['employee_id'])) {
    echo "<script>alert('Please log in to access the dashboard.'); window.location.href = '../login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Grocery Store Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
    .card {
        margin: 20px;
    }

    .dashboard-header {
        text-align: center;
        margin: 20px 0;
    }

    .nav-container {
        max-width: 12000px;
    }
    </style>
</head>

<body>
    <!-- Navbar -->

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="nav-container container">
            <a class="navbar-brand" href="dashboard.php">XYZ Grocery Store</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="add_customer.php">Add Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="add_expense.php">Add Expense</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="add_product.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="add_sale.php">Add Sale</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <!-- Employee Name -->
                    <li class="nav-item">
                        <span class="nav-link text-white">Welcome, <?php echo $_SESSION['employee_name']; ?></span>
                    </li>
                    <!-- Logout Button -->
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php" title="Logout">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>