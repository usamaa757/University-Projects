<?php
include 'header.php';
include '../other/db_connection.php';

// Initialize variables
$message = '';
$teachers = [];
$classes = [];
$courses = [];

// Fetch teachers
$teacher_sql = "SELECT teacher_id, teacher_name FROM teachers";
$teacher_result = $conn->query($teacher_sql);
if ($teacher_result->num_rows > 0) {
    while ($teacher_row = $teacher_result->fetch_assoc()) {
        $teachers[] = $teacher_row;
    }
}

// Fetch classes
$class_sql = "SELECT class_id, class_name FROM classes";
$class_result = $conn->query($class_sql);
if ($class_result->num_rows > 0) {
    while ($class_row = $class_result->fetch_assoc()) {
        $classes[] = $class_row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['class_id'])) {
        $class_id = $_POST['class_id'];

        // Fetch courses based on the selected class_id
        $course_sql = "SELECT course_id, course_name FROM courses WHERE class_id = ?";
        $stmt = $conn->prepare($course_sql);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $course_result = $stmt->get_result();
        if ($course_result->num_rows > 0) {
            while ($course_row = $course_result->fetch_assoc()) {
                $courses[] = $course_row;
            }
        }
    }

    if (isset($_POST['assign'])) {
        $teacher_id = $_POST['teacher_id'];
        $class_id = $_POST['class_id'];
        $course_ids = $_POST['course_ids']; // Array of course IDs

        foreach ($course_ids as $course_id) {
            // Insert assignment into the teacher_class_course table
            $sql_insert = "INSERT INTO teacher_class_course (teacher_id, class_id, course_id) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iii", $teacher_id, $class_id, $course_id);

            if (!$stmt_insert->execute()) {
                $message = 'Error assigning teacher to class and course!';
                break; // Exit loop on error
            }
        }

        $message = 'Teacher assigned to class and courses successfully!';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assign Class to Teacher</title>
    <link rel="stylesheet" href="../css/manage.css">
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>
    <div class="container">
        <h3>Assign Class to Teacher</h3>

        <!-- Display operation message -->
        <?php if ($message != ''): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Assign Class Form -->
        <form method="POST">
            <label for="teacher_id">Select Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <?php if (!empty($teachers)): ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>">
                            <?php echo htmlspecialchars($teacher['teacher_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No teachers available</option>
                <?php endif; ?>
            </select>

            <label for="class_id">Select Class:</label>
            <select id="class_id" name="class_id" onchange="this.form.submit()" required>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class['class_id']); ?>" 
                            <?php echo (isset($_POST['class_id']) && $_POST['class_id'] == $class['class_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No classes available</option>
                <?php endif; ?>
            </select>

            <label for="course_ids">Select Courses:</label>
            <select id="course_ids" name="course_ids[]" multiple required>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['course_id']); ?>">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No courses available for the selected class</option>
                <?php endif; ?>
            </select>
<br>
            <input type="submit" name="assign" value="Assign">
        </form>
    </div>
</body>

</html>
