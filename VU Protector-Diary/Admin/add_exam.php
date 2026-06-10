<?php
include '../connection.php';

if (isset($_POST['add_exam'])) {
    $exam_name  = mysqli_real_escape_string($con, $_POST['exam_name']);
    $exam_date  = $_POST['exam_date'];
    $start_time = $_POST['start_time'];
    $end_time   = $_POST['end_time'];
    $center     = mysqli_real_escape_string($con, $_POST['center']);

    if ($start_time >= $end_time) {
        echo "<script>alert('End time must be greater than start time');</script>";
    } else {
        $insert = mysqli_query($con, "
            INSERT INTO exams (exam_name, exam_date, start_time, end_time, center)
            VALUES ('$exam_name','$exam_date','$start_time','$end_time','$center')
        ");

        if ($insert) {
            echo "<script>alert('Exam added successfully'); window.location='add_exam.php';</script>";
        } else {
            echo "<script>alert('Error adding exam');</script>";
        }
    }
}
?>


<?php include '../header/admin-header.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Exam</h2>

    <div class="card shadow p-4">
        <form method="POST">

            <div class="form-group">
                <label>Exam Name</label>
                <input type="text" name="exam_name" class="form-control" placeholder="Enter Exam Name" required>
            </div>

            <div class="form-group">
                <label>Exam Date</label>
                <input type="date" name="exam_date" class="form-control" required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>

                <div class="form-group col-md-6">
                    <label>End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Exam Center</label>
                <input type="text" name="center" class="form-control" placeholder="Enter Exam Center" required>
            </div>

            <button type="submit" name="add_exam" class="btn btn-success ">
                Add Exam
            </button>

        </form>
    </div>

</div>

</body>

</html>