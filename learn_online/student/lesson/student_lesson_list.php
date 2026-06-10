<?php
session_start();


if (!isset($_SESSION['student_email'])) {

    header("Location: ../../login/student_login.php");
    exit();
}

include '../../db_connection.php';
$lessons = array();
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    $sql = "SELECT course_name FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($course = $result->fetch_assoc()) {
        $course_name = $course['course_name'];
    }
    // Retrieve lesson details for the student from the database
    $sql = "SELECT * FROM lessons WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);

    // Check if lessons are found for the student
    if (mysqli_num_rows($result) > 0) {
        while ($lesson = mysqli_fetch_assoc($result)) {
            $lessons[] = $lesson;




            // Construct the URL to the video file
            $video_url = "../../admin/lesson/videos/" . $lesson['file_name']; // Adjust the directory name if necessary

        }
    }
}

$student_name = $_SESSION['student_name'];
$student_email = $_SESSION['student_email'];
$student_picture = $_SESSION['profile_pic'];
$baseUrl = 'http://localhost/learn_online/';
$imagePath = $baseUrl . str_replace('../', '', htmlspecialchars($student_picture));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson List</title>
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
                    <h3><?php echo htmlspecialchars($student_name); ?></h3>
                    <span><?php echo htmlspecialchars($student_email); ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="../student_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                    <li> <a href="../student_profile.php">Student Profile</a> </li>

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

            <a href="../student_dashboard.php"> <button>
                    Back

                </button></a>



            <div>
                <div class="lecture-container">
                    <div>
                        <?php
                        if (!empty($course_name)) : ?>
                            <h3> <?php echo $course_name; ?></h3>
                        <?php endif ?>
                    </div>
                    <div>

                        <?php

                        if (!empty($lessons)) {
                            echo "<table width='100%'>";
                            echo "<tr>

                          <th>S No</th>
                          <th>Title</th>
                          <th>Start Date</th>
                          <th>End Date</th>
                          
                          </tr>";
                            $sno = 1;
                            $lesson_no = 1;
                            
                            foreach ($lessons as $lesson) {

                                $lesson_title = $lesson['title']; // Assuming 'title' is the correct key
                                $start_date = $lesson['start_date'];
                                $end_date = $lesson['end_date'];
                                $lesson_id = $lesson['lesson_id']; // Fetch lesson_id from the $lesson array
                                echo "<tr>";
                                echo "<td>";
                                echo $sno++;
                                echo  "</td>";
                                echo "<td>";
                              
                                echo "<a href='view_lesson.php?course_id=$course_id&lesson_id=$lesson_id'>$lesson_title</a>";

                                echo "</td>";
                                echo "<td>$start_date</td>";
                                echo "<td>$end_date</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No lessons found for the specified subject.";
                        }
                        ?>

                    </div>

                </div>


            </div>



    </div>

    </main>
    </div>

    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>