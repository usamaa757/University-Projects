<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role']) == 'admin') {
    header("Location: ../login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Art Gallery</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
        }

        .navbar {

            background-color: #000000;
            padding: 1rem 2rem;
        }


        nav ul {
            list-style: none;
            display: flex;
        }

        .brand-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #38bdf8;
        }

        .nav-link {
            color: #fff;
        }

        .nav-item a:hover {
            color: #38bdf8;
        }


        .btn {
            background-color: #38bdf8;
            color: white;

        }

        .btn:hover {
            background-color: rgb(71, 155, 194);
            color: white;

        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <span class="brand-title">Art Gallery</span>
            <button class=" navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="art_list.php">Arts</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">Manage User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_arts.php">Manage Arts</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="manage_orders.php">Manage Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Bootstrap 5 JS and CSS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pzjw8f+ua7Kw1TIq0Gso3oBzmqB8c1dujdK4y4ysdF5vR5xtaxlqTPQAWf+0gL+L" crossorigin="anonymous">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KyZXEJ6H7a0B1xF9E9o21fO1s6v3a7z5D0y6U0pST51Nfiz+z5hUOduHXM2Flsuk" crossorigin="anonymous">