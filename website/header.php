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
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <!-- <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div> -->
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light bg-primary px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    <h1 class="m-0">VU LMS</h1>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="handouts.php" class="nav-item nav-link">Handouts</a>
                        <a href="mid_papers.php" class="nav-item nav-link">Mid Term</a>
                        <a href="final_papers.php" class="nav-item nav-link">Final Term</a>
                        <!-- <a href="service.php" class="nav-item nav-link">Service</a>
                        <a href="project.php" class="nav-item nav-link">Project</a> 
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Papers</a>
                            <div class="dropdown-menu m-0">
                                <a href="mid_papers.php" class="dropdown-item">Mid Term</a>
                                <a href="final_papers.php" class="dropdown-item">Final Term</a>
                                <a href="404.php" class="dropdown-item">404 Page</a>
                            </div>
                        </div>-->
                        <a href="softwares.php" class="nav-item nav-link">Softwares</a>
                        <!-- <a href="contact.php" class="nav-item nav-link">Contact</a> -->
                    </div>
                    <div class="navbar-nav auth-buttons">

                        <a href="register.php"
                            class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3">Register</a>
                        <a href="login.php" class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3">Login</a>
                    </div>

                </div>
            </nav>

            <!-- <div class="container-xxl bg-primary hero-header">
                <div class="container px-lg-5">
                    <div class="row g-5 align-items-end">
                        <div class="col-lg-6 text-center text-lg-start">
                            <h1 class="text-white mb-4 animated slideInDown">A Digital Agency Of Inteligents & Creative
                                People</h1>
                            <p class="text-white pb-3 animated slideInDown">Tempor rebum no at dolore lorem clita rebum
                                rebum ipsum rebum stet dolor sed justo kasd. Ut dolor sed magna dolor sea diam. Sit diam
                                sit justo amet ipsum vero ipsum clita lorem</p>
                            <a href=""
                                class="btn btn-secondary py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">Read
                                More</a>
                            <a href="" class="btn btn-light py-sm-3 px-sm-5 rounded-pill animated slideInRight">Contact
                                Us</a>
                        </div>
                        <div class="col-lg-6 text-center text-lg-start">
                            <img class="img-fluid animated zoomIn" src="img/hero.png" alt="">
                        </div>
                    </div>
                </div>-->
        </div>
    </div>
    <!-- Navbar & Hero End -->


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
    </style><!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/isotope/isotope.pkgd.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>