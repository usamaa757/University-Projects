<?php
session_start();
include '../other/db_connection.php'; // Assume this file contains database connection code

// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    die('You are not logged in.');
}

$student_id = $_SESSION['user_id'];
$class_id = $_SESSION['class_id']; // Assuming you have class_id stored in the session

$message = '';
$course_query = "SELECT course_id FROM course_selection WHERE student_id = ?";
$course_stmt = $conn->prepare($course_query);
$course_stmt->bind_param("i", $student_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();

if ($course_result->num_rows > 0) {
    // Assuming a student is enrolled in only one course at a time
    $course = $course_result->fetch_assoc();
    $course_id = $course['course_id'];
} else {
    die('No course found for this student.');
}
// Fetch assignments for the student's class and course
$query = "SELECT a.assignment_id, a.title, a.due_date, a.assignment_file, c.course_name, cl.class_name
          FROM assignments a
          JOIN courses c ON a.course_id = c.course_id
          JOIN classes cl ON a.class_id = cl.class_id
          WHERE a.class_id = ? AND a.course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $class_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$assignments = $result->fetch_all(MYSQLI_ASSOC);

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = $_POST['assignment_id'];

    // File upload handling for assignment submission
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
        $upload_dir = 'submissions/';
        $upload_file = $upload_dir . basename($_FILES['submission_file']['name']);

        if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $upload_file)) {
            // Save submission details in the database
            $stmt = $conn->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, submission_file) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $assignment_id, $student_id, $upload_file);
            if ($stmt->execute()) {
                $message = "Assignment submitted successfully.";
            } else {
                $message = "Database error: " . $stmt->error;
            }
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "No file uploaded or there was an upload error.";
    }
}
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
    <h1>Assignments for <?php echo htmlspecialchars($class_id); ?> - <?php echo htmlspecialchars($course_id); ?></h1>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Due Date</th>
                <th>Download</th>
                <th>Submit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignments as $assignment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                    <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                    <td><a href="<?php echo  '../teacher/' . htmlspecialchars($assignment['assignment_file']); ?>" download>Download</a></td>
                    <td>
                        <form action="student_assignments.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                            <input type="file" name="submission_file" required>
                            <button type="submit">Submit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>