<?php
include 'db.php';
session_start();

// Assuming student logs in and session holds their student_id
$student_id = $_SESSION['student_id'];

// Fetch assigned exams for the logged-in student
$query = "SELECT es.exam_date, es.exam_time, c.course_name 
          FROM student_exams sea
          JOIN exam_schedule es ON sea.exam_id = es.exam_id
          JOIN courses c ON es.course_id = c.course_id
          WHERE sea.student_id = '$student_id' 
          ORDER BY es.exam_date, es.exam_time";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exam Schedule</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="container">
        <h3>My Exam Schedule</h3>
        <form action="generate_pdf.php" method="post">
            <button type="submit">Download Exam Schedule (PDF)</button>
        </form>

        <?php if (mysqli_num_rows($result) > 0) { ?>
        <table>
            <tr>
                <th>Exam Date</th>
                <th>Exam Time</th>
                <th>Subject</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo date("d M, Y", strtotime($row['exam_date'])); ?></td>
                <td><?php echo date("h:i A", strtotime($row['exam_time'])); ?></td>
                <td><?php echo $row['course_name']; ?></td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <p>No exams scheduled yet.</p>
        <?php } ?>

    </div>
    <div class="logout-btn">

        <a href="logout.php" class="logout">Logout</a>
    </div>

</body>

</html>