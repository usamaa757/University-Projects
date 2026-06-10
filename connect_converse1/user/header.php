<?php
session_start();
$name = $_SESSION['name'];
$profile_pic = $_SESSION['profile_pic'];
$base_url = 'http://localhost/connect_converse/user/';

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

        .navbar,
        .card-header {
            background: linear-gradient(135deg, #6f42c1, #8e44ad);

        }

        .nav-link,
        .navbar-brand {
            color: white;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-chat-text"></i> Connect & Converse
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_topic.php">Create Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="topic_list.php">Discussions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_topic_list.php">My Topics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search_result.php">Search</a>
                    </li>
                </ul>
                <span class="navbar-text me-3 d-flex align-items-center">
                    <?php
                    if ($profile_pic): ?>
                        <img src="<?= str_replace("user/", "", $profile_pic); ?>" alt="Profile" width="30" height="30"
                            class="rounded-circle me-2">
                    <?php else: ?>
                        <i class="bi bi-person-circle me-2" style="font-size: 1.5rem;"></i>
                    <?php endif; ?>
                    <a href="profile.php"><?= htmlspecialchars($name) ?></a>
                </span>

                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>