<?php
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>



<div class="navbar">
    
        <?php if(isset($_SESSION['user_id'])) { ?>

    <a href="dashboard.php" class="logo">Fitness Tracker</a>

 <?php } else { ?>
    <a href="index.php" class="logo">Fitness Tracker</a>

        <?php } ?>

    <div class="nav-links">

        <?php if(isset($_SESSION['user_id'])) { ?>
            
            <span class="username">Welcome, <?php echo ucfirst($_SESSION['role']); ?></span>
            <a href="logout.php" class="delete">Logout</a>

        <?php } else { ?>

            <a href="index.php">Home</a>
            <a href="view_routines.php">Workout Routines</a>
            <a href="view_tips.php">View Tips</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>

        <?php } ?>

    </div>
</div>

</body>
</html>