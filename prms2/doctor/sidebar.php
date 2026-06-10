<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Select2 CSS (Make sure you add this in your <head> if not already added) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 JS and Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery (important for Select2) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            width: 250px;
            background-color: #000000;
            min-height: 100vh;
            color: white;
            padding: 30px 15px;
            position: fixed;
        }

        .sidebar h2 {
            font-size: 1.5rem;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            display: block;
            margin: 10px 0;
            text-decoration: none;
        }

        .sidebar a:hover {
            text-decoration: underline;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 30px;
            color: #0d6efd;
        }

        .footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 60px;
            color: #999;
        }

        nav.navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            margin-left: 250px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            nav.navbar {
                margin-left: 0;
            }
        }
    </style>

    <!-- BOOTSTRAP ICONS -->
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block">
        <h2 class="text-center">PRMS</h2>
        <!-- Sidebar links -->
        <a href="dashboard.php"><i class="bi bi-house-door me-2 text-info"></i> Dashboard</a>
        <a href="appointment_list.php"><i class="bi bi-person-lines-fill me-2 text-info"></i> Treat Patients</a>
        <a href="patient_list.php"><i class="bi bi-person-lines-fill me-2 text-info"></i> Patients</a>
        <a href="appointments.php"><i class="bi bi-calendar-check me-2 text-info"></i> Appointments</a>
        <a href="instruction_list.php"><i class="bi bi-calendar-plus me-2 text-info"></i> Instructions</a>

        <a href="../logout.php"><i class="bi bi-box-arrow-right me-2 text-info"></i> Logout</a>



    </div>
    <?php
    include 'header.php';

    ?>