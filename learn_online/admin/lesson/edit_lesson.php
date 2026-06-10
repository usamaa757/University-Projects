<?php
session_start();
include 'edit_lesson_process.php';
include 'update_lesson.php';
include '../../db_connection.php';
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}

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
    <title>Edit Lesson</title>
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


            <a href="lesson_list.php?course_id=<?php echo $course_id;?>"> <button>
                    Back

                </button></a>
            <br>
            <div class="assignment-container">
                <div class="heading">
                    <h3>Update Lesson</h3>

                </div>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?lesson_id=<?php echo $lesson_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="result-output">
                    <?php
                            if (isset($_GET['result'])) {
                                $resultMsg = $_GET['result'];
                            } elseif (isset($_GET['error'])) {
                                $errorMsg = $_GET['error'];
                            }
                            
                            if (!empty($errorMsg)) {
                                echo "<span id='error'>$errorMsg</span>";
                            } elseif (!empty($resultMsg)) {
                                echo "<span id='result'>$resultMsg</span>";
                            }
                            ?>
                    </div>

                    <div style="padding: 10px;">
                        <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                        <label for="current_file">Current Video:</label>
                        <div>

                            <?php if (!empty($video_url)) : ?>
                                <video width="560" height="315" controls>
                                    <source src="<?php echo $video_url; ?>" type="<?php echo $file_type; ?>">
                                    Your browser does not support the video tag.
                                </video><br>
                                <label for="title"><strong>Title:</strong></label>

                                <p><?php echo $title; ?></p>
                                <p>File Name: <?php echo $file_name; ?></p>
                            <?php else : ?>
                                <p>No video found for this lesson.</p>
                            <?php endif; ?>

                        </div>
                        <br><label for="lesson_file">New Video:</label>
                        <input type="file" name="lesson_file" id="lesson_file">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" required>
                        <label for="due_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" required>
                        <div class="submit-btn">
                            <button type="submit">Update</button>
                        </div>
                    </div>
                </form>
            </div>

    </div>

    </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>