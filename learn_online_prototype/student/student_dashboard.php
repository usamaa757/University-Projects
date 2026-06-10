<?php
session_start();
if (!isset($_SESSION['student_email'])) {

    header("Location: ../login/student_login.php");
    exit();
}
$student_name = $_SESSION['student_name'];
$student_email = $_SESSION['student_email'];
$student_picture = $_SESSION['profile_pic'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            <img src="<?php echo htmlspecialchars($student_picture); ?>" alt="Profile Picture">

                <div>
                    <h3><?php echo htmlspecialchars($student_name); ?></h3>
                    <span><?php echo htmlspecialchars($student_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="student_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
           
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

            <div class="page-header">
                <div>
                    <h1>
                        E - Learning
                    </h1>
                </div>

            </div>




            <div>
                <div class="subject-container">
                   
                        <div class="box">
                          <h3>Student Dashboard</h3>

        
                            <div class="inline">
                                <a href="quiz/quiz_list.php">
                                    <img id="quiz" src="../assets/img/quiz.png" alt="">
                                </a>
                                <div>Quiz</div>
                            </div>
                            <div class="inline">
                                <a href="lesson/student_lesson_list.php">
                                    <img id="lecture" src="../assets/img/book.png" alt="">
                                </a>
                                <div>Lesson</div>
                            </div>
                        </div>
                  
                </div>



            </div>



    </div>

    </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>