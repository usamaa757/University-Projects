<?php
include 'header.php';
include '../other/db_connection.php';

// Initialize variables
$teachers = [];
$classes = [];
$courses = [];
$assignment = [];
$message = '';

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

// Fetch courses
$course_sql = "SELECT course_id, course_name FROM courses";
$course_result = $conn->query($course_sql);
if ($course_result->num_rows > 0) {
    while ($course_row = $course_result->fetch_assoc()) {
        $courses[] = $course_row;
    }
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $teacher_id = $_POST['teacher_id'];
    $class_id = $_POST['class_id'];
    $course_id = $_POST['course_id'];

    $update_sql = "UPDATE teacher_class_course SET teacher_id = ?, class_id = ?, course_id = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("iiii", $teacher_id, $class_id, $course_id, $id);

    if ($stmt_update->execute()) {
        $message = 'Assignment updated successfully!';
    } else {
        $message = 'Error updating assignment!';
    }
}

// Fetch existing assignment details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $fetch_sql = "SELECT * FROM teacher_class_course WHERE id = ?";
    $stmt_fetch = $conn->prepare($fetch_sql);
    $stmt_fetch->bind_param("i", $id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($result->num_rows > 0) {
        $assignment = $result->fetch_assoc();
    } else {
        $message = 'Assignment not found!';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Assignment</title>
    <link rel="stylesheet" href="../css/manage.css">
    <link rel="stylesheet" href="../css/form.css">
</head>

<body>
    <div class="container">
        <h3>Edit Assignment</h3>
        
        <!-- Display operation message -->
        <?php if ($message != ''): ?>
            <p><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($assignment['id']); ?>">

            <label for="teacher_id">Select Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <?php if (!empty($teachers)): ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>"
                            <?php echo ($assignment['teacher_id'] == $teacher['teacher_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['teacher_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No teachers available</option>
                <?php endif; ?>
            </select>

            <label for="class_id">Select Class:</label>
            <select id="class_id" name="class_id" required>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class['class_id']); ?>"
                            <?php echo ($assignment['class_id'] == $class['class_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No classes available</option>
                <?php endif; ?>
            </select>

            <label for="course_id">Select Course:</label>
            <select id="course_id" name="course_id" required>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['course_id']); ?>"
                            <?php echo ($assignment['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No courses available</option>
                <?php endif; ?>
            </select>

            <input type="submit" value="Update Assignment">
        </form>
    </div>
</body>

</html>
