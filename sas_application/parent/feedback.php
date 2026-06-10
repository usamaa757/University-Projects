<?php
// Database configuration
include '../other/db_connection.php'; // Ensure this file contains the correct connection details
session_start();

$feedback_message = "";
$teachers = [];
$parent_id = $_SESSION['user_id'];

// Fetch student_id based on parent_id
$sql = "SELECT student_id, parent_name FROM parents WHERE parent_id = $parent_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $student_id = $row['student_id'];
    $parent_name = $row['parent_name'];

    // Fetch the class selected by the student
    $sql_class = "SELECT class_id FROM students WHERE student_id = $student_id";
    $result_class = $conn->query($sql_class);

    if ($result_class->num_rows > 0) {
        $row_class = $result_class->fetch_assoc();
        $class_id = $row_class['class_id'];

        // Fetch teachers and their names using JOIN
        $sql_teacher = "
            SELECT DISTINCT t.teacher_id, t.teacher_name 
            FROM teacher_class_course tc
            JOIN teachers t ON tc.teacher_id = t.teacher_id
            WHERE tc.class_id = $class_id
        ";
        $result_teacher = $conn->query($sql_teacher);

        if ($result_teacher->num_rows > 0) {
            // Prepare an array to store teachers' information
            $teachers = [];
            while ($row_teacher = $result_teacher->fetch_assoc()) {
                $teachers[] = ['teacher_id' => $row_teacher['teacher_id'], 'teacher_name' => $row_teacher['teacher_name']];
            }
        } else {
            $feedback_message = "No teachers found for the selected class.";
        }
    } else {
        $feedback_message = "No class found for the selected student.";
    }
} else {
    $feedback_message = "No student found for the given parent.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inserting feedback into the database
    if (isset($_POST['quality_education'])) {
        $teacher_id = $conn->real_escape_string($_POST['teacher_id']); // Get the selected teacher_id from the form
        $remarks = $conn->real_escape_string($_POST['quality_education']); // Get the selected feedback value
        
        // Other fields like additional feedback can also be included
        $additional_feedback = $conn->real_escape_string($_POST['additional_feedback']);

        // SQL query to insert feedback into the database
        $sql_insert = "INSERT INTO parent_feedback (student_id, teacher_id, remarks, parent_id, additional_feedback) 
                       VALUES ('$student_id', '$teacher_id', '$remarks', '$parent_id', '$additional_feedback')";

        if ($conn->query($sql_insert) === TRUE) {
            $feedback_message = "Thank you for your feedback!";
        } else {
            $feedback_message = "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Parent Feedback Form</title>
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
       

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            margin: auto;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }


        .success {
            color: #155724;
        }

        .error {
            color: #721c24;
        }
    </style>
</head>
   <!-- Top bar -->
    <div class="top-bar">
        <!-- Hamburger menu icon -->
        <div class="menu-icon" onclick="toggleNavbar()">
            &#9776;
        </div>
        <div class="logo"><i class="fas fa-school fa-1x"></i> School Automation System</div>
        <div class="user-info">
            <span style="margin-left: 70px;">Welcome, <?php echo htmlspecialchars($parent_name); ?></span>
        </div>
    </div>

    <!-- Vertical Navbar -->
    <div class="navbar" id="navbar">
        <ul>
            <li><a href="parent_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="feedback.php"><i class="fas fa-comments"></i> Give Feedback</a></li>
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
<body>

    <div class="container">
        <h2>Parent Feedback Form</h2>
        
        <!-- Display feedback message -->
        <?php if ($feedback_message != ''): ?>
            <div class="feedback-message <?php echo (strpos($feedback_message, 'Error') === false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <label for="teacher_id">Select Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <option value="">Select a teacher</option>
                <!-- Populate teachers dynamically -->
                <?php
                if (!empty($teachers)) {
                    foreach ($teachers as $teacher) {
                        echo '<option value="' . $teacher['teacher_id'] . '">' . htmlspecialchars($teacher['teacher_name']) . '</option>';
                    }
                }
                ?>
            </select>
            
            <label for="quality_education">Quality of Education:</label>
            <select id="quality_education" name="quality_education" required>
                <option value="not_satisfied">Not Satisfied</option>
                <option value="somewhat_satisfied">Somewhat Satisfied</option>
                <option value="satisfied">Satisfied</option>
                <option value="very_satisfied">Very Satisfied</option>
            </select>

            <label for="additional_feedback">Is there any other feedback you would like to provide?</label>
            <textarea id="additional_feedback" name="additional_feedback"></textarea>

            <input type="submit" value="Submit Feedback">
        </form>
    </div>

</body>

</html>
