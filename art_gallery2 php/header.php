<?php
session_start();
$base_url = "http://localhost/art_gallery/";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arts Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Arts Gallery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item"><a class="nav-link btn btn-outline-primary" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-outline-success" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="col-md-4 mx-auto mt-3 text-center">


        <form method="GET" action="search_art.php" class="shadow">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Enter artwork name or keyword..."
                    value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </div>
        </form>
    </div>
    <hr>