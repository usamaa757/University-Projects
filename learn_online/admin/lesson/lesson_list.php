<?php
// Include your database connection file
include '../../db_connection.php';
session_start();
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}
if(isset($_GET['course_id'])){
    $course_id = $_GET["course_id"];
    $courses = array();

    // Fetch student course for marking
    $fetch_course_query = "SELECT lessons.*, courses.course_name 
                           FROM lessons 
                           INNER JOIN courses ON lessons.course_id = courses.course_id
                           WHERE lessons.course_id = ?";
    $stmt = $conn->prepare($fetch_course_query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $course_result = $stmt->get_result();

    if ($course_result->num_rows > 0) {
        // Output the course for marking
        while ($row = $course_result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
}
 
$stmt->close();

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
        <a href="lesson_record.php"> <button>
                        Back

                    </button></a>
                    <br><br>
            <?php
            if (!empty($courses)) {
            ?>
                <div id="container">
                    <div>
                        <h3><?php echo $courses[0]['course_name'];?></h3>
                    </div>
                    <div class="table-responsive">
                    <div class="result-output">
                        <?php
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
                                    <th>Lesson ID</th>
                                    <th>Lesson Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>

                                    <th colspan="2">Action</th>
                                 

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($courses as $key => $course) {
                                    echo "<tr>";
                                    echo "<td>" . ($key + 1) . "</td>";
                                    echo "<td>" . $course["course_id"] . "</td>";
                                    echo "<td>" . $course["lesson_id"] . "</td>";
                                    echo "<td>" . $course["title"] . "</td>";
                                    echo "<td>" . $course["start_date"] . "</td>";
                                    echo "<td>" . $course["end_date"] . "</td>";
                                  
                                   

                                    echo "<td style = 'width:50px;'>";
                                    echo "<button onclick=\"window.location.href='edit_lesson.php?lesson_id=" . $course['lesson_id'] . "&course_id=" .$course['course_id']."'\" >Edit</button> ";
                                    echo "</td>";
        
        
                                        echo "<td style = 'width:50px;'>";
                                        echo "<button style='color:white; background-color:red; border:none;' onclick=\"if(confirm('Do you want to delete this lesson?')) { window.location.href='delete_lesson.php?lesson_id=" . $course['lesson_id']."'; }\">Delete</button>";
                                        echo "</td>";
                                      
                                    
                                 
                                    echo "</tr>";
                                }
                              
                                ?>

                            </tbody>
                        </table>
                    </div>

                <?php
            }else{

                 echo "Debug: No course submitted yet.";
            }
                ?>


                </div>
        </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>