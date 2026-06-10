<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teachers') {
    header('Location: ../other/login.php');
    exit();
}

// Fetch the teacher's name from the session data
$teacher_name = $_SESSION['teacher_name'];
// HTML for the teacher dashboard page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Automation System - Teacher Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
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
            <li><a href="teacher_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            
                    <li><a href="../teacher/attendance.php"><i class="fas fa-edit"></i> Mark Attendance</a></li>
           
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
</body>

</html>
