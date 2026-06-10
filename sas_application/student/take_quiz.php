<?php
session_start();
include '../other/db_connection.php'; // Include database connection

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];
$quiz_id = $_GET['quiz_id'];

if (empty($quiz_id)) {
    die('Invalid quiz ID.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];

    // Prepare to insert quiz results
    $stmt_answer = $conn->prepare("INSERT INTO student_answers (student_id, quiz_id, question_id, option_id) VALUES (?, ?, ?, ?)");

    foreach ($_POST['answers'] as $question_id => $option_id) {
        $stmt_answer->bind_param("iiii", $student_id, $quiz_id, $question_id, $option_id);
        $stmt_answer->execute();
    }

    // Redirect with success message
    header("Location: submitted.php?quiz_id=$quiz_id&msg=Quiz+submitted+successfully");
    exit();
}

// Fetch quiz details
$query_quiz = "SELECT * FROM quizzes WHERE quiz_id = ?";
$stmt_quiz = $conn->prepare($query_quiz);
$stmt_quiz->bind_param('i', $quiz_id);
$stmt_quiz->execute();
$result_quiz = $stmt_quiz->get_result();

if ($result_quiz->num_rows == 0) {
    die('Quiz not found.');
}

$quiz = $result_quiz->fetch_assoc();

// Fetch questions and options for the quiz
$query_questions = "SELECT q.question_id, q.question_text, o.option_id, o.answer_text
    FROM questions q
    INNER JOIN options o ON q.question_id = o.question_id
    WHERE q.quiz_id = ?";
$stmt_questions = $conn->prepare($query_questions);
$stmt_questions->bind_param('i', $quiz_id);
$stmt_questions->execute();
$result_questions = $stmt_questions->get_result();

$questions = [];
while ($row = $result_questions->fetch_assoc()) {
    $question_id = $row['question_id'];
    if (!isset($questions[$question_id])) {
        $questions[$question_id] = [
            'question_text' => $row['question_text'],
            'options' => []
        ];
    }
    $questions[$question_id]['options'][] = [
        'option_id' => $row['option_id'],
        'answer_text' => $row['answer_text']
    ];
}

$stmt_quiz->close();
$stmt_questions->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/home.css">

</head>

<body>

    <!-- Top bar -->
    <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school fa-1x"></i>School Automation System </div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="#" onclick="toggleSubmenu('courses')"><i class="fas fa-chalkboard-teacher"></i> Courses <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="courses_submenu">
                    <li><a href="display_course.php"><i class="fas fa-eye"></i> View Courses</a></li>
                    <li><a href="course_selection.php"><i class="fas fa-edit"></i> Course Selection</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="toggleSubmenu('lecture')"><i class="fas fa-chalkboard-teacher"></i> Lectures<span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="lecture_submenu">
                    <li><a href="view_lecture.php"><i class="fas fa-eye"></i> View lecture</a></li>
                    <li><a href="course_selection.php"><i class="fas fa-edit"></i> Course Selection</a></li>
                </ul>
            </li>
            <li><a href="view_voucher.php"><i class="fas fa-money-bill"></i> Fees</a></li>
            <li><a href="submit_assignment.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="view_quiz.php"><i class="fas fa-pen"></i> Quizzes</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-graduation-cap"></i> attendance </a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Results</a></li>
            <li><a href="../other/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>

    </div>


    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
    </script>
    <h1><?= htmlspecialchars($quiz['title']) ?></h1>
    <form action="take_quiz.php?quiz_id=<?= $quiz_id ?>" method="post">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        <?php foreach ($questions as $question_id => $question): ?>
            <fieldset>
                <legend><?= htmlspecialchars($question['question_text']) ?></legend>
                <?php foreach ($question['options'] as $option): ?>
                    <label>
                        <input type="radio" name="answers[<?= $question_id ?>]" value="<?= $option['option_id'] ?>">
                        <?= htmlspecialchars($option['answer_text']) ?>
                    </label>
                    <br>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
    </form>
    <div class="form-group">
        <button type="submit">Submit Quiz</button>
    </div>
</body>

</html>