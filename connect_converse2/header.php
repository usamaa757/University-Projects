<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Connect & Converse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }

        .navbar {
            background: linear-gradient(135deg, rgb(83, 241, 83), rgb(68, 173, 147));
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }

        .hero {
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1659356874266-c672f53e6473?ixlib=rb-4.1.0&q=85&fm=jpg&crop=entropy&cs=srgb&dl=fotos-ktQDb-EXLOo-unsplash.jpg');
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            color: white;
            z-index: 0;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(82, 81, 81, 0.6);
            /* darker background */
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
        }


        .hero h1 {
            font-size: 3.2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .btn-join {
            font-size: 1.1rem;
            padding: 12px 28px;
            border-radius: 30px;
        }

        footer {
            background-color: #f1f1f1;
            padding: 20px 0;
            color: #555;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>


    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg ">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Connect & Converse</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>