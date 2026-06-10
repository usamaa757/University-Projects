<?php require_once(__DIR__ . '/config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambulance Booking System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/index.php">ABS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_PATH; ?>/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_PATH; ?>/user/user_registration.php">Register</a>
                </li>
                <!-- Login Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Login
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/user/user_login.php">User Login</a>
                        <a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/admin_login.php">Admin Login</a>
                    </div>
                </li>
                 
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_PATH; ?>/hospitals.php">Hospitals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo BASE_PATH; ?>/contact.php">Contact Us</a>
                </li>
            </ul>
        </div>
    </nav>
