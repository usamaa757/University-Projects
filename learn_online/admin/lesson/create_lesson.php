<?php
session_start();
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}

include 'create_lesson_process.php';
// Include the database connection script
include '../../db_connection.php';



$admin_name = $_SESSION['name'];
$admin_email = $_SESSION['email'];
$admin_picture = $_SESSION['profile_pic'];

$baseUrl = 'http://localhost/learn_online/'; 
$imagePath = $baseUrl . str_replace('../', '', htmlspecialchars($admin_picture));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Lesson</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../../assets/fontawesome/web-fonts-with-css/css/fontawesome.min.css">
</head>

<body>
    <input type="checkbox" name="" id="sidebar-toggle">
    <div class="sidebar">

        <div class="sidebar-main">
            <div class="sidebar-user">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile Picture">

                <div>
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <span><?php echo htmlspecialchars($admin_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="../admin_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                <li> <a href="../admin_profile.php">Profile</a> </li>
                    <li> <a href="../course/course_management.php">Course Management</a> </li>
                    <li> <a href="../assignment/assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="../quiz/quiz_list.php"> Quiz Management </a> </li>
                    <li> <a href="lesson_record.php"> Lesson Management </a> </li>
                    <li> <a href="../admin_registration.php"> Admin Registration </a> </li>
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


                <a href="../../login/logout.php"> <button>
                        <!-- <span class="las la-file-export"></span> -->
                        <span class="fa fa-power-off"></span>

                    </button></a>

            </div>
        </header>

        <main>

           
        <a href="../admin_dashboard.php"> <button>
                       Back

                    </button></a>
            <div class="lecture-container">
                <div>
                    <h3><?php  echo $course_name ?></h3>

                </div>
                <div>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?course_id=' . $course_id; ?>" method="post" enctype="multipart/form-data" class="lecture-form">
        <div class="result-output">
            <?php
            if (!empty($errorMsg)) {
                echo "<span id='error'>$errorMsg</span>";
            } elseif (!empty($resultMsg)) {
                echo "<span id='result'>$resultMsg</span>";
            }
            ?>
        </div>

        <input type="hidden" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>">
        <input type="hidden" id="course_name" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">

        <label for="date">From</label>
        <input type="date" id="start_date" name="start_date" required>
        <label for="date">To</label>
        <input type="date" id="end_date" name="end_date" required><br>

        <label for="title">Lesson Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="file">Upload Video:</label>
        <input type="file" id="lesson_file" name="lesson_file" accept=".mp4, .docx, .doc, .pdf" required style="border: none;"><br>

        <button type="submit">Upload Lesson</button>
    </form>




                </div>
            </div>





        </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>