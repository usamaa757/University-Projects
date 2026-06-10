<?php
include 'db.php';
include 'header.php';
// Fetch students with assigned exams
$student_query = mysqli_query($conn, "
    SELECT se.student_id, s.student_name, se.exam_id, e.course_id, c.course_name 
    FROM student_exams se
    JOIN students s ON se.student_id = s.student_id
    JOIN exam_schedule e ON se.exam_id = e.exam_id
    JOIN courses c ON e.course_id = c.course_id
    ORDER BY course_name ASC");

$students = [];
while ($row = mysqli_fetch_assoc($student_query)) {
    $students[] = $row;
}

// Fetch available rooms
$room_query = mysqli_query($conn, "SELECT * FROM rooms WHERE available_seats > 0");
$rooms = [];
while ($row = mysqli_fetch_assoc($room_query)) {
    $rooms[] = $row;
}

?>

<!-- Main Content -->
<div class="container mt-4">
    <h3 class="text-center mb-4">Assign Seating to Students</h3>

    <div class="card shadow p-4">
        <form action="process_seating.php" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Exam ID</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student) { ?>
                        <tr>
                            <td><?= $student['student_name']; ?></td>
                            <td><?= $student['course_name']; ?></td>
                            <td><?= $student['exam_id']; ?></td>
                            <td>
                                <select name="room_assignments[<?= $student['student_id']; ?>]" class="form-select">
                                    <?php foreach ($rooms as $room) { ?>
                                    <option value="<?= $room['room_id']; ?>"><?= $room['room_name']; ?> (Seats Left:
                                        <?= $room['available_seats']; ?>)</option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center">
                <button type="submit" name="assign_seats" class="btn btn-primary">Assign Seats</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>