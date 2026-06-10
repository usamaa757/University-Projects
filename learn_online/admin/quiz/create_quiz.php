<?php
session_start();
if(!$_SESSION['email']){
    header("Location: ../../login/admin_login.php" );
}

include '../../db_connection.php';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Retrieve the course_id and course_name using INNER JOIN
    $stmt_course = $conn->prepare("SELECT *  FROM courses  WHERE course_id = ?");

    $stmt_course->bind_param("i", $course_id);
    $stmt_course->execute();
    $result = $stmt_course->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $course_id = $row['course_id'];
        $course_name = $row['course_name'];
    } else {
        // course not found
        $course_id = null;
        $course_name = "course Not Found";
    }
    $stmt_course->close();
} else {
    // No course_id provided in URL
    $course_id = null;
    $course_name = "No course ID Provided";
}

// Close the statement

// Initialize variables
$resultMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_id = $_GET['course_id']; // Assuming you are passing course_id via URL parameter
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    

    // Prepare insert statement for quiz
    $insert_quiz = $conn->prepare("INSERT INTO quizzes (course_id, course_name, start_date, end_date) VALUES  (?, ?, ?, ?)");
    $insert_quiz->bind_param("isss", $course_id, $course_name, $start_date, $end_date);
    $insert_quiz->execute();
    $resultMsg  = "Quiz created successfully";

    // Get the inserted quiz_id
    $quiz_id = $insert_quiz->insert_id;
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
    <title>Create Quiz</title>
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
        <a href="../admin_dashboard.php"> <button>
                       Back

                    </button></a>
            <div class="class-container">
                <div style="width: auto;">
                    <h3><?php echo $course_name; ?></h3>
                </div>
                <div style="padding: 15px 5px;">
                    <div class="result-output">
                        <?php
                        if (!empty($errorMsg)) {
                            echo "<span id = 'error'>$errorMsg</span>";
                        } elseif (!empty($resultMsg)) {
                            echo "<span id ='result'>$resultMsg </span>";
                        }
                        ?>
                    </div>
                    <h2>Create Quiz</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?course_id=' . $_GET['course_id']; ?>" method="post">

                        <input type="hidden" id="<?php echo $course_id; ?>" name="course_id"><br>
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date">
                        <div class="submit-btn">
                            <button type="submit" name="submit">Create</button>
                        </div>
                    </form>

                    <h2>Add Questions</h2>
                    <?php
                  
                    // Retrieve all quizzes from the database
                    // Retrieve quizzes based on course ID
                    $course_id = $_GET['course_id']; // Assuming you are passing course_id via URL parameter
                    $sql = "SELECT * FROM quizzes WHERE course_id = $course_id ORDER BY quiz_id";
                    $result = $conn->query($sql);

                    // Display a form for adding questions for each quiz
                    if ($result->num_rows > 0) {
                        $sn = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo '<a href="add_question.php?quiz_id=' . $row['quiz_id'] .'">Quiz ' .    $sn++ . '</a><br>';
                        }
                    } else {
                        echo "No quizzes found for this course.";
                    }

                    ?>
                  
                </div>
            </div>
        </main>
    </div>
    <label for="sidebar-toggle" class="body-label"></label>
</body>

</html>