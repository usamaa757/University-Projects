<?php
include 'student_registration_process.php';
include '../db_connection.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
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


                <a href="../login/student_login.php"> <button>

                        <span class="fa fa-user"></span> Login</button></a>

                </button></a>



            </div>
        </header>


        <main>

            <div class="page-header">
                <div>
                    <h1 class="main-heading">E - Learning</h1>
                </div>


            </div>

            <br>
            <div class="form-container">
                <div class="headding">
                    <h3 class="sub-heading">Student's Registration</h3>

                </div>
                <div class="form">

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" id="form">

                        <div class="result-output">
                            <?php
                            if (isset($errorMsg) && !empty($errorMsg)) {
                                echo "<span style='color:red;'>$errorMsg</span>";
                            } elseif (isset($resultMsg) && !empty($resultMsg)) {
                                echo "<span style='color:green;'>$resultMsg</span>";
                            }
                            ?>
                        </div>

                        <div class="reg-form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Name" required>
                        </div>

                        <div class="reg-form-group">
                            <label for="gender">Select Gender</label>
                            <select name="gender" id="gender" required>
                                <option value="selectGender">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="reg-form-group">
                            <label for="picture">Picture</label><br>
                            <input type="file" id="picture" name="picture" required style="border:none;">
                        </div>

                        <div class="reg-form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" maxlength="11" placeholder="0300-1234567" required>
                        </div>

                        <div class="reg-form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="abc@gmail.com" required>
                        </div>

                        <div class="reg-form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required placeholder="Password">
                        </div>

                        <div class="reg-form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">
                        </div>

                        <div class="submit-btn">
                            <button type="submit">Submit</button>
                        </div>
                    </form>

                </div>

            </div>

        </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>