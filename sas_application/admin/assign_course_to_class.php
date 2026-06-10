<?php
include 'header.php';
include '../other/db_connection.php';

// Initialize variables
$message = '';
$classes = [];
$courses = [];

// Fetch classes
$class_sql = "SELECT class_id, class_name FROM classes";
$class_result = $conn->query($class_sql);
if ($class_result->num_rows > 0) {
    while ($class_row = $class_result->fetch_assoc()) {
        $classes[] = $class_row;
    }
}

// Fetch all courses
$course_sql = "SELECT course_id, course_name FROM courses";
$course_result = $conn->query($course_sql);
if ($course_result->num_rows > 0) {
    while ($course_row = $course_result->fetch_assoc()) {
        $courses[] = $course_row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['assign'])) {
        $class_id = $_POST['class_id'];
        $course_ids = $_POST['course_ids']; // Array of course IDs

        // Insert assignments into the class_course table
        foreach ($course_ids as $course_id) {
            $sql_insert = "INSERT INTO class_course (class_id, course_id) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $class_id, $course_id);

            if (!$stmt_insert->execute()) {
                $message = 'Error assigning courses to class!';
                break; // Exit loop on error
            }
        }

        if ($message === '') {
            $message = 'Courses assigned to class successfully!';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assign Courses to Class</title>
    <link rel="stylesheet" href="../css/manage.css">
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>
    <div class="container">
        <h3>Assign Courses to Class</h3>

        <!-- Display operation message -->
        <?php if ($message != ''): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- Assign Courses Form -->
        <form method="POST">
            <label for="class_id">Select Class:</label>
            <select id="class_id" name="class_id" required>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class['class_id']); ?>">
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No classes available</option>
                <?php endif; ?>
            </select>

            <label for="course_ids">Select Courses:</label>
            <div id="courses_container">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <label>
                            <input type="checkbox" name="course_ids[]" value="<?php echo htmlspecialchars($course['course_id']); ?>">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No courses available</p>
                <?php endif; ?>
            </div>

            <input type="submit" name="assign" value="Assign">
        </form>
    </div>
</body>

</html>
