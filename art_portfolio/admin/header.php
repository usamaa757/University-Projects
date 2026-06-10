<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../style.css">
    <title>Art Portfolio Hub</title>
</head>

<body>


    <body>

        <header>
            <h1>Welcome to Art Portfolio Hub</h1>
            <p>Discover, showcase, and connect through creativity.</p>
        </header>

        <nav>




            <a href="dashboard.php">Home</a>
            <a href="manage_user.php">Manage User</a>
            <a href="add_category.php">Manage Categories</a>
            <a href="add_user.php">Add User</a>
            <a href="manage_art.php">Manage Art</a>
            <a href="reported_comments.php">Manage Comments</a>

            <a href="../logout.php" class="logout-btn">Logout</a>

        </nav>