<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
                <img src="../assets/img/logo.png" alt="logo">
                <!-- <div>
                    
                </div> -->
            </div>
            <div class="sidebar-menu">
                <!-- <div class="menu-head">
                    <a href="admin_panel.php">
                        <h2>About Us</h2>
                    </a>
                </div> -->
                <ul>
                    <li> <a href="../index.php"> Home </a> </li>
                    <li> <a href="admission.php">Admissions</a> </li>
                    <li> <a href="contact_us.php"> Contact Us </a> </li>
                  
                </ul>
            </div>
        </div>

    </div>

    <div class="main-content">

        <header>
            <div class="menu-toggle">
                <label for="sidebar-toggle">

                    <span class="las la-bars"></span>
                </label>
            </div>

            <div class="header-icons">
            <a href="../login/admin_login.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        Login

                    </button></a>

            <a href="../student/student_registration.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        Student SignUp

                    </button></a>

            </div>
        </header>

        <main>

            <!-- <div class="page-header">
                <div>
                    <h1>About Us</h1>
                </div>

            </div> -->






            <section class="contact" id="contact">
                <div class="container">
                    <div class="heading text-center">
                        <h2>Contact
                            <span> Us </span>
                        </h2>
                        <p>Connecting, Caring and Resolving!
                        </p>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="title">
                                <h3>Contact details</h3>

                            </div>
                            <div class="content">
                                <!-- Info-1 -->
                                <div class="info">
                                    <i class="fas fa-mobile-alt"></i>
                                    <h4 class="d-inline-block">PHONE :
                                        <br>
                                        <span>+12457836913 , +12457836913</span>
                                    </h4>
                                </div>
                                <!-- Info-2 -->
                                <div class="info">
                                    <i class="far fa-envelope"></i>
                                    <h4 class="d-inline-block">EMAIL :
                                        <br>
                                        <span>example@info.com</span>
                                    </h4>
                                </div>
                                <!-- Info-3 -->
                                <div class="info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <h4 class="d-inline-block">ADDRESS :<br>
                                        <span>31-Industrial Area, Gurumangat Road, Gulberg III, Lahore, Pakistan.</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>




        </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>