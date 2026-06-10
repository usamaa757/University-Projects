<?php
include 'header.php';
include '../other/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_class']) && isset($_POST['class_id'])) {
    // Extract student_id from the submitted value
    $student_id = $_POST['student_class'];
    $class_id = $_POST['class_id'];


    // Fetch courses selected by the student
    $sql = "SELECT courses.course_id, courses.course_name
            FROM course_selection
            JOIN courses ON course_selection.course_id = courses.course_id 
            WHERE course_selection.student_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $courses = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $courses = [];
    }
    $stmt->close();
}
?>
<br><br>
<h2>Enter Marks for Courses</h2>

<?php if (!empty($courses)): ?>
    <form method="POST" action="submit_marks.php">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <input type="hidden" name="class_id" value="<?php echo   $class_id; ?>">

        <?php foreach ($courses as $course): ?>
            <div>
                <label for="course_<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?>:</label>
                <input type="number" id="course_<?php echo $course['course_id']; ?>" name="marks[<?php echo $course['course_id']; ?>]" required>
            </div>
        <?php endforeach; ?>

        <input type="submit" value="Submit Marks">
    </form>
<?php else: ?>
    <p>No courses found for the selected student.</p>
<?php endif; ?>
