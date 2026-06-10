<?php
session_start();
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

    .sidebar {
        height: 100vh;
        background-color: #343a40;
        color: white;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
    }

    .sidebar a:hover {
        background-color: #495057;
    }

    .profile-pic {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid white;
    }

    .card-stat {
        border-left: 5px solid #6f42c1;
    }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 p-3 sidebar d-flex flex-column justify-content-between">
                <div>
                    <div class="text-center mb-4">
                        <h5><?= $name ?></h5>
                        <small><?= $email ?></small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-house-door me-2"></i>
                                Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-chat-dots me-2"></i> My
                                Discussions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person me-2"></i> Edit
                                Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right me-2"></i>
                                Logout</a></li>
                    </ul>
                </div>
                <div class="text-center">
                    <small>&copy; <?= date("Y") ?> Connect & Converse</small>
                </div>
            </div>