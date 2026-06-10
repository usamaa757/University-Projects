<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Seating Arrangement</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>

    <h3>Assign Seating to Students</h3>

    <form action="process_seating.php" method="POST">
        <table>
            <tr>
                <th>S No</th>
                <th>Student Roll No</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>Exam ID</th>
                <th>Room</th>
            </tr>
            <?php $sno = 1;
            foreach ($students as $student) { ?>
            <tr>
                <td><?= $sno++; ?></td>
                <td><?= $student['student_id']; ?></td>
                <td><?= $student['student_name']; ?></td>
                <td><?= $student['course_name']; ?></td>
                <td><?= $student['exam_id']; ?></td>
                <td>
                    <select name="room_assignments[<?= $student['student_id']; ?>]">
                        <?php foreach ($rooms as $room) { ?>
                        <option value="<?= $room['room_id']; ?>"><?= $room['room_name']; ?> (Seats Left:
                            <?= $room['available_seats']; ?>)</option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </table>

        <button type="submit" name="assign_seats">Assign Seats</button>
    </form>

</body>

</html>