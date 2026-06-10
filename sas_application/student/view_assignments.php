<?php
session_start();
require '../other/db_connection.php';
$message = $_GET['msg'] ?? '';

// Check if student ID is set in the session
$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    echo 'Student ID not set in session.';
    exit;
}

$student_name = $_SESSION['student_name'] ?? '';

// Fetch assignments for the student
$query = "SELECT a.assignment_id, a.title, a.assignment_file, a.due_date, a.course_id, c.course_name, sa.status
          FROM assignments a
          JOIN courses c ON a.course_id = c.course_id
          LEFT JOIN student_assignments sa ON a.assignment_id = sa.assignment_id AND sa.student_id = ?
          ORDER BY a.due_date ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$assignments = [];
while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}
$current_date = date('Y-m-d'); // Use a consistent date format for comparisons

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
    <style>
       

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .closed {
            color: #888;
        }
    </style>
</head>

<body>
    <div class="top-bar">
        <div class="menu-icon" onclick="toggleNavbar()">&#9776;</div>
        <div class="logo"><i class="fas fa-school fa-1x"></i> School Automation System </div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
        </div>
    </div>

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
    <br><br><br><br>
    <div style = "background-color:#fff; padding:10px;">
        <h3>Assignment List</h3>
        <?php if (!empty($assignments)) : ?>
            <table>
            <?php if ($message) : ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
                <thead>
                    <tr>
                        <th>Assignment ID</th>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Download</th>
                        <th>Upload</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment) : ?>
                        <tr>
                            <td><?= htmlspecialchars($assignment['assignment_id']) ?></td>
                            <td><?= htmlspecialchars($assignment['title']) ?></td>
                            <td><?= htmlspecialchars($assignment['course_name']) ?></td>
                            <td><?= htmlspecialchars(date('d M, Y', strtotime($assignment['due_date']))) ?></td>
                            <td>
                                <?php if ($current_date <= $assignment['due_date']): ?>
                                    <?php if (!empty($assignment['assignment_file'])) : ?>
                                        <a href="download.php?file=<?= urlencode($assignment['assignment_file']) ?>" download>Download</a>
                                    <?php else : ?>
                                        N/A
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:red">Closed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="upload_assignment.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="assignment_id" value="<?= htmlspecialchars($assignment['assignment_id']) ?>">
                                    <input type="hidden" name="course_id" value="<?= htmlspecialchars($assignment['course_id']) ?>">
                                    <input type="hidden" name="student_id" value="<?php echo $student_id;?>">
                                    <input type="file" name="uploaded_file" required>
                                    <input type="submit" value="Upload">
                                </form>
                            </td>
                            <td style="color:green;"><?= htmlspecialchars($assignment['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No assignments found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
