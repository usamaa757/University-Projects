<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
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
    <title>Pakistan Travel Group Social System</title>
</head>

<body>


    <body>

        <header>
            <h1>Welcome to Pakistan Travel Group Social System</h1>
            <p>Your gateway to new Travel opportunities</p>
        </header>

        <nav>

            <a href="dashboard.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="my_created_groups.php">Manage My Groups</a>
            <a href="create_group.php">Create Group</a>
            <a href="../logout.php" class="logout-btn">Logout</a>

        </nav>