<?php
ob_start();
include '../db_connection.php'; // Include database connection
session_start();

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch the user from the students table based on email
    $sql = "SELECT * FROM admin WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        // Verify the password
        if (password_verify($password, $student['password'])) {
            // Store user data in session variables
            $_SESSION['admin_id'] = $student['admin_id'];
            $_SESSION['name'] = $student['name'];
            $_SESSION['email'] = $student['email'];

            // Redirect to the dashboard or home page
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            $message = "Incorrect email or password!";
            header("Location: admin_login.php?status=error&message=" . urlencode($message));
            exit();
        }
    } else {
        // No user found with this email
        $message = "No account found with this email!";
        header("Location: admin_login.php?status=error&message=" . urlencode($message));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>VULMS</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link href="../css/student_style.css" rel="stylesheet">

</head>

<body>
    <div class="container-xxl bg-primary p-0">
        <!-- Navbar Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg  px-lg-5 py-3">
                <a href="#" class="text-danger navbar-brand">VULMS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="../index.php" class="nav-item text-white nav-link active">Home</a>
                        <a href="handouts.php" class="nav-item text-white nav-link active">Handouts</a>
                        <!-- Dropdown for Past Papers -->
                        <div class="nav-item dropdown">
                            <a class="nav-link text-white dropdown-toggle" href="#" id="pastPapersDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Past Papers
                            </a>
                            <ul class="dropdown-menu text-white" aria-labelledby="pastPapersDropdown">
                                <li><a class="text-primary dropdown-item" href="mid_papers.php">Mid Term</a></li>
                                <li><a class="text-primary dropdown-item" href="mid_papers.php">Final Term</a></li>
                            </ul>
                        </div>
                        <a href="../about.php" class="text-white nav-item nav-link">About</a>
                        <a href="../service.php" class="text-white nav-item nav-link">Service</a>
                        <a href="../project.php" class="text-white nav-item nav-link">Project</a>
                        <!-- Dropdown for Past Papers -->
                        <!-- <div class="nav-item dropdown text-white">
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
                    <a href="../register.php"
                        class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3 d-none d-lg-block">Register</a>
                    <a href="../login.php"
                        class="btn bg-secondary text-dark rounded-pill py-2 px-4 ms-3 d-none d-lg-block">Login</a>

                </div>

            </nav>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>


    <!-- JavaScript Libraries -->
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
    <div class="container-xxl py-5">
        <div class="container py-5 px-lg-5">
            <div class="wow fadeInUp" data-wow-delay="0.1s">
                <p class="section-title text-secondary justify-content-center"><span></span>Login<span></span></p>
                <h1 class="text-center mb-5">Login to Your Account</h1>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="wow fadeInUp" data-wow-delay="0.3s">
                        <p class="text-center mb-4">Please enter your email and password to login to your account.</p>

                        <?php
                        // Check if there's a message passed in the URL
                        if (isset($_GET['message'])) {
                            $status = $_GET['status'];
                            $message = htmlspecialchars($_GET['message']);

                            // Display the message based on status
                            if ($status == 'success') {
                                echo "<p style='color: green;'>" . $message . "</p>";
                            } elseif ($status == 'error') {
                                echo "<p style='color: red;'>" . $message . "</p>";
                            }
                        }
                        ?>

                        <form method="POST" action="admin_login.php">
                            <div class="col-md-12 mb-4">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Your Email" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="form-floating">
                                    <input type="password" class="form-control" name="password" id="password"
                                        placeholder="Password">
                                    <label for="password">Password</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "../footer.php";
    ob_end_flush();
    ?>