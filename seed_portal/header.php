<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seed Portal</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>

                <a href="user_dashboard.php">🌾 SeedTrack Portal</a>
                <?php elseif (isset($_SESSION['agent_id']) && $_SESSION['role'] == 'agent'): ?>

                <a href="agent_dashboard.php">🌾 SeedTrack Portal</a>
                <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>

                <a href="admin_dashboard.php">🌾 SeedTrack Portal</a>

                <?php else: ?>
                <a href="index.php">🌾 SeedTrack Portal</a>
                <?php endif; ?>

            </div>

            <nav>
                <ul>

                    <li><a href="seeds.php">Seeds</a></li>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="order_list.php">Orders</a></li>
                    <li><a href="agent_approvel.php">Agents</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>

                    <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                    <li><a href="user_dashboard.php">Dashboard</a></li>
                    <li><a href="my_orders.php">My Orders</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                    <?php elseif (isset($_SESSION['agent_id']) && $_SESSION['role'] == 'agent'): ?>
                    <li><a href="agent_dashboard.php">Dashboard</a></li>
                    <li><a href="my_orders.php">My Orders</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                    <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="user_register.php">User Register</a></li>
                    <li><a href="agent_register.php">Agent Register</a></li>
                    <li><a href="login.php" class="login-btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>