<?php
include 'admin_login_process.php';
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                <img src="../assets/img/logo.png" alt="logo Image">


            </div>
           <div class="sidebar-menu">

                <ul>
                    <li> <a href="../index.php"> Home </a> </li>
                    <li> <a href="../misc/admission.php">Admissions</a> </li>
                    <li> <a href="../misc/contact_us.php"> Contact Us </a> </li>
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


                <a href="../student/student_registration.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        Student SignUp

                    </button></a>

            </div>
        </header>


        <main>


            <div class="login-container">
                <div class="submit-btn">
                    <a href="admin_login.php"> <button>Admin</button></a>
                  
                    <a href="student_login.php"> <button>Student</button></a>


                </div>
                <br>
                <div class="form-container">
                    <div class="headding">
                        <h3>Admin Login</h3>

                    </div>
                    <div class="form">

                        <form method='POST' action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' id="form">

                            <div class="result-output">
                                <?php if (!empty($errorMsg)) : ?>
                                    <div id="error"><?php echo $errorMsg; ?></div>
                                <?php endif; ?>
                            </div>



                            <label for="email">Email</label>
                            <input type="email" placeholder="Email" name="email" id="email">

                            <label for="password">Password</label>
                            <input type="password" name="password" placeholder="Password" id="password">

                            <div class="submit-btn">
                                <button type="submit">Log In</button>
                            </div>
                        </form>




                    </div>
                </div>


        </main>


    </div>
    <label for="sidebar-toggle" class="body-label"></label>

</body>

</html>