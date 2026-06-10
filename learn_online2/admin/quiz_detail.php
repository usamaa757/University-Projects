<?php
// Include your database connection file
include '../../db_connection.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../../login/admin_login.php");
    exit();
}


if (isset($_GET['quiz_id']) && isset($_GET['student_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $student_id = $_GET['student_id'];

    // Fetch quiz questions, options, and selected answers for the student
    $fetch_quiz_query = "
        SELECT q.question_id, q.question_text, o.option_id, o.option_text, qa.selected_option_id
        FROM questions q
        JOIN options o ON q.question_id = o.question_id
        LEFT JOIN student_quiz_answers qa ON q.question_id = qa.question_id AND qa.student_quiz_id = (
            SELECT student_quiz_id
            FROM student_quiz
            WHERE quiz_id = $quiz_id AND student_id = $student_id
        )
        WHERE q.quiz_id = $quiz_id
    ";

    $result_quiz = mysqli_query($conn, $fetch_quiz_query);
    if ($result_quiz) {
        $quiz_details = mysqli_fetch_assoc($result_quiz);
    }

} else {
    echo "Quiz ID not provided.";
    exit();
}


$admin_name = $_SESSION['name'];
$admin_email = $_SESSION['email'];
$admin_picture = $_SESSION['profile_pic'];
$baseUrl = 'http://localhost/learn_online/'; 
$imagePath = $baseUrl . str_replace('../', '', htmlspecialchars($admin_picture));
?>
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
            <a href="view_quiz.php"> <button>
                    Back

                </button></a>
            <br><br>
            <?php

            ?>
            <div id="container">
                <div>
                    <h3>Quiz's Details</h3>
                </div>
                <div class="table-responsive">
                    <table width="100%">
                        <thead>
                            <tr>

                                <th>S No</th>
                                <th>Question</th>
                                <th>Selected Option</th>
                                <th>Correct/Incorrect</th>

                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $sno = 1;
                            $last_question_id = null;
                            while ($row = mysqli_fetch_assoc($result_quiz)) {
                                if ($last_question_id != $row['question_id']) {
                                    if ($last_question_id !== null) {
                                        echo "</td><td></td></tr>"; // Close previous row
                                    }
                                    echo "<tr><td>" . $sno++ . "</td><td>";
                                    echo "<td>" . $row['question_text'] . "</td><td>"; 
                                  
                                    $last_question_id = $row['question_id'];
                                } else {
                                    echo "<br>"; // Add line break for multiple options
                                }
                                $selected_option = $row['selected_option_id'] == $row['option_id'] ? 'Selected' : '';
                                echo $row['option_text'] . " " . $selected_option;
                            }
                            echo "</td><td></td></tr>"; // Close the last row
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