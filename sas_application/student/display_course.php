<?php
include '../other/db_connection.php';

session_start();

$student_id = $_SESSION['user_id'];
$class_id = $_SESSION['class_id'];
$student_name = $_SESSION['student_name'];

$stmt = $conn->prepare("
    SELECT cs.course_id, c.course_name, c.course_description
    FROM course_selection cs
    JOIN courses c ON cs.course_id = c.course_id
    WHERE cs.student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
    <li><a href="../student/student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
    <li><a href="#" onclick="toggleSubmenu('courses')"><i class="fas fa-chalkboard-teacher"></i> Courses <span class="arrow">&#9662;</span></a>
        <ul class="submenu" id="courses_submenu">
            <li><a href="../student/display_course.php"><i class="fas fa-eye"></i> View Courses</a></li>
            <li><a href="course_selection.php"><i class="fas fa-edit"></i> Course Selection</a></li>
        </ul>
    </li>
    <li><a href="#" onclick="toggleSubmenu('lecture')"><i class="fas fa-chalkboard-teacher"></i> Lectures<span class="arrow">&#9662;</span></a>
        <ul class="submenu" id="lecture_submenu">
            <li><a href="../student/view_lecture.php"><i class="fas fa-eye"></i> View lecture</a></li>
            <li><a href="course_selection.php"><i class="fas fa-edit"></i> Course Selection</a></li>
        </ul>
    </li>
    <li><a href="view_voucher.php"><i class="fas fa-money-bill"></i> Fees</a></li>
    <li><a href="../student/submit_assignment.php"><i class="fas fa-tasks"></i> Assignments</a></li>
    <li><a href="../student/submit_quiz.php"><i class="fas fa-pen"></i> Quizzes</a></li>
    <li><a href="../student/view_attendance.php"><i class="fas fa-graduation-cap"></i> attendance </a></li>
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
    <style>
        header {
            background-color: #0f582d;
            color: #fff;
            padding: 5px;
            text-align: center;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: white;
            text-align: center;
            padding: 20px 0;
        }
        .course-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            padding: 20px;
        }
        .course {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: calc(33.333% - 20px); /* Responsive width with padding */
        }
        .no-courses {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #333;
        }
    </style>
<body>
    <header>
        <h1>Your Selected Courses</h1>
    </header>
    <div class="course-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="course">
                    <strong><header>Course Name:</strong> <?= htmlspecialchars($row['course_name']) ?><br></header><br>
                    <strong>Course Description:</strong> <?= htmlspecialchars($row['course_description']) ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-courses">
                <p>You have not selected any courses yet. Please <a href="course_selection.php">select course</a> first</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
