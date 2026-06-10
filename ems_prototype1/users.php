<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch one user from the database (excluding the logged-in user)
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">EMS Prototype</div>
        <ul>
            <li><a class="logout" href="logout.php">Logout</a></li>
        </ul>
    </nav>


    <div class="profile-container">
        <?php if ($user) { ?>
        <img src="images/<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'default.png'; ?>"
            alt="Profile Picture" class="profile-img">
        <div class="user-info">
            <strong>Name:</strong> <?= htmlspecialchars($user['full_name']); ?><br>
            <br>
            <strong>Study Program:</strong> <?= htmlspecialchars($user['study_program']); ?><br><br>
            <strong>Email:</strong> <?= htmlspecialchars($user['email']); ?><br><br>

        </div>
        <strong>About Me:</strong>
        <div class="profile-about"><?= nl2br(htmlspecialchars($user['about_me'])); ?></div>
        <?php } else { ?>
        <p>No user found!</p>
        <?php } ?>
    </div>

</body>

</html>