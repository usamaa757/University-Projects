<?php
session_start();
// include "class_filter.php";

// $fetch_student = classFilter('student_subjects');
if (!isset($_SESSION['student_email'])) {
    header("Location: ../login/student_login.php");
    exit();
}
include 'subject_fetch_process.php';
include 'course_selection_process.php';
include '../common_process/db_connection.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Selection</title>
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
                <img src="../assets/img/logo.png" alt="">
                <div>
                    <h3><?php echo $_SESSION['student_name']; ?></h3>
                    <span><?php echo $_SESSION['student_email']; ?></span>


                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <!-- <a href="../students/student_panel.php">
                        <h2>Dashboard</h2>
                    </a> -->
                </div>
                <!-- <ul>
                <li> <a href="../students/student_profile.php">Student Profile</a> </li>
                    <li> <a href="../students/fee_voucher.php"> Fee Voucher</a> </li>
                    <li> <a href="../students/attendance_status.php">Attendance Status </a> </li>
                    <li> <a href=""> Help </a> </li> -->
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

            <div class="page-header">
                <div>
                    <h1>School Automation System</h1>
                </div>

            </div>
<br>
            <div class="form-container">
                <div class="headding">
                    <h3> <?php echo $class_name;  ?></h3>

                </div>
                <div class="form">
                    <div class="result-output">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<span id = 'error'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span id ='result'>$resultMsg </span>";
                        }
                        ?>
                    </div>
                    <form method='POST' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                      

                        <select name="selected_subject[]" id="selected_subject" multiple>
                            <?php foreach ($subjects as $subject) : ?>


                                <?php if (isset($subject['subject_id']) && isset($subject['subject_name'])) : ?>
                                    <option value="<?= $subject['subject_id']; ?>"><?= $subject['subject_name']; ?></option>

                                <?php endif; ?>

                            <?php endforeach; ?>
                        </select>
                        <?php foreach ($subjects as $subject) : ?>
                            <?php if (isset($subject['subject_id']) && isset($subject['subject_name'])) : ?>
                                <input type="hidden" name="subject_name[]" value="<?php echo htmlspecialchars($subject['subject_name']); ?>">
                          
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="submit-btn">
                            <button type="submit">Submit</button>
                        </div>
                    </form>


                </div>
            </div>
    </div>
    </main>
    </div>



</body>

</html>