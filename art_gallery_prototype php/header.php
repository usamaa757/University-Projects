<?php
session_start();



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
            <a class="navbar-brand" href="#">Art Gallery</a>
            <div>
                <?php
                if (isset($_SESSION['user_id'])) {
                    if ($_SESSION['role'] == 'admin') {
                ?>
                <a href="manage_art.php" class="btn btn-light">Manage Art</a>


                <?php } elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'seller') { ?>
                <a href="add_art.php" class="btn text-white">Add Art</a>
                <a href="view_art.php" class="btn text-white">View Art</a>

                <?php }

                    ?>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                <?php } else { ?>
                <a href="register.php" class="btn btn-outline-success">Resgister</a>
                <a href="login.php" class="btn btn-outline-primary">Login</a>
                <?php } ?>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>