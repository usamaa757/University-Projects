<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>VULMS</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Jost:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl  p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    <h3 class="m-0 text-secondary">VULMS</h3>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="index.php" class="nav-item text-white text-white nav-link active">Home</a>
                        <a href="mid_papers.php" class="nav-item text-white text-white nav-link">Handouts</a>
                        <a href="final_papers.php" class="nav-item text-white text-white nav-link">Final Term</a>
                        <!-- Dropdown for Past Papers -->
                        <!-- <div class="nav-item text-white dropdown">
                            <a class="nav-link text-white dropdown-toggle" href="#" id="pastPapersDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Past Papers
                            </a>
                            <ul class="dropdown-menu text-white" aria-labelledby="pastPapersDropdown">
                                <li><a class="text-primary dropdown-item" href="mid_papers.php">Mid Term</a></li>
                                <li><a class="text-primary dropdown-item" href="final_papers.php">Final Term</a></li>
                            </ul>
                        </div> -->
                        <a href="about.php" class="text-white nav-item text-white nav-link">About</a>
                        <a href="service.php" class="text-white nav-item text-white nav-link">Service</a>
                        <a href="project.php" class="text-white nav-item text-white nav-link">Project</a>
                        <!-- Dropdown for Past Papers -->
                        <!-- <div class="nav-item text-white dropdown text-white">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="pastPapersDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Past Papers
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="pastPapersDropdown">
                                <li><a class="text-white dropdown-item" href="mid_papers.php">Mid Term</a></li>
                                <li><a class="text-white dropdown-item" href="final_papers.php">Final Term</a></li>
                            </ul>
                        </div> -->

                    </div>
                    <div class="navbar-nav auth-buttons">

                        <a href="register.php"
                            class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3">Register</a>
                        <a href="login.php" class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3">Login</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <style>
    /* Default styling: stack buttons vertically */
    .auth-buttons {
        display: flex;
        flex-direction: column;
    }

    /* Media query for larger screens */
    @media (min-width: 265px) {

        /* Adjust breakpoint as needed */
        .auth-buttons {
            flex-direction: row;
            /* Display buttons in a row */
            align-items: center;
            /* Align vertically centered */
        }

        .auth-buttons a {
            margin-left: 8px;
            margin-bottom: 10px;
            /* Add spacing between buttons */

            /* Optional: Adjust spacing between buttons */
        }

        .auth-buttons {
            padding: 10px;
            /* Optional: Adjust spacing between buttons */
        }

        auth-buttons a:last-child {
            margin-bottom: 0;
        }

    }
    </style>
    <?php
// session_start();
include 'footer.php';

?>