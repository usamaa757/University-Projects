<?php
session_start();
if (!isset($_SESSION['user_id'])) {
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
            <li><a href="user_dashboard.php"><i class="fas fa-home"></i> Home</a></li>


            <li><a href="#"><i class="fas fa-shopping-cart"></i> Shop</a></li>
            <li><a href="#"><i class="fas fa-box"></i> My Orders</a></li>

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
                <i class="fas fa-shopping-bag"></i>
                <h3>Shop Now</h3>
                <p>Explore our latest products.</p>
                <a href="#">Go to Shop</a>
            </div>
            <div class="card">
                <i class="fas fa-receipt"></i>
                <h3>My Orders</h3>
                <p>Track and manage your orders.</p>
                <a href="#">View Orders</a>
            </div>

        </section>
    </div>

</body>

</html>