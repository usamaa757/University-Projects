<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the teacher is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['teacher_name'];

// Fetch classes and courses for the logged-in teacher
$query = "SELECT DISTINCT tcc.class_id, tcc.course_id, c.course_name, cl.class_name
          FROM teacher_class_course tcc
          JOIN courses c ON tcc.course_id = c.course_id
          JOIN classes cl ON tcc.class_id = cl.class_id
          WHERE tcc.teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher_courses = $result->fetch_all(MYSQLI_ASSOC);

// Check if attendance has been marked for today
$today = date('Y-m-d');
$attendance_marked = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_course'])) {
    list($class_id, $course_id) = explode('-', $_POST['class_course']);
    $query = "SELECT COUNT(*) as count FROM attendance WHERE class_id = ? AND course_id = ? AND teacher_id = ? AND attendance_date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $class_id, $course_id, $teacher_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $attendance_marked = ($row['count'] > 0);
}

// Fetch students for the selected class and course
$students = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_course']) && !$attendance_marked) {
    list($class_id, $course_id) = explode('-', $_POST['class_course']);
    $query = "SELECT student_id, student_name FROM students WHERE class_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    
    // Handle attendance submission
    if (isset($_POST['attendance'])) {
        $attendance_date = $today;
        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, class_id, course_id, teacher_id, attendance_date, status) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
            $stmt->bind_param("iiisss", $student_id, $class_id, $course_id, $teacher_id, $attendance_date, $status);
            $stmt->execute();
        }
        echo "Attendance marked successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System - Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
     <!-- Top bar -->
     <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school fa-1x"></i> School Automation System</div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($teacher_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li>
                <a href="#" onclick="toggleSubmenu('attendance_submenu')"><i class="fas fa-check-circle"></i> Attendance <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="attendance_submenu">
                    <li><a href="../teacher/attendance.php"><i class="fas fa-edit"></i> Mark Attendance</a></li>
                </ul>
            </li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li>
                <a href="#" onclick="toggleSubmenu('assignment_submenu')"><i class="fas fa-upload"></i> Assignment <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="assignment_submenu">
                    <li><a href="../teacher/create_assignment.php"><i class="fas fa-upload"></i> Upload Assignment</a></li>
                    <li><a href="../teacher/view_assignment.php"><i class="fas fa-upload"></i> view Assignment</a></li>
                </ul>
            </li>
            <li>
                <a href="#" onclick="toggleSubmenu('quiz_submenu')"><i class="fas fa-upload"></i> quiz <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="quiz_submenu">
                    <li><a href="create_quiz.php"><i class="fas fa-upload"></i> Add Quiz</a></li>
                    <li><a href="create_quiz_questions.php"><i class="fas fa-upload"></i> Create Quiz</a></li>
                </ul>
            </li>
            <li>
                <a href="#" onclick="toggleSubmenu('lecture_submenu')"><i class="fas fa-chalkboard-teacher"></i> Lecture <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="lecture_submenu">
                <li><a href="../teacher/add_live_session.php"><i class="fas fa-calendar-alt"></i> Live Session</a></li>
                    <li><a href="../teacher/lecture.php"><i class="fas fa-calendar-alt"></i>Add_lecture</a></li>
                </ul>
            </li>
            <li>
                <a href="#" onclick="toggleSubmenu('result_submenu')"><i class="fas fa-clipboard-list"></i> Result <span class="arrow">&#9662;</span></a>
                <ul class="submenu" id="result_submenu">
                    <li><a href="upload_results.php"><i class="fas fa-upload"></i> Upload Results</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
// Function to toggle submenu
function toggleSubmenu(submenuId) {
            var submenu = document.getElementById(submenuId + "_submenu");
            submenu.style.display = submenu.style.display === "block" ? "none" : "block";
        }
    </script>
    <br><br><br><br>
    <h1>Mark Attendance</h1>
    <form action="attendance.php" method="post">
        <label for="class_course">Class and Course:</label>
        <select name="class_course" id="class_course" required onchange="this.form.submit()">
            <option value="">Select Class and Course</option>
            <?php foreach ($teacher_courses as $course): ?>
                <option value="<?= $course['class_id'] . '-' . $course['course_id'] ?>" <?= (isset($_POST['class_course']) && $_POST['class_course'] == $course['class_id'] . '-' . $course['course_id']) ? 'selected' : '' ?>>
                    <?= $course['class_name'] ?> - <?= $course['course_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <?php if (!empty($students) && !$attendance_marked): ?>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['student_name']) ?></td>
                        <td><input type="radio" name="attendance[<?= $student['student_id'] ?>]" value="present" required></td>
                        <td><input type="radio" name="attendance[<?= $student['student_id'] ?>]" value="absent" required></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit">Submit Attendance</button>
        <?php elseif ($attendance_marked): ?>
            <p>Attendance for today has already been marked.</p>
        <?php endif; ?>
    </form>
</body>
</html>
