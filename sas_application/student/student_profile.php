<?php
session_start();
require '../other/db_connection.php';

// Check if student ID is set in the session
$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    echo 'Student ID not set in session.';
    exit;
}
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['student_name'];
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $student_name = $_POST['student_name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hash the password if provided
    if ($password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Update student information in the database
    $query = "UPDATE students SET student_name = ?, dob = ?, email = ?";
    if ($password) {
        $query .= ", password = ?";
    }
    $query .= " WHERE student_id = ?";

    $stmt = $conn->prepare($query);

    if ($password) {
        $stmt->bind_param('ssssi', $student_name, $dob, $email, $password, $student_id);
    } else {
        $stmt->bind_param('sssi', $student_name, $dob, $email, $student_id);
    }

    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch student information along with the class name
$query = "SELECT s.student_name, s.dob, s.email, c.class_name
          FROM students s
          JOIN classes c ON s.class_id = c.class_id
          WHERE s.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
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
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }

        form {
            display: none;
        }

        input,
        textarea,
        select {
            display: block;
            margin: 10px 0;
        }
    </style>
    <script>
        function toggleEdit() {
            document.getElementById("editForm").style.display = "block";
        }
    </script>

    <body>
        <br><br><br><br>
        <div class="container">
            <h2>Student Profile</h2>
            <?php if ($student): ?>
                <p>Name: <?= htmlspecialchars($student['student_name']) ?></p>
                <p>Date of Birth: <?= htmlspecialchars($student['dob']) ?></p>
                <p>Email: <?= htmlspecialchars($student['email']) ?></p>
                <p>Class: <?= htmlspecialchars($student['class_name']) ?></p> <!-- Display class name -->

                <!-- "Edit" button to switch to edit mode -->
                <button onclick="toggleEdit()">Edit</button>

                <!-- Form for editing (hidden by default) -->
                <form id="editForm" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" enctype="multipart/form-data">
                    <label for="name">Name:</label>
                    <input type="text" name="student_name" value="<?= htmlspecialchars($student['student_name']) ?>"><br>
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($student['dob']) ?>"><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>"><br>
                    <label for="password">Password (leave blank to keep unchanged):</label>
                    <input type="password" name="password"><br>
                    <input type="submit" value="Update">
                </form>
            <?php else: ?>
                <p>Student not found.</p>
            <?php endif; ?>
        </div>
    </body>

</html>