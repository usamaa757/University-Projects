<?php
$base_url = "http://localhost/green_sphere/"; // Change this based on your project folder and environment
session_start();

// Check if the user is logged in
if (!isset($_SESSION['seller_id']) || $_SESSION['role'] !== 'Seller') {
    header("Location: ../login.php");
    exit();
}

// User Info (dummy data, in real case you should fetch from session/database)
$seller_id = $_SESSION['seller_id'];
$user_role = $_SESSION['role'];
$user_name = $_SESSION['seller_name'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenSphere</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="dashboard.php">Green Sphere</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon fa fa-bars"></span>

        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="add_plants.php">Add Plants</a></li>
                <li class="nav-item"><a class="nav-link" href="plants_list.php">Plants List</a></li>
                <li class="nav-item"><a class="nav-link" href="orders_list.php">Orders List</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
                <li class="nav-item"><a class="nav-link" href="add_plant_care.php">Plant Care</a></li>
                <li class="nav-item"><a class="nav-link" href="user_query.php">User Queries</a></li>
                <li class="nav-item"><a class="nav-link" href="my_reviews.php">My Reviews</a></li>

            </ul>
            <div class="user-info">
                <span><?php echo ucfirst($user_role); ?></span>
                <a href="<?php echo $base_url; ?>logout.php" class="btn btn-logout text-danger">
                    <i class="fas fa-power-off text-danger"></i>
                </a>
            </div>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>