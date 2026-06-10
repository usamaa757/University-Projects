<?php
session_start();

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
    <title>Lesson Record</title>
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



            <div id="container">
                <div>
                    <h3>Lesson's Record</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>

                            <tr>
                                <th>S No</th>
                             
                                <th>Course Name</th>

                                <th colspan="3">Action</th>


                            </tr>
                        </thead>

                        <tbody>

                            <?php


                            include "../../db_connection.php";

                            $query = "SELECT DISTINCT lessons.lesson_id, lessons.course_id, courses.course_name 
                                      FROM lessons 
                                      INNER JOIN courses ON lessons.course_id = courses.course_id";

                            $result = mysqli_query($conn, $query);

                            if ($result) {
                                $fetch_lesson = array();
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $fetch_lesson[] = $row;
                                }

                                mysqli_free_result($result);
                            } else {
                                echo "Error: " . mysqli_error($conn);
                            }

                            mysqli_close($conn);

                            if ($fetch_lesson) {
                                $sNo = 1;
                                foreach ($fetch_lesson as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $sNo++ . "</td>";
                                 
                                    echo "<td>" . $row['course_name'] . "</td>";
                                   
                           
                            
                           
                         
                            echo "<td 'width:50px;'>";

                            echo "<button style = 'color:white; background-color:green; border:none;' onclick=\"window.location.href='lesson_list.php?course_id=" . $row['course_id'] . "'\">View Lesson List</button> ";

                            echo "</td>";
                            echo "</tr>";

                        }
                    }
                    else {
                        echo "No lesson recorded";
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