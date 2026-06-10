<?php
// Include your database connection file
include '../../db_connection.php';
session_start();
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}
$quizzes = array();

// Fetch student quizzes for marking
$fetch_quizzes_query = "
    SELECT q.quiz_id, q.course_name, sq.marks, sq.student_id
    FROM quizzes q
    JOIN student_quiz sq ON q.quiz_id = sq.quiz_id
";

$result = mysqli_query($conn, $fetch_quizzes_query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
        $student_id = $row['student_id'];
        
    }

} else {
    echo  "Quiz is not submitted";
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
    <title>Quiz Detail</title>
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
                    <li> <a href="quiz_list.php"> Quiz Management </a> </li>
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
        <a href="quiz_list.php"> <button>
                        Back

                    </button></a>
                    <br><br>
            <?php
            
            ?>
                <div id="container">
                    <div>
                        <h3>Quiz's List</h3>
                    </div>
                    <div class="table-responsive">
                        <table width="100%">
                            <thead>
                                <tr>

                                    <th>S No</th>
                                    <th>Quiz ID</th>
                                    <th>Course Name</th>

                                    <th>Student ID</th>
                                    <th>Action</th>
                                    <th>Marks</th>

                                </tr>
                            </thead>
                            <tbody>
                            <?php
            foreach ($quizzes as $key => $quiz) {
                echo "<tr>";
                echo "<td>" . ($key + 1) . "</td>";
                echo "<td>" . $quiz["quiz_id"] . "</td>";
                echo "<td>" . $quiz["course_name"] . "</td>";
                echo "<td>" . $quiz["student_id"] . "</td>";

               
                echo "<td><a href='quiz_detail.php?quiz_id=" . $quiz['quiz_id'] . "&student_id=" . $student_id . "'>View Detail</a></td>";
                echo "<td>". $quiz['marks']. "</td>";
              
                       echo "</tr>";
            }
            ?>

                            </tbody>
                        </table>
                    </div>

          

                </div>
        </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>