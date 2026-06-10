<?php
// user_header.php
session_start();
include '../db.php';

$user_id = $_SESSION['user_id'] ?? null;

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

// Fetch user info
$stmt = $conn->prepare("SELECT name, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result()->fetch_assoc();
$name = $user_result['name'] ?? 'User';
$profile_pic = $user_result['profile_pic'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Connect & Converse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Add Bootstrap CSS -->

<style>
    .top-bar {
        background: linear-gradient(135deg, rgb(83, 241, 83), rgb(68, 173, 147));
        color: white;
        padding: 10px 15px;
        font-size: 20px;
        font-weight: bold;
    }

    .top-bar img {
        border: 2px solid white;
    }

    .nav-buttons {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 30px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-purple {
        background-color: #6f42c1;
        color: #fff;
    }

    .btn-purple:hover {
        background-color: #5936a4;
        color: #fff;
    }

    .btn-pink {
        background-color: #d63384;
        color: #fff;
    }

    .btn-pink:hover {
        background-color: #b72c6e;
        color: #fff;
    }

    .btn-teal {
        background-color: #20c997;
        color: #fff;
    }

    .btn-teal:hover {
        background-color: #1aa179;
        color: #fff;
    }

    .btn-orange {
        background-color: #fd7e14;
        color: #fff;
    }

    .btn-orange:hover {
        background-color: #e56f10;
        color: #fff;
    }
</style>


<div class="top-bar d-flex justify-content-between align-items-center px-3">
    <div>Connect & Converse</div>
    <a href="profile.php" class="d-flex flex-column align-items-center text-white text-decoration-none">
        <?php if (!empty($profile_pic)): ?>
            <img src="<?= $profile_pic ?>" alt="Profile" width="40" height="40" class="rounded-circle mb-1">
        <?php else: ?>
            <i class="bi bi-person-circle fs-3 mb-1"></i>
        <?php endif; ?>
        <span style="font-size: 13px;"><?= htmlspecialchars($name) ?></span>
    </a>
</div>

<!-- NAVIGATION BUTTONS -->
<div class="nav-buttons">
    <a href="dashboard.php" class="btn btn-purple">Dashboard</a>
    <a href="create_topic.php" class="btn btn-pink">Create Post</a>
    <a href="search.php" class="btn btn-orange">Search Post</a>
    <a href="my_topic_list.php" class="btn btn-teal">My Posts</a>

    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div></head>