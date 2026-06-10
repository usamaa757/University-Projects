<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Pateint Record Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    .navbar {
        background-color: #343a40;
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
    }

    .hero-section {
        position: relative;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: url('side-view-doctor-working-office_1048944-25765027.avif') no-repeat center center;
        background-size: cover;
        background-position: center center;
        color: white;
        /* text-shadow: 2px 2px 10px rgba(0,0,0,0.5); */
    }

    .hero-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* Black overlay with 60% opacity */
    }

    .hero-section div {
        position: relative;
        z-index: 1;
        /* Ensure text is above overlay */
    }

    .hero-section .btn {
        margin: 10px;
        padding: 10px 20px;
        font-size: 18px;
        border-radius: 10px;
    }

    .footer {
        background: #343a40;
        color: white;
        text-align: center;
        padding: 15px;
        position: fixed;
        bottom: 0;
        width: 100%;
    }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div>

        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">🏥 Patient Record Management System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/prms/index.php">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/prms/auth/login.php">Login</a>
                        </li>

                        <!-- Dropdown for Register -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Register
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                                <li><a class="dropdown-item"
                                        href="http://localhost/prms/auth/doctor_register.php">Doctor Register</a></li>
                                <li><a class="dropdown-item"
                                        href="http://localhost/prms/auth/patient_register.php">Patient Register</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

    </div>