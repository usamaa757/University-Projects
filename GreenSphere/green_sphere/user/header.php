<?php
// Define base URL for the project
$base_url = "http://localhost/green_sphere/"; // Change this based on your project folder and environment
session_start();
include 'notifications.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../login.php");
    exit();
}

// User Info (dummy data, in real case you should fetch from session/database)
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$user_name = $_SESSION['user_name'];

// // Redirect if not an organizer
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}
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




<!-- Sidebar -->
<!-- <div class="sidebar">
        <a href="<?php echo $base_url; ?>organizer/dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i>
            Dashboard</a>
        <a href="<?php echo $base_url; ?>users.php"><i class="fas fa-users"></i> Users</a>
        <a href="<?php echo $base_url; ?>settings.php"><i class="fas fa-cogs"></i> Settings</a>
        <a href="<?php echo $base_url; ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div> -->

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
                <li class="nav-item"><a class="nav-link" href="plants_list.php">Plants List</a></li>
                <li class="nav-item"><a class="nav-link" href="order_details.php">Order List</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="query.php">Plant Queries</a></li>
            </ul>
            <div class="user-info">
                <!-- Notifications Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="badge badge-danger"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu " style="max-width:600px; margin-left:-355px;"
                        aria-labelledby=" notificationsDropdown">
                        <?php if ($unread_count > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <a class="dropdown-item"
                                    href="mark_as_read.php?notification_id=<?php echo $notification['notification_id']; ?>">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </a>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <a class="dropdown-item" href="">No new notifications</a>
                        <?php endif; ?>
                    </div>
                </div>
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