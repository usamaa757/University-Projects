<?php
session_start();
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Prayer Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f4f6f9;
        }

        .stat-box {
            border-radius: 12px;
            transition: 0.3s ease;
        }

        .stat-box:hover {
            transform: scale(1.03);
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">Prayer Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link active" href="add_prayer.php">Add Prayer</a></li>
                    <li class="nav-item"><a class="nav-link active" href="prayer_record.php">Prayer Record</a></li>
                    <li class="nav-item"><a class="nav-link" href="daily_report.php">Daily Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="qaza_list.php">Qaza List</a></li>
                    <li class="nav-item"><a class="nav-link" href="guidance.php">Guidance</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>