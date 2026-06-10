<?php
// Start output buffering
ob_start();
session_start();
// Include the database connection file
include '../other/db_connection.php';

// Assuming teachers_id is stored in the session when teachers logs in
$teacher_id = $_SESSION['user_id'];
$teacher_name = $_SESSION['teacher_name'];

// Fetch existing teachers data
$stmt = $conn->prepare("SELECT teacher_name, email FROM teachers WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher_data = $result->fetch_assoc();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_name = $_POST['teacher_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL for updating teachers details
    if (!empty($password)) {
        // Hash the new password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update with password
        $stmt = $conn->prepare("UPDATE teachers SET teacher_name = ?, email = ?, password = ? WHERE teacher_id = ?");
        $stmt->bind_param("sssi", $teacher_name, $email, $hashed_password, $teacher_id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE teachers SET teacher_name = ?, email = ? WHERE teacher_id = ?");
        $stmt->bind_param("ssi", $teacher_name, $email, $teacher_id);
    }

    // Execute the update query
    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Redirect to the same page with a message
    header("Location: profile.php?message=" . urlencode($message));
    exit;
}

// Close the connection
$conn->close();

// Flush the output buffer
ob_end_flush();
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
    <style>
      
   
      
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        /* .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        } */
      
        
        .message {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <?php
            // Display message if available
            if (isset($_GET['message'])) {
                echo "<p class='message'>" . htmlspecialchars($_GET['message']) . "</p>";
            }
            ?>
        <div style="text-align: center; margin-top: 10px;">
        
        <h3>Update Profile</h3>
    </div>
            <div class="form-group">
                <label for="teacher_name">teacher Name:</label>
                <input type="text" id="teacher_name" name="teacher_name" value="<?php echo htmlspecialchars($teacher_data['teacher_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <button type="submit">Update</button>
            </div>
        </form>
    </div>
</body>
</html>
