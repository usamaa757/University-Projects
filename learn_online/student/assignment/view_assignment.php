<?php
session_start();

if (!isset($_SESSION['student_email'])) {

    header("Location: ../../login/student_login.php");
    exit();
}
$subject_id = $_GET['subject_id'];



?>
<a href=""></a>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Record</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/form.css">
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
                <img src="../../assets/img/logo.png" alt="logo Image">

                <div>
                    <h3><?php echo $_SESSION['student_name']; ?></h3>
                    <span><?php echo $_SESSION['student_email']; ?></span>
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



            <div id="container">
                <div>
                    <h3>Assignments</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>

                            <tr>

                                <th>S No</th>
                                <th>Subject Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
                                <th>Submit Status</th>
                                <th>Marks</th>
                                <th>Assignment Status</th>



                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            include '../../db_connection.php';
                            if (isset($_GET['subject_id']) && isset($_SESSION['student_id'])) {
                                $subject_id = $_GET['subject_id'];
                                $student_id = $_SESSION['student_id'];
                            
                                // Fetch assignments
                                $stmt = $conn->prepare("SELECT * FROM assignments WHERE subject_id = ?");
                                $stmt->bind_param("i", $subject_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                            
                                // Fetch marks for the assignments
                                $stmt_marks = $conn->prepare("SELECT * FROM student_assignment WHERE student_id = ? AND subject_id = ?");
                                $stmt_marks->bind_param("ii", $student_id, $subject_id);
                                $stmt_marks->execute();
                                $result_marks = $stmt_marks->get_result();
                            
                                // Store marks in an associative array for quick lookup
                                $marks = [];
                                while ($row_marks = $result_marks->fetch_assoc()) {
                                    $marks[$row_marks['assignment_id']] = $row_marks;
                                }
                            
                                if ($result->num_rows > 0) {
                                    $sn = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        $start_date = $row['start_date'];
                                        $end_date = $row['end_date'];
                                        $current_date = date('Y-m-d');
                                        $is_open = ($start_date <= $current_date && $current_date <= $end_date);
                                        $is_before_start = ($current_date < $start_date);
                                        $is_expired = ($current_date > $end_date);
                            
                                        echo "<tr>";
                                        echo "<td>" . $sn++ . "</td>";
                                        echo "<td>" . $row['subject_name'] . "</td>";
                                        echo "<td>" . $row['start_date'] . "</td>";
                                        echo "<td>" . $row['end_date'] . "</td>";
                                        echo "<td>";
                                        if ($is_open) {
                                            echo "<a style='color:blue;' href='submit_assignment.php?assignment_id=" . $row['assignment_id'] . "&subject_id=" . $row['subject_id'] . "'>Submit Assignment</a>";
                                        }
                                        echo "</td>";
                            
                                        // Check if marks for this assignment are available
                                        if (isset($marks[$row['assignment_id']])) {
                                            $row_marks = $marks[$row['assignment_id']];
                                            if ($row_marks['status'] == 'Submitted') {
                                                echo "<td><p style='color:green;'>Submitted</p></td>";
                                            } else {
                                                echo "<td><p style='color:red;'>Not Submitted</p></td>";
                                            }
                            
                                            if ($row_marks['marks'] == 0 || $row_marks['marks'] == '') {
                                                echo "<td style='color:red;'>Not Marked</td>";
                                            } else {
                                                echo "<td>" . $row_marks['marks'] . "</td>";
                                            }
                                        } else {
                                            echo "<td><p style='color:red;'>Not Submitted</p></td>";
                                            echo "<td style='color:red;'>Not Marked</td>";
                                        }
                            
                                        // Logic for assignment status
                                        if ($is_open) {
                                            echo "<td><p style='color:green;'>Open</p></td>";
                                        } elseif ($is_expired) {
                                            echo "<td style='color:red;'>Closed</td>";
                                        } elseif ($is_before_start) {
                                            echo "<td style='color:orange;'>Not Started</td>";
                                        }
                            
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No Assignment found for this subject.</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>



            </div>
        </main>
    </div>
    <label for=" sidebar-toggle" class="body-label"></label>
</body>

</html>