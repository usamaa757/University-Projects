<?php
include 'header.php';
?>

<div class="seating-container">
    <h2>Seating Arrangement</h2>


    <table>
        <tr>
            <th>Seat Number</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Exam Date</th>
        </tr>

        <?php
        include 'db.php';
        $query = "SELECT sa.seat_number, s.student_id, s.student_name, c.course_name, e.exam_date 
              FROM seat_assignments sa
              JOIN students s ON sa.student_id = s.student_id
              JOIN exam_schedule e ON sa.exam_id = e.exam_id
              JOIN courses c ON e.course_id = c.course_id
              ORDER BY sa.seat_number";

        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$row['seat_number']}</td>
                <td>{$row['student_id']}</td>
                <td>{$row['student_name']}</td>
                <td>{$row['course_name']}</td>
                <td>" . date("d M, Y", strtotime($row['exam_date'])) . "</td>
              </tr>";
        }
        ?>
    </table>
</div>
</body>

</html>