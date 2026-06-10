<?php
include 'db.php';
include 'header.php';
include 'admin_auth.php';

// Fetch students with assigned courses and seat/room details
$student_query = mysqli_query($conn, "
    SELECT s.student_id, s.email, c.course_name, sc.course_id,
           sa.row_number, sa.column_number, r.room_name
    FROM student_courses sc
    JOIN students s ON sc.student_id = s.student_id
    JOIN courses c ON sc.course_id = c.course_id
    LEFT JOIN seat_assignments sa ON sc.student_id = sa.student_id AND sc.course_id = sa.course_id
    LEFT JOIN rooms r ON sa.room_id = r.room_id
    ORDER BY c.course_name, s.email
");
?>

<h3>Send Reminder</h3>

<form action="send_reminder.php" method="POST">
    <table border="1" cellpadding="10">
        <tr>
            <th><input type="checkbox" id="selectAll"> Select All</th>
            <th>Student Email</th>
            <th>Course</th>
            <th>Room</th>
            <th>Seat</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($student_query)) {
            // Create seat label if row and column numbers are available
            $seat_label = isset($row['row_number']) && isset($row['column_number']) ?
                chr(64 + $row['row_number']) . $row['column_number'] : 'Not Assigned';
        ?>
        <tr>
            <td>
                <input type="checkbox" class="student-checkbox" name="students[]"
                    value="<?php echo htmlspecialchars(json_encode($row)); ?>">
            </td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
            <td><?php echo htmlspecialchars($row['room_name'] ?? 'Room info not available'); ?></td>
            <td><?php echo htmlspecialchars($seat_label); ?></td>
        </tr>
        <?php } ?>
    </table>
    <br>
    <button type="submit">Send Reminders</button>
</form>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

</body>

</html>