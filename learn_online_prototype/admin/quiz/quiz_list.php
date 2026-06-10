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
    <title>Quiz List</title>
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
                <li> <a href="quiz_list.php"> Quiz Management </a> </li>
                    
                    <li> <a href="../registration_request.php"> Student Management </a> </li>
                    <li> <a href="../student_list.php">Student Profiles Management </a> </li>
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


        <a href="../admin_dashboard.php"> <button>
                    Back

                </button></a>
            <br><br>

            <div id="container">
                <div>
                    <h3>Quiz's Record</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>

                            <tr>
                                <th>S No</th>
                                <th>Quiz ID</th>
                                <th>Start Date</th>
                                <th>Ednd Date</th>
                                <th>Status</th>


                                <th style="text-align: center;" colspan="3">Action</th>


                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            include "../../fetch_table_data.php";
                            include "../../db_connection.php";



                            // Fetching students data from database
                            $fetch_quiz = fetchTableData('quizzes');
                            if ($fetch_quiz) {
                                $sNo = 1;
                                foreach ($fetch_quiz as $row) {
                                    $quiz_taken = false;
                                    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM student_quiz WHERE quiz_id = ?");
                                    $check_stmt->bind_param("i", $row['quiz_id']);
                                    $check_stmt->execute();
                                    $check_stmt->bind_result($count);
                                    $check_stmt->fetch();
                                    if ($count > 0) {
                                        $quiz_taken = true;
                                    }
                                    $check_stmt->close();
                            
                                    $current_date = date('Y-m-d');
                                    $end_date = $row['end_date'];
                                    $is_expired = $current_date > $end_date;
                           
                                    echo "<tr>";
                                    echo "<td>" . $sNo++ . "</td>";
                                    echo "<td>" . $row['quiz_id'] . "</td>";
                                 
                                    echo "<td>" . $row['start_date'] . "</td>";
                                  
                                    echo "<td>" . $row['end_date'] . "</td>";
                                  
                                    if ($is_expired) {
                                    echo "<td style ='color:red;'>  Closed </td>";

                                        echo "<td>";
                                        echo "<button onclick=\"window.location.href='edit_quiz.php?quiz_id=" . $row['quiz_id'] . "'\" disabled>Edit</button>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<button style='color:white; background-color:red; border:none;' onclick=\"if(confirm('Do you want to delete this quiz?')) { window.location.href='delete_quiz.php?quiz_id=" . $row['quiz_id'] . "'; }\" disabled>Delete</button>";
                                        echo "</td>";
                                    } else {
                                        echo "<td style ='color:green;'>  Open </td>";
                                        echo "<td>";
                                        echo "<button onclick=\"window.location.href='edit_quiz.php?quiz_id=" . $row['quiz_id'] . "'\">Edit</button>";
                                        echo "</td>";
                                        echo "<td>";
                                        echo "<button style='color:white; background-color:red; border:none;' onclick=\"if(confirm('Do you want to delete this quiz?')) { window.location.href='delete_quiz.php?quiz_id=" . $row['quiz_id'] . "'; }\">Delete</button>";
                                        echo "</td>";
                                    }
                                    echo "<td>";
                                    echo "<button style='color:white; background-color:green; border:none;' onclick=\"window.location.href='view_quiz.php?quiz_id=" . $row['quiz_id'] . "'\">View Details</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
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