<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];

// Fetch attendance records for the student from the attendance table only
$query = "SELECT attendance_date, status, course_id, class_id
          FROM attendance
          WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance_records = $result->fetch_all(MYSQLI_ASSOC);

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
        <div class="logo"><i class="fas fa-school fa-1x"></i> School Automation System</div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
            <li><a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="view_lecture.php"><i class="fas fa-chalkboard-teacher"></i> Lectures</a></li>
            <li><a href="view_voucher.php"><i class="fas fa-money-bill"></i> Fees</a></li>
            <li><a href="view_assignments.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="view_quiz.php"><i class="fas fa-pen"></i> Quizzes</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-graduation-cap"></i> Attendance</a></li>
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
    <div class="container">
        <h1>My Attendance</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Course ID</th>
                    <th>Class ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                        <td><?= htmlspecialchars($record['status']) ?></td>
                        <td><?= htmlspecialchars($record['course_id']) ?></td>
                        <td><?= htmlspecialchars($record['class_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
