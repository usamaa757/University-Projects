<?php
include 'db.php';
include 'student_auth.php';

$student_id = $_SESSION['student_id'];

// Fetch seat assignments for student’s courses
$query = "
    SELECT 
        c.course_name, 
        r.room_name, 
        sa.row_number, 
        sa.column_number
    FROM student_courses sc
    JOIN courses c ON sc.course_id = c.course_id
    JOIN seat_assignments sa ON sc.student_id = sa.student_id AND sc.course_id = sa.course_id
    JOIN rooms r ON sa.room_id = r.room_id
    WHERE sc.student_id = '$student_id'
    ORDER BY c.course_name
";

$result = mysqli_query($conn, $query);
$alphabet = range('A', 'Z');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Seat Assignments</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="container">
        <h3>My Seat Assignments</h3>
        <form action="generate_pdf.php" method="post">
            <button type="submit">Download Seating Info (PDF)</button>
        </form>

        <?php if (mysqli_num_rows($result) > 0) { ?>
        <table>
            <tr>
                <th>Course</th>
                <th>Room</th>
                <th>Seat</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) {
                    $seat_label = $alphabet[$row['row_number'] - 1] . $row['column_number'];
                ?>
            <tr>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['room_name']) ?></td>
                <td><?= $seat_label ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <p>No seat assignments found.</p>
        <?php } ?>
    </div>

    <div class="logout-btn">
        <a href="logout.php" class="logout">Logout</a>
    </div>

</body>

</html>