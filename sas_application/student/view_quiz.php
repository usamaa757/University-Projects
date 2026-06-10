<?php
session_start();
include '../other/db_connection.php'; // Include database connection

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];

// Fetch quizzes and check if they have been submitted by the student
$query = "SELECT q.quiz_id, q.title, q.start_date, q.end_date,
                 COALESCE(SUM(CASE WHEN o.is_correct = 1 AND sa.option_id = o.option_id THEN 1 ELSE 0 END), 0) AS total_marks,
                 CASE WHEN sa.status = 'submitted' THEN 'submitted' ELSE 'take' END AS action
          FROM quizzes q
          LEFT JOIN student_answers sa ON q.quiz_id = sa.quiz_id AND sa.student_id = ?
          LEFT JOIN options o ON sa.option_id = o.option_id AND o.question_id = (SELECT question_id FROM questions WHERE quiz_id = q.quiz_id LIMIT 1)
          GROUP BY q.quiz_id, q.title, q.start_date, q.end_date, sa.status
          ORDER BY q.start_date ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$quizzes = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System</title>
    <link rel="stylesheet" href="../css/dashboard.css">

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
        <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="student_dashboard.php"><i class="fas fa-user"></i> Home</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="view_lecture.php"><i class="fas fa-chalkboard-teacher"></i> Lectures</a>
            </li>
            <li><a href="view_voucher.php"><i class="fas fa-money-bill"></i> Fees</a></li>
            <li><a href="view_assignments.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="view_quiz.php"><i class="fas fa-pen"></i> Quizzes</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-graduation-cap"></i> attendance </a></li>
            <li><a href="../other/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>

    </div>


   
    <h1>Available Quizzes</h1>
    
    <?php if (count($quizzes) > 0): ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Your Marks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizzes as $quiz): ?>
                    <tr>
                        <td><?= htmlspecialchars($quiz['title']) ?></td>
                        <td><?= htmlspecialchars($quiz['start_date']) ?></td>
                        <td><?= htmlspecialchars($quiz['end_date']) ?></td>
                        <td><?= htmlspecialchars($quiz['total_marks']) ?></td>
                        <td>
                            <?php if ($quiz['action'] === 'submitted'): ?>
                                Submitted
                            <?php else: ?>
                                <a href="take_quiz.php?quiz_id=<?= $quiz['quiz_id'] ?>">Take Quiz</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No quizzes are available at the moment.</p>
    <?php endif; ?>
</body>
</html>
