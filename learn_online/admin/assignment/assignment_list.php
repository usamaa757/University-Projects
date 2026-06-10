<?php
// Include your database connection file
include '../../db_connection.php';
session_start();
if (!isset($_SESSION['email'])) {

    header("Location: ../../login/admin_login.php");
    exit();
}
if(isset($_GET['assignment_id'])){
    $assignment_id =$_GET['assignment_id'];
}
$assignments = array();


$fetch_assignments_query = "SELECT sa.*, c.course_name 
                            FROM student_assignment sa 
                            INNER JOIN courses c ON c.course_id = (
                                SELECT course_id
                                FROM student_assignment
                                WHERE assignment_id = ?
                            )
                            WHERE sa.assignment_id = ?";
$stmt = $conn->prepare($fetch_assignments_query);
$stmt->bind_param("ii", $assignment_id, $assignment_id);
$stmt->execute();
$assignments_result = $stmt->get_result();

if ($assignments_result->num_rows > 0) {
    // Output the assignments for marking
    while ($row = $assignments_result->fetch_assoc()) {
        $assignments[] = $row;
       
     
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
    <title>Assignment List</title>
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
                    <br><br>
            <?php
            if (!empty($assignments)) {
            ?>
                <div id="container">
                    <div>
                        <h3><?php echo $assignments[0]['course_name'];?></h3>
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
                                    <th>Assignment ID</th>
                                  
                                    <th>Student ID</th>

                                    <th>Action</th>
                                    <th>Marks</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                foreach ($assignments as $key => $assignment) {
                                    echo "<tr>";
                                    echo "<td>" . ($key + 1) . "</td>";
                                    echo "<td>" . $assignment["assignment_id"] . "</td>";
                                  
                                    echo "<td>" . $assignment["student_id"] . "</td>";
                                  
                                   

                                    $marks = $assignment["marks"];
                                    if (is_null($marks) || $marks === '' || $marks == 0) {
                                        echo "<td><a href='assignment_marking.php?assignment_id=" . $assignment['assignment_id'] . "&student_id=" .$assignment['student_id']."'>Add Marks</a></td>";
                                        echo "<td>Please add marks</td>";
                                    } else {
                                        echo "<td><a href='assignment_marking.php?assignment_id=" . $assignment['assignment_id'] . "&student_id=" .$assignment['student_id']."'>Add Marks</a></td>";
                                      
                                        echo "<td>" . $assignment['marks'] . "</td>";
                                    }

                                    echo "</tr>";
                                }
                              
                                ?>

                            </tbody>
                        </table>
                    </div>

                <?php
            }else{

                 echo "No assignments submitted yet.";
            }
                ?>


                </div>
        </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>