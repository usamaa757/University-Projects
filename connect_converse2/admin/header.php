<?php
session_start();
include '../db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
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
    <title>User Dashboard - Connect & Converse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-stat {
            border-left: 5px solid #6f42c1;
        }

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
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-buttons a {
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
        }

        .btn-purple {
            background-color: #6f42c1;
        }

        .btn-pink {
            background-color: #e83e8c;
        }

        .btn-teal {
            background-color: #20c997;
        }

        .btn-orange {
            background-color: #fd7e14;
        }
    </style>
    
</head>

<body>


    <!-- TOP BAR -->
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
        <a href="dashboard.php" class="btn btn-success">Dashboard</a>
        <a href="add_category.php" class="btn btn-purple">Add Category</a>
        <a href="topic_list.php" class="btn btn-pink">Discussions</a>
        <a href="manage_comments.php" class="btn btn-teal">Manage Comments</a>
        <a href="manage_users.php" class="btn btn-orange">Manage Users</a>
        <a href="manage_posts.php" class="btn btn-purple">Manage Posts</a>
        
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>