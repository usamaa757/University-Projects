<?php
include 'db.php';
include 'header.php';

// Assuming student logs in and session holds their student_id
$student_id = $_SESSION['student_id'];

// Fetch assigned exams for the logged-in student
$query = "SELECT es.exam_date, es.exam_time, sa.seat_number, c.course_name 
FROM student_exams sea
JOIN exam_schedule es ON sea.exam_id = es.exam_id
JOIN courses c ON es.course_id = c.course_id
LEFT JOIN seat_assignments sa ON sea.student_id = sa.student_id AND sea.exam_id = sa.exam_id
WHERE sea.student_id = '$student_id' 
ORDER BY es.exam_date, es.exam_time";

$result = mysqli_query($conn, $query);
?>

<!-- Main Content -->
<div class="container mt-4">
    <h3 class="mb-3 text-center">My Exam Schedule</h3>

    <form action="generate_pdf.php" method="post" class="mb-3 text-center">
        <button type="submit" class="btn btn-primary">Download Exam Schedule (PDF)</button>
    </form>

    <?php if (mysqli_num_rows($result) > 0) { ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>Exam Date</th>
                    <th>Exam Time</th>
                    <th>Subject</th>
                    <th>Seat No</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo date("d M, Y", strtotime($row['exam_date'])); ?></td>
                    <td><?php echo date("h:i A", strtotime($row['exam_time'])); ?></td>
                    <td><?php echo $row['course_name']; ?></td>
                    <td><?php echo $row['seat_number']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
    <p class="alert alert-warning text-center">No exams scheduled yet.</p>
    <?php } ?>

</div>


</body>

</html>