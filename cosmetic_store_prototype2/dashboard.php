<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$admin_id = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <?php if ($admin_id): ?>
            <li><a href="#">Manage Users</a></li>
            <li><a href="#">View Reports</a></li>
            <li><a href="#">Settings</a></li>
            <?php else: ?>
            <li><a href="#">View Profile</a></li>
            <li><a href="#">My Orders</a></li>
            <li><a href="#">Support</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

</body>

</html>