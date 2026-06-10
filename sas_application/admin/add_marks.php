<?php
include '../other/db_connection.php';

$sql = "SELECT students.student_id, students.student_name, classes.class_id, classes.class_name
        FROM students 
        JOIN classes ON students.class_id = classes.class_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Student and Class</title>
</head>
<body>
    <h2>Select Student and Class</h2>
    <form method="POST" action="marks_process.php">
        <label for="student_class">Select Student and Class:</label>
        
        <select id="student_class" name="student_class">

            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $student_id = $row['student_id'];
                    $student_name = $row['student_name'];
                    $class_name = $row['class_name'];
                    $class_id = $row['class_id'];
                    echo "<option value='$student_id'>$student_name - $class_name</option>";
                }
                echo  "<input type='hidden' name='class_id' value='$class_id'>";
            } else {
                echo "<option value=''>No students found</option>";
            }
            ?>
        </select>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
