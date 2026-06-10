<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <title>Pakistan Travel Group Social System</title>
</head>

<body>


    <body>

        <header>
            <h1>Welcome to Pakistan Travel Group Social System</h1>
            <p>Your gateway to new Travel opportunities</p>
        </header>

        <nav>
            <?php

            if (!isset($_SESSION['user_id'])) {
            ?>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <?php
            } else {
            ?>

            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="my_created_groups.php">Manage My Groups</a>

            <a href="my_groups.php">Joined Groups</a>
            <a href="create_group.php">Create Group</a>
            <a href="view_groups.php">Groups List</a>
            <a href="logout.php" class="logout-btn">Logout</a>

            <?php
            }
            ?>
        </nav>