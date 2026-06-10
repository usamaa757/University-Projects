<?php
session_start();
$base_url = "http://localhost/art_gallery/";
if (!isset($_SESSION['role']) == 'seller') {
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
            <a class="navbar-brand" href="seller_dashboard.php">Art Gallery</a>
            <div>

                <a href="profile.php" class="btn btn-outline-primary text-white">Profile</a>
                <a href="add_art.php" class="btn btn-outline-success text-white">Add Art</a>
                <a href="view_art.php" class="btn btn-outline-warning text-white">View Art</a>


                <a href="<?= $base_url . "logout.php" ?>" class="btn btn-outline-danger text-white">Logout</a>

            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>