<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';

// Fetch students with assigned courses
$student_query = mysqli_query($conn, "
    SELECT sc.student_id, s.student_name, sc.course_id, c.course_name 
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.student_id
    JOIN courses c ON sc.course_id = c.course_id
    WHERE NOT EXISTS (
        SELECT 1 FROM seat_assignments sa
        WHERE sa.student_id = sc.student_id AND sa.course_id = sc.course_id
    )
    ORDER BY c.course_name ASC");


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

<h3>Assign Seating to Students</h3>

<form action="process_seating.php" method="POST">
    <table border="1" cellpadding="6">
        <tr>
            <th>S No</th>
            <th>Student Roll No</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Room</th>
        </tr>

        <?php
        $sno = 1;
        if ($students) {
            foreach ($students as $student) { ?>
        <tr>
            <td><?= $sno++; ?></td>
            <td><?= $student['student_id']; ?></td>
            <td><?= $student['student_name']; ?></td>
            <td><?= $student['course_name']; ?></td>
            <td>
                <select name="room_assignments[<?= $student['student_id']; ?>][<?= $student['course_id']; ?>]">
                    <?php foreach ($rooms as $room) { ?>
                    <option value="<?= $room['room_id']; ?>"><?= $room['room_name']; ?> (Seats Left:
                        <?= $room['available_seats']; ?>)</option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php
            }
        } else { ?>
        <tr>
            <td colspan="5" style="text-align: center;">No record found</td>
        </tr>
        <?php } ?>
    </table>

    <?php if ($students) { ?>
    <button type="submit" name="assign_seats">Assign Seats</button>
    <?php } ?>
</form>

</body>

</html>