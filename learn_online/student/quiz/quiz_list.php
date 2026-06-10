<?php
session_start();

if (!isset($_SESSION['student_email'])) {

    header("Location: ../../login/teacher_login.php");
  
    exit();
}
$course_id = $_GET['course_id'];

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
                    <br><br>


            <div id="container">
                <div>
                    <h3>Quiz</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Quiz No</th>
                                <th>Subject Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Submit Status</th>
                                <th>Quiz Status</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../../db_connection.php';

                            if (isset($_GET['course_id'])) {
                                $course_id = $_GET['course_id'];

                                // Query to fetch quizzes, submission status, and total marks
                                $sql = "
                    SELECT 
                        q.*, 
                        sq.marks,
                        sq.status AS sq_status, 
                        sq.student_quiz_id,
                        (SELECT COUNT(*) FROM questions WHERE questions.quiz_id = q.quiz_id) AS total_marks
                    FROM 
                        quizzes q 
                    LEFT JOIN 
                        student_quiz sq 
                    ON 
                        q.quiz_id = sq.quiz_id AND sq.student_id = ?
                    WHERE 
                        q.course_id IN (
                            SELECT cs.course_id 
                            FROM courses cs 
                            WHERE cs.course_id = ?
                        ) 
                    ORDER BY 
                        q.quiz_id";

                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ii", $_SESSION['student_id'], $course_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

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
                                        echo "<td>" . $row['course_name'] . "</td>";
                                        echo "<td>" . $row['start_date'] . "</td>";
                                        echo "<td>" . $row['end_date'] . "</td>";
                                        echo "<td>";
                                
                                        // Logic for submission status
                                        if (isset($row['marks'])) {
                                            echo "<p style='color:green;'>Submitted</p>";
                                        } elseif ($is_open) {
                                            echo "<a style='color:blue;' href='submit_quiz.php?quiz_id=" . $row['quiz_id'] . "'>Take Quiz</a>";
                                        } else {
                                            echo "<p style='color:red;'>Not Submitted</p>";
                                        }
                                
                                        echo "</td>";
                                
                                        // Logic for quiz status
                                        if ($is_open) {
                                            echo "<td><p style='color:green;'>Open</p></td>";
                                        } elseif ($is_expired) {
                                            echo "<td style='color:red;'>Closed</td>";
                                        } elseif ($is_before_start) {
                                            echo "<td style='color:orange;'>Not Started</td>";
                                        }
                                
                                        // Check if marks are available
                                        if (isset($row['marks'])) {
                                            echo "<td>" . $row['marks'] . " / " . $row['total_marks'] . "</td>";
                                        } else {
                                            echo "<td style='color:red;'></td>";
                                        }
                                
                                        echo "</tr>";
                                    }
                                }
                                } else {
                                    echo "<tr><td colspan='7'>No quizzes found for this subject.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>


    </div>
    </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>