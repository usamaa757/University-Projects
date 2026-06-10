<?php 

session_start();

// Check if admin is logged in, if not, redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: ../login/admin_login.php");
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
    <title>Registration</title>
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
            <img src="<?php echo htmlspecialchars($admin_picture); ?>" alt="Profile Picture">
                <div>
                    <h3><?php echo $_SESSION['name']; ?></h3>
                    <span><?php echo $_SESSION['email']; ?></span>
                </div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-head">
                    <a href="admin_dashboard.php">
                        <h2>Dashboard</h2>
                    </a>
                </div>
                <ul>
                <li> <a href="quiz/quiz_list.php"> Quiz Management </a> </li>
                   
                    <li> <a href="registration_request.php"> Student Management </a> </li>
                    <li> <a href="student_list.php">Student Profiles Management </a> </li>

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

        

            <br>
        
                <div id="container">
                    <div>
                        <h3>Student Record</h3>
                    </div>
                    <div class="table-responsive">
                        <table width=100%;>
                        <div class="result-output">
                            <?php
                            if (isset($errorMsg) && !empty($errorMsg)) {
                                echo "<span style='color:red;'>$errorMsg</span>";
                            } elseif (isset($resultMsg) && !empty($resultMsg)) {
                                echo "<span style='color:green;'>$resultMsg</span>";
                            }
                            ?>
                        </div>
                            <thead id="table-heading">
                                <!-- <tr>
                                <th colspan="4">
                                    <h3 class="sub-heading">Student Record</h3>
                                </th>
                            </tr> -->
                                <tr>
                                    <th>S No</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th colspan="4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                include "pending_user.php";
                                if ($fetch_data) {
                                    $sNo = 1;
                                    foreach ($fetch_data as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $sNo++ . "</td>";
                                        echo "<td>" . $row['student_id'] . "</td>";
                                        echo "<td>" . $row['student_name'] . "</td>";
                                        echo "<td>";

                                        echo "<form method='POST' action='registration_request_process.php'>";
                                        echo "<input type='hidden' name='action' value='approve_student'>";
                                        echo "<input type='hidden' name='id' value='" . $row['student_id'] . "'>";
                                        echo "<button id='approve'  type='submit' name='approve_student_btn'>Approve</button>";
                                        echo "</form>";

                                        echo "</td>";
                                        echo "<td>";

                                        echo "<form method='POST' action='registration_request_process.php'>";
                                        echo "<input type='hidden' name='action' value='reject_student'>";
                                        echo "<input type='hidden' name='id' value='" . $row['student_id'] . "'>";
                                        echo "<button id='reject' type='submit' name='reject_student_btn'>Reject</button>";
                                        echo "</form>";

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