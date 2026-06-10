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
    <title>Assignment Record</title>
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



            <div id="container">
                <div>
                    <h3>Assignment's Record</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <?php
                            // Display error or result messages
                            $resultMsg = isset($_GET['resultMsg']) ? $_GET['resultMsg'] : '';
                            $errorMsg = isset($_GET['errorMsg']) ? $_GET['errorMsg'] : '';
                            ?>
                            <tr>
                                <th>S No</th>
                                <th>Assignment ID</th>
                                <th>Course Name</th>
                                <th>Submit Status</th>
                                <th>Assignment Status</th>
                                <th>End Date</th>
                                <th colspan="3" style="text-align: center;">Action</th>


                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            include "../../db_connection.php";

                            include "../../fetch_table_data.php";


                            // Fetching assignments with course name
                            $query = "
    SELECT a.assignment_id, a.end_date, sa.course_id, c.course_name
    FROM assignments a
    LEFT JOIN student_assignment sa ON a.assignment_id = sa.assignment_id
    LEFT JOIN courses c ON sa.course_id = c.course_id
    GROUP BY a.assignment_id";

                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                $sNo = 1;
                                while ($row = $result->fetch_assoc()) {
                                    $assignment_submit = false;

                                    // Check if the assignment is submitted
                                    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM student_assignment WHERE assignment_id = ?");
                                    $check_stmt->bind_param("i", $row['assignment_id']);
                                    $check_stmt->execute();
                                    $check_stmt->bind_result($count);
                                    $check_stmt->fetch();
                                    if ($count > 0) {
                                        $assignment_submit = true;
                                    }
                                    $check_stmt->close();

                                    echo "<tr>";
                                    echo "<td>" . $sNo++ . "</td>";
                                    echo "<td>" . $row['assignment_id'] . "</td>";
                                    echo "<td>" . $row['course_name'] . "</td>"; // Display the course name
                                    echo "<td>" . ($assignment_submit ? "Submitted" : "Not-Submitted") . "</td>";
                                    echo "<td>" . ($assignment_submit ? "Completed" : "Pending") . "</td>";
                                    echo "<td>" . $row['end_date'] . "</td>";

                                    if ($assignment_submit) {
                                        echo "<td>";
                                        echo "<button onclick=\"window.location.href='edit_assignment.php?assignment_id=" . $row['assignment_id'] . "'\" disabled>Edit</button> ";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<button style='color:white; background-color:red; border:none;' onclick=\"if(confirm('Do you want to delete this assignment?')) { window.location.href='delete_assignment.php?assignment_id=" . $row['assignment_id'] . "'; }\" disabled>Delete</button>";
                                        echo "</td>";
                                    } else {
                                        echo "<td>";
                                        echo "<button onclick=\"window.location.href='edit_assignment.php?assignment_id=" . $row['assignment_id'] . "'\" >Edit</button> ";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<button style='color:white; background-color:red; border:none;' onclick=\"if(confirm('Do you want to delete this assignment?')) { window.location.href='delete_assignment.php?assignment_id=" . $row['assignment_id'] . "'; }\">Delete</button>";
                                        echo "</td>";
                                    }
                                    echo "<td>";
                                    echo "<button style='color:white; background-color:green; border:none;' onclick=\"window.location.href='assignment_list.php?assignment_id=" . $row['assignment_id'] . "'\">View</button> ";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "No assignment found.";
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