<?php

include '../db.php';
include 'header.php';

// Get user details
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

?>

<div class="container mt-5">
    <h3 class="mb-4 text-center">Artist Dashboard</h3>
    <div class="card shadow-lg rounded-4 border p-4">
        <h2 class="text-center">Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h2>

        <div class="text-center">

            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
        <div class="text-center">
            <a href="profile.php" class="btn">Update Profile</a>
        </div>
    </div>
</div>