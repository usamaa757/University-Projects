<?php
// Include the database connection
include '../other/db_connection.php';
include 'header.php';

// Fetch all courses from the database, including class information
$fetch_query = "SELECT c.course_id, c.course_name, c.course_description, cl.class_id, cl.class_name
               FROM courses AS c
               JOIN classes AS cl ON c.class_id = cl.class_id";
$stmt = $conn->prepare($fetch_query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Courses</title>
    <link rel="stylesheet" href="../css/form.css">
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <header>
        <h1>Existing Courses</h1>
    </header>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Class</th>
            <th>Actions</th>
        </tr>

        <!-- Display courses with class information -->
        <?php while ($course = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $course['course_id']; ?></td>
                <td><?php echo $course['course_name']; ?></td>
                <td><?php echo $course['course_description']; ?></td>
                <td><?php echo $course['class_name']; ?></td>
                <td>
                    <!-- Form for editing a course -->
                    <form action="../admin/add_course.php" method="GET" style="display:inline;">
                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                        <input type="hidden" name="course_name" value="<?php echo $course['course_name']; ?>">
                        <input type="hidden" name="course_description" value="<?php echo $course['course_description']; ?>">
                        <input type="hidden" name="class_id" value="<?php echo $course['class_id']; ?>">
                        <button type="submit">Edit</button>
                    </form>

                    <!-- Form for deleting a course -->
                    <form action="../admin/delete_course.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>

        <?php
        // Close the statement and the database connection
        $stmt->close();
        $conn->close();
        ?>
    </table>

    <p><a href="../admin/add_course.php">+ Add Course</a></p>
</body>
</html>
