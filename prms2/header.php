<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PRMS</title>
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="index.php">PRMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav fw-semibold">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Register
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                            <li><a class="dropdown-item" href="register_doctor.php">Doctor</a></li>
                            <li><a class="dropdown-item" href="register_patient.php">Patient</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap 5.3.0 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>