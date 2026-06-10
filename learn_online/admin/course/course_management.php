<?php session_start();

//  include 'delete_student.php';

include 'add_course_process.php';
// include 'class_filter.php';

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
    <title>Course Management</title>
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
                <ul>
                <li> <a href="../admin_profile.php">Profile</a> </li>
                    <li> <a href="course_management.php">Course Management</a> </li>
                    <li> <a href="../assignment/assignment_record.php"> Assignment Management</a> </li>
                    <li> <a href="../quiz/quiz_list.php"> Quiz Management </a> </li>
                    <li> <a href="../lesson/lesson_record.php"> Lesson Management </a> </li>
                    <li> <a href="../admin_registration.php"> Admin Registration </a> </li>
                   
                </ul>
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

                <a href="../../login/logout.php">
                    <button>
                        <span class="fa fa-power-off"></span>
                    </button>
                </a>
            </div>
        </header>

        <main>

            <br>
            <div class="class-container">
                <div>
                    <h3>Add New Course</h3>
                </div>
                <?php 
                 if (isset($_GET['resultMsg'])) {
                    $result= $_GET['resultMsg'];
                } elseif (isset($_GET['errorMsg'])) {
                    $error= $_GET['errorMsg'];
                }
                if (!empty($error) ) {
                    echo "<span id='error'>$error</span>";
                } elseif (!empty($result)) {
                    echo "<span id='result'>$result</span>";
                } ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="class-form">

                    <label for="course_name">Course Name:</label>
                    <input type="text" name="course_name" id="course_name" required>
                    <button style="margin-top: 10px;" type="submit" name="add_course">Add Course</button>
                </form>
            </div>
            <div class="container">
                <div>
                    <h3>Course Management</h3>
                </div>
                <?php
                include '../../fetch_table_data.php';
                $courses = fetchTableData('courses');
                if (!empty($courses)) {

                ?>

                    <div class="table-responsive">
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
                        <table width="100%">
                            <thead>
                                <tr>

                                    <th>S No</th>
                                    <th>course ID</th>
                                    <th>Course Name</th>


                                    <th colspan="2">Action</th>


                                </tr>
                            </thead>
                            <tbody>


                                <?php
                                foreach ($courses as $key => $course) {
                                    echo "<tr>";
                                    echo "<form method='POST' action='update_course.php'>";
                                    echo "<td>" . ($key + 1) . "</td>";
                                    echo "<td>" . $course["course_id"] . "</td>";
                                    echo "<td><input type='text' name='course_name' value='" . htmlspecialchars($course['course_name'], ENT_QUOTES) . "'></td>";
                                    echo "<td style='width:50px;'>";
                                    echo "<input type='hidden' name='course_id' value='" . $course['course_id'] . "'>";
                                    echo "<button type='submit'>Update</button>";
                                    echo "</td>";
                                    echo "</form>";  // Close update form
                                    echo "<form method='POST' action='delete_course.php' onsubmit='return confirm(\"Do you want to delete this course?\");'>";
                                    echo "<td style='width:50px;'>";
                                    echo "<input type='hidden' name='course_id' value='" . $course['course_id'] . "'>";
                                    echo "<button type='submit' style='color:white; background-color:red; border:none;'>Delete</button>";
                                    echo "</td>";
                                    echo "</form>";  // Close delete form
                                    echo "</tr>";
                                }



                                ?>

                            </tbody>
                        </table>


                    <?php
                } else {

                    echo "No course found.";
                }
                    ?>

                    </div>
            </div>


          
        </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>