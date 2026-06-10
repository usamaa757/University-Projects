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
<!-- Main Content -->
<div class="container mt-4">
    <h3 class="text-center mb-4">Assign Exam to Student</h3>

    <div class="card shadow p-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Select Student:</label>
                <select name="student_id[]" class="form-select" required multiple>
                    <?php
                    $students = mysqli_query($conn, "SELECT * FROM students");
                    while ($row = mysqli_fetch_assoc($students)) {
                        echo "<option value='" . $row['student_id'] . "'>" . $row['student_name'] . " (Roll: " . $row['student_id'] . ")</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Exam:</label>
                <select name="exam_id[]" class="form-select" required multiple>
                    <?php
                    $exams = mysqli_query($conn, "SELECT es.*, c.course_name FROM exam_schedule es JOIN courses c ON es.course_id = c.course_id");
                    while ($row = mysqli_fetch_assoc($exams)) {
                        echo "<option value='" . $row['exam_id'] . "'>" . $row['course_name'] . " - " . date("d M, Y", strtotime($row['exam_date'])) . " - " . date("h:i A", strtotime($row['exam_time'])) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" name="assign_exam" class="btn btn-dark w-100">Assign Exam</button>
            </div>
        </form>
    </div>
</div>
</body>

</html>