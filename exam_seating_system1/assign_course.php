<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';

if (isset($_POST['assign_course'])) {
    $student_ids = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    if (!empty($student_ids) && !empty($course_id)) {
        foreach ($student_ids as $student_id) {
            // Check if already assigned
            $check = mysqli_query($conn, "SELECT * FROM student_courses WHERE student_id='$student_id' AND course_id='$course_id'");
            if (mysqli_num_rows($check) > 0) {
                echo "<script>alert('Student ID $student_id already assigned to Course ID $course_id!');</script>";
            } else {
                mysqli_query($conn, "INSERT INTO student_courses (student_id, course_id) VALUES ('$student_id', '$course_id')");
                echo "<script>alert('Course assigned successfully!'); window.location.href='assign_seat.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Please select at least one student and one course.');</script>";
    }
}

?>



<div class="data-container">
    <h3>Assign Course to Student</h3>

    <form action="" method="POST">
        <!-- Select Student -->
        <label>Select Student:</label>
        <select name="student_id[]" required multiple>
            <?php
            $students = mysqli_query($conn, "SELECT * FROM students");
            while ($row = mysqli_fetch_assoc($students)) {
                echo "<option value='" . $row['student_id'] . "'>" . $row['student_name'] . " (Roll: " . $row['student_id'] . ")</option>";
            }
            ?>
        </select>

        <!-- Select Course -->
        <label>Select Course:</label>
        <select name="course_id[]" required multiple>
            <?php
            $courses = mysqli_query($conn, "SELECT * FROM courses");
            while ($row = mysqli_fetch_assoc($courses)) {
                echo "<option value='" . $row['course_id'] . "'>" . $row['course_name'] . "</option>";
            }
            ?>
        </select>

        <button type="submit" name="assign_course">Assign Course</button>
    </form>
</div>

</body>

</html>