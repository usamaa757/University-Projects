<?php
session_start();
include '../fetch_table_data.php';


// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: ../login/admin_login.php");
    exit();
}



$admin_name = $_SESSION['name'];
$admin_email = $_SESSION['email'];
$admin_picture = $_SESSION['profile_pic'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <img src="<?php echo htmlspecialchars($admin_picture); ?>" alt="Profile Picture">
                <div>
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <span><?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="admin_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="quiz/quiz_list.php"> Quiz Management </a> </li>
                   
                    <li> <a href="registration_request.php"> Student Management </a> </li>
                    <li> <a href="student_list.php">Student Profiles Management </a> </li>

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


                <a href="../login/logout.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        <span class="fa fa-power-off"></span>

                    </button></a>


            </div>
        </header>


        <main>

            <div>
                <div class="subject-container">

                    <div class="box">
                        <h3>Admin Dashboard</h3>
                        <div class="inline">
                            <a href="quiz/create_quiz.php">
                                <img id="quiz" src="../assets/img/quiz.png" alt="">
                            </a>
                            <div>Quiz</div>
                        </div>
                        <div class="inline">
                            <a href="lesson/create_lesson.php">
                                <img id="lesson" src="../assets/img/book.png" alt="">
                            </a>
                            <div>Lesson</div>
                        </div>
                    </div>

                </div>



            </div>




        </main>
    </div>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>