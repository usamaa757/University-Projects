<?php
include 'db.php';
include 'header.php';
if (isset($_POST['assign_exam'])) {
    $student_ids = $_POST['student_id'];
    $exam_ids = $_POST['exam_id'];

    if (!empty($student_ids && $exam_ids)) {
        foreach ($student_ids as $student_id) {
            foreach ($exam_ids as $exam_id) {


                $check = mysqli_query($conn, "SELECT * FROM student_exams WHERE student_id='$student_id' AND exam_id='$exam_id'");

                if (mysqli_num_rows($check) > 0) {
                    echo "<script>alert('This student is already assigned to this exam!');</script>";
                } else {
                    // Insert into the student_exam_assignments table
                    mysqli_query($conn, "INSERT INTO student_exams (student_id, exam_id) VALUES ('$student_id', '$exam_id')");
                    echo "<script>alert('Exam assigned successfully!'); window.location.href='assign_exam.php';</script>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Exam to Student</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="data-container">
        <h3>Assign Exam to Student</h3>


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

            <!-- Select Exam -->
            <label>Select Exam:</label>
            <select name="exam_id[]" required multiple>
                <?php
                $exams = mysqli_query($conn, "SELECT es.*, c.course_name FROM exam_schedule es JOIN courses c ON es.course_id = c.course_id");
                while ($row = mysqli_fetch_assoc($exams)) {
                    echo "<option value='" . $row['exam_id'] . "'>" . $row['course_name'] . " - " . $row['exam_date'] . " - " . $row['exam_time'] . "</option>";
                }
                ?>
            </select>

            <button type="submit" name="assign_exam">Assign Exam</button>
        </form>
    </div>
</body>

</html>