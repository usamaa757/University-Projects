<?php
session_start();
include("../config.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECO Trade Hub</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        .navbar-custom {
            background-color: rgb(80, 166, 252);
            /* Custom background color */
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #fff;
            /* Custom text color */
        }

        .navbar-custom .nav-link:hover {
            color: #ddd;
            /* Lighter text color on hover */
        }

        .form-container {
            background-color: #f7f7f7;
            /* Light background color for the form container */
            border: 1px solid #ddd;
            /* Light border */
            padding: 20px;
            /* Padding inside the form container */
            border-radius: 10px;
            /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Shadow effect */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="<?php echo BASE_PATH; ?>/buyer/buyer_dashboard.php">
            <h3>ECO Trade Hub</h3>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/buyer/buyer_profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/buyer/products_list.php">Products List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/buyer/orders.php">My Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/buyer/chat_list.php">Chat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_PATH; ?>/buyer/part_request_list.php">Request Part List</a>
                </li>
            </ul>
            <form action="search_results.php" class="form-inline ml-auto" id="searchForm">
                <input class="form-control mr-sm-2" type="search" name="q" id="searchInput" placeholder="Search parts" aria-label="Search" required>
                <button class="btn btn-outline-light my-2 my-sm-0 btn-sm" type="submit">Search</button>
            </form>
            <span class="navbar-text ml-auto text-white">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>
            <ul class="navbar-nav ml-3">
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Logout');">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   