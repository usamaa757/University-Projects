<?php
session_start();
include '../other/db_connection.php';

$student_id = $_SESSION['user_id'];

// Fetch the student's class and class name from the students table using a JOIN with the classes table
$stmt = $conn->prepare("
    SELECT s.class_id, c.class_name 
    FROM students s 
    JOIN classes c ON s.class_id = c.class_id 
    WHERE s.student_id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$class_result = $stmt->get_result();
$class_row = $class_result->fetch_assoc();

if ($class_row) {
    $class_id = $class_row['class_id'];
    $class_name = $class_row['class_name'];
} else {
    echo "Unable to fetch the student's class.";
    exit;
}

// Check if the student has already selected courses
$stmt = $conn->prepare("
    SELECT cs.course_id, co.course_name, co.course_description 
    FROM course_selection cs
    JOIN courses co ON cs.course_id = co.course_id
    WHERE cs.student_id = ? AND co.class_id = ?
");
$stmt->bind_param("ii", $student_id, $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Redirect to dashboard if the student has already selected courses
    header("Location: ../student/student_dashboard.php");
    exit;
}

// Fetch courses based on class_id
$stmt = $conn->prepare("
    SELECT co.course_id, co.course_name, co.course_description 
    FROM courses co
    WHERE co.class_id = ?
");
$stmt->bind_param("i", $class_id);
$stmt->execute();
$course_result = $stmt->get_result();
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


    <script>
        // Function to toggle the navbar
        function toggleNavbar() {
            var navbar = document.getElementById("navbar");
            navbar.classList.toggle("open");
        }
    </script>
    <header>
        <h1>Course Selection for Class: <?php echo htmlspecialchars($class_name); ?></h1>
    </header>
    <form action="save_selection.php" method="POST">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <table border="1">
            <tr>
                <th>Select</th>
                <th>Course Name</th>
                <th>Description</th>
            </tr>
            <?php while ($course = $course_result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="selected_courses[]" value="<?php echo htmlspecialchars($course['course_id']); ?>"></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <button type="submit">Submit Selection</button>
    </form>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>