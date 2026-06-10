<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VU Proctor-Diary</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>

        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profile.php">My Profile</a>
        <?php if ($_SESSION['role'] === 'superintendent' || $_SESSION['role'] === 'invigilator'): ?>
        <a href="my_duties.php">My Duty</a>

        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="approvel.php">User Approvals</a>
        <a href="assign_duties.php">Assign Duty</a>
        <a href="add_exam.php">Add Exam</a>
        <?php endif; ?>
        <span class="nav-right"><a href="logout.php">Logout</a></span>
        <?php else: ?>

        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>



        <?php endif; ?>
    </nav>
</body>

</html>