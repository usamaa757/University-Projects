<?php
session_start();

// Redirect to login if not logged in or wrong role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receptionist') {
    header("Location: ../auth/login.php");
    exit;
}

// You can fetch more patient data from the DB here if needed
$patient_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="receptionist_dashboard.php">🏥 PRMS</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link active" href="add_medicine.php">Add Medicines</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="medicine_list.php">Medicines</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="#">Welcome, Receptionist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout 🔒</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>