<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['superintendent', 'invigilator'])) {
    die("Access denied!");
}

$user_id = $_SESSION['user_id'];

// Fetch duties assigned to this user
$duties = $conn->query("
    SELECT d.id as duty_id, e.exam_name, e.exam_date, e.center, e.session, d.attendance_status, d.verified_by
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    WHERE d.user_id = $user_id
    ORDER BY e.exam_date DESC
");

// Handle attendance marking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $duty_id = intval($_POST['duty_id']);
    $status = $_POST['status']; // 'present' or 'absent'

    $sql = "UPDATE duties SET attendance_status = '$status' WHERE id = $duty_id AND user_id = $user_id";
    if ($conn->query($sql)) {
        $msg = "Attendance marked successfully!";
    } else {
        $error = "Error updating attendance!";
    }
}
?>

<div class="table-container">
    <h2>Mark Attendance</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <table>
        <tr>
            <th>Exam</th>
            <th>Date</th>
            <th>Center</th>
            <th>Session</th>
            <th>Status</th>
            <th>Verified</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $duties->fetch_assoc()): ?>
            <tr>
                <td><?= $row['exam_name'] ?></td>
                <td><?= $row['exam_date'] ?></td>
                <td><?= $row['center'] ?></td>
                <td><?= ucfirst($row['session']) ?></td>
                <td><?= ucfirst($row['attendance_status']) ?></td>
                <td><?= $row['verified_by'] ? 'Yes' : 'No' ?></td>

                <td>
                    <?php if ($row['attendance_status'] == 'pending'): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="duty_id" value="<?= $row['duty_id'] ?>">
                            <select name="status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                            </select>
                            <button type="submit" name="mark_attendance">Submit</button>
                        </form>
                    <?php else: ?>
                        <span>Marked</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>