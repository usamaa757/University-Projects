<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the teacher is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}
$message ='';
$teacher_id = $_SESSION['user_id'];

$teacher_name = $_SESSION['teacher_name'];

// Fetch classes and courses for the logged-in teacher
$query = "SELECT tcc.class_id, tcc.course_id, c.course_name, cl.class_name
          FROM teacher_class_course tcc
          JOIN courses c ON tcc.course_id = c.course_id
          JOIN classes cl ON tcc.class_id = cl.class_id
          WHERE tcc.teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher_courses = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure class_course, title, and due_date are set
    if (isset($_POST['class_course'], $_POST['title'], $_POST['due_date'])) {
        // Extract class_id and course_id from the combined value
        list($class_id, $course_id) = explode('-', $_POST['class_course']);
        $title = $_POST['title'];
        $due_date = $_POST['due_date'];

        // File upload handling
        if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
            $upload_dir = 'uploads/';
            $upload_file = $upload_dir . basename($_FILES['assignment_file']['name']);
            
            if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_file)) {
                // Save assignment details in the database
                $stmt = $conn->prepare("INSERT INTO assignments (teacher_id, class_id, course_id, title, due_date, assignment_file) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iiisss", $teacher_id, $class_id, $course_id, $title, $due_date, $upload_file);
                if ($stmt->execute()) {
                    $message = "Assignment uploaded successfully.";
                } else {
                    $message = "Database error: " . $stmt->error;
                }
            } else {
                $message = "Failed to upload file.";
            }
        } else {
            $message = "No file uploaded or there was an upload error.";
        }
    } else {
        $message = "Required fields are missing.";
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
<body>
    <h1>Upload Assignment</h1>
   

    <form action="create_assignment.php" method="post" enctype="multipart/form-data">
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        <label for="class_course">Class and Course:</label>
        <select name="class_course" id="class_course" required>
            <option value="">Select Class and Course</option>
            <?php foreach ($teacher_courses as $course): ?>
                <option value="<?= $course['class_id'] . '-' . $course['course_id'] ?>">
                    <?= $course['class_name'] ?> - <?= $course['course_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
        <br>
        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required>
        <br>
        <label for="assignment_file">Upload File:</label>
        <input type="file" name="assignment_file" id="assignment_file" required>
        <br>
        <button type="submit">Upload Assignment</button>
    </form>

   
</body>
</html>
