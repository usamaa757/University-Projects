<?php
session_start();
include '../other/db_connection.php';

// Check if student ID is set in the session
$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    echo 'Student ID not set in session.';
    exit;
}
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];

// Fetch lectures for the selected courses
$query = "
    SELECT l.lecture_id, l.title, l.video_url, c.course_name
    FROM lectures l
    JOIN course_selection cs ON l.course_id = cs.course_id
    JOIN courses c ON l.course_id = c.course_id
    WHERE cs.student_id = ?
    ORDER BY l.lecture_id ASC;
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$lectures = [];
while ($row = $result->fetch_assoc()) {
    $lectures[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/form.css">

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


    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
    </script>
   
</head>
<body>
    <br><br>
    <div class="container">
        <h2>Lecture List</h2>
        <?php if (!empty($lectures)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Lecture ID</th>
                        <th>Title</th>
                        <th>Course Name</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lectures as $lecture) : ?>
                        <tr>
                            <td><?= htmlspecialchars($lecture['lecture_id']) ?></td>
                            <td><?= htmlspecialchars($lecture['title']) ?></td>
                            <td><?= htmlspecialchars($lecture['course_name']) ?></td>
                            <td><a href="<?= htmlspecialchars(($lecture['video_url'])) ?>" target="_blank">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No lectures found for the selected courses.</p>
        <?php endif; ?>
    </div>
</body>
</html>
