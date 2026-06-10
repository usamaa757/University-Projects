<?php
session_start();
$base_url = "http://localhost/art_gallery/";

if (!isset($_SESSION['role']) == 'admin') {
    header("Location: {$base_url}login.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Art Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Art Gallery</a>

            <div>

                <a href="profile.php" class="btn btn-outline-primary text-white">Profile</a>
                <a href="manage_user.php" class="btn btn-outline-success text-white">Manage User</a>
                <a href="manage_art.php" class="btn btn-outline-light">Manage Art</a>
                <a href="manage_orders.php" class="btn btn-outline-warning text-white">Manage Orders</a>
                <a href="<?= $base_url . "logout.php" ?>" class="btn btn-outline-danger text-white">Logout</a>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>