<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS - Patient Record Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f5f8fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .hero {
        background: linear-gradient(135deg, #8e2de2, #4a00e0);
        /* Purple Dream */

        color: white;
        padding: 100px 30px;
        text-align: center;
    }

    .hero h1 {
        font-size: 3.2rem;
        font-weight: bold;
    }

    .hero p {
        font-size: 1.2rem;
    }

    .feature-icon {
        font-size: 2.5rem;
        color: #0d6efd;
    }

    .card:hover {
        transform: scale(1.02);
        transition: 0.3s;
    }

    .footer {
        background: #343a40;
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: 50px;
    }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Patient Record Management System</h1>
            <p class="lead">Digitizing healthcare records for better, faster, and safer medical care.</p>
            <a href="login.php" class="btn btn-light btn-lg mt-3">Login</a>
            <a href="register.php" class="btn btn-outline-light btn-lg mt-3 ms-2">Register</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mt-5">
        <div class="row text-center">
            <h2 class="mb-4">Key Features</h2>

            <div class="col-md-4 mb-4">
                <div class="card shadow p-4 h-100">
                    <div class="feature-icon mb-3">🩺</div>
                    <h5>Medical History</h5>
                    <p>Record and access complete medical history, including allergies and medications.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow p-4 h-100">
                    <div class="feature-icon mb-3">📁</div>
                    <h5>Centralized Records</h5>
                    <p>Eliminate paper files and reduce redundancy with one secure data center.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow p-4 h-100">
                    <div class="feature-icon mb-3">📊</div>
                    <h5>Smart Analytics</h5>
                    <p>Use medical trends and reports for better diagnosis and patient care planning.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="container my-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="cdc-TnrpxHeN3OM-unsplash.jpg" alt="Healthcare" class="img-fluid rounded shadow-sm">
            </div>
            <div class="col-md-6">
                <h3>Why Choose PRMS?</h3>
                <p>The PRMS software is designed to eliminate the hassle of manual records and enable healthcare
                    providers to focus more on patients and less on paperwork. With enhanced accessibility, secure data,
                    and efficient care protocols, PRMS improves outcomes across the board.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">© <?= date("Y") ?> PRMS - Patient Record Management System. All Rights Reserved.</p>
        </div>
    </footer>

</body>

</html>