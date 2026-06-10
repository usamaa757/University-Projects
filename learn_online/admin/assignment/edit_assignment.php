<?php
session_start();
include 'fetch_assignment_process.php';
include 'update_assignment.php';
include '../../db_connection.php';
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
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
    <title>Edit Assignment</title>
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
            <img src="<?php echo htmlspecialchars($admin_picture); ?>" alt="Profile Picture">

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
                    <li> <a href="assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="../quiz/quiz_list.php"> Quiz Management </a> </li>
                    <li> <a href="../lesson/lesson_record.php"> Lesson Management </a> </li>
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


        <a href="assignment_record.php"> <button>
                        Back

                    </button></a>
                    <br>
            <div class="assignment-container">
                <div class="heading">
                    <h3>Update Assignment</h3>

                </div>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?assignment_id=<?php echo $assignment_id; ?>" method="POST">
                <div class="result-output">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<span id='error'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span id='result'>$resultMsg</span>";
                        }
                        ?>
                    </div>

                    <div style="padding: 10px;">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <label for="assignment_question">
                            <h4>Assignment Question:</h4>
                        </label>
                        <textarea name="assignment_question" id="assignment_question" style="margin: 10px 0;" cols="70" rows="10" required><?php echo $assignment_question; ?></textarea><br>
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