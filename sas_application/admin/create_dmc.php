<?php
include '../other/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];
    $sql = "SELECT courses.course_name
            FROM student_courses
            JOIN courses ON student_courses.course_id = courses.id
            WHERE student_courses.student_id = $student_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $courses = array();
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row['course_name'];
        }
        $report_link = "<a href='generate_report.php?student_id=$student_id'>Generate Report</a>";
    } else {
        $courses = "No courses selected by this student.";
        $report_link = "";
    }
} else {
    $student_id = "";
    $courses = "";
    $report_link = "";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>DMC - Generate Report</title>
</head>
<body>
    <h2>Select Student and Generate Report</h2>
    <form method="POST">
        <label for="student_id">Select Student:</label>
        <select id="student_id" name="student_id">
            <?php
            $sql = "SELECT id, name FROM students";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $selected = ($student_id == $row['id']) ? "selected" : "";
                    echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                }
            }
            ?>
        </select>
        <input type="submit" value="View Courses and Generate Report">
    </form>

    <?php if ($courses !== ""): ?>
    <h3>Courses for Student <?php echo $student_id; ?>:</h3>
    <?php if (is_array($courses)): ?>
        <ul>
            <?php foreach ($courses as $course): ?>
                <li><?php echo $course; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php echo $courses; ?></p>
    <?php endif; ?>

    <?php echo $report_link; ?>
    <?php endif; ?>
</body>
</html>
