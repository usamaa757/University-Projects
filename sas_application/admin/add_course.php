<?php
// Include the database connection
include '../other/db_connection.php';
include 'header.php';
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $class_id = $_POST['class_id'];

    // Insert a new course into the database
    $insert_query = "INSERT INTO courses (course_name, course_description, class_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssi", $course_name, $course_description, $class_id);

    $stmt->execute();
    $stmt->close();
    header("Location: view_course.php");
    exit();
}

// Fetch available classes
$fetch_classes_query = "SELECT class_id, class_name FROM classes";
$stmt = $conn->prepare($fetch_classes_query);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link rel="stylesheet" href="../css/form.css">
</head>
<body>
    <header>
        <h1>Add Course</h1>
    </header>

    <!-- Form for creating a course -->
    <form action="add_course.php" method="POST">
        <label for="course_name">Course Name:</label>
        <input type="text" id="course_name" name="course_name" required>

        <label for="course_description">Course Description:</label>
        <textarea id="course_description" name="course_description" required></textarea>

        <label for="class_id">Class:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['class_id']); ?>">
                    <?php echo htmlspecialchars($class['class_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save</button>
    </form>

    <p><a href="view_course.php">View Courses</a></p>
</body>
</html>
