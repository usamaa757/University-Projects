<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title> Furniture Hub</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="navbar">
        <a href="dashboard.php" class="logo">Furniture Hub</a>
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>

                <li><a href="dashboard.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="management.php">Management</a></li>

                <?php elseif ($_SESSION['role'] === 'seller'): ?>
                    <li><a href="furniture.php">Manage Furniture</a></li>

                <?php elseif ($_SESSION['role'] === 'buyer'): ?>
                    <li><a href="furniture_list.php">Browse Furniture</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>

            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>

</body>

</html>