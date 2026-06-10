<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details
$user_name = $_SESSION['first_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Home</a></li>


            <li><a href="#"><i class="fas fa-user-cog"></i> Manage Users</a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> View Reports</a></li>


            <li><a href="#"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <h2>Welcome, <?php echo $user_name; ?> </h2>
        </header>

        <section class="dashboard-cards">

            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Manage Users</h3>
                <p>View and manage registered users.</p>
                <a href="#">Go to Users</a>
            </div>
            <div class="card">
                <i class="fas fa-chart-bar"></i>
                <h3>Reports</h3>
                <p>View website analytics and reports.</p>
                <a href="#">View Reports</a>
            </div>

        </section>
    </div>

</body>

</html>