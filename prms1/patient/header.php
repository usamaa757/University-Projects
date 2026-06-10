<?php
session_start();

// Redirect to login if not logged in or wrong role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: user_login.php");
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
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="patient_dashboard.php">🏥 PRMS</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Welcome, Patient</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout 🔒</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>