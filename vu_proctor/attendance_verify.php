<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

$admin_id = $_SESSION['user_id'];

// Fetch attendance to verify
$records = $conn->query("
    SELECT d.id as duty_id, e.exam_name, e.exam_date, e.center, u.full_name, d.attendance_status, d.verified_by
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    JOIN users u ON d.user_id = u.id
    ORDER BY e.exam_date DESC
");

// Handle verification
if (isset($_GET['verify'])) {
    $duty_id = intval($_GET['verify']);
    $conn->query("UPDATE duties SET verified_by = $admin_id, verified_at = NOW() WHERE id = $duty_id");
    $msg = "Attendance verified!";
}
?>

<div class="table-container">
    <h2>Verify Attendance</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>

    <table>
        <tr>
            <th>Exam</th>
            <th>Date</th>
            <th>Center</th>
            <th>User</th>
            <th>Status</th>
            <th>Verified</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $records->fetch_assoc()): ?>
        <tr>
            <td><?= $row['exam_name'] ?></td>
            <td><?= $row['exam_date'] ?></td>
            <td><?= $row['center'] ?></td>
            <td><?= $row['full_name'] ?></td>
            <td><?= ucfirst($row['attendance_status']) ?></td>
            <td><?= $row['verified_by'] ? 'Yes' : 'No' ?></td>
            <td>
                <?php if (!$row['verified_by'] && $row['attendance_status'] !== 'pending'): ?>
                <a href="?verify=<?= $row['duty_id'] ?>" class="delete"
                    onclick="return confirm('Verify this attendance?');">Verify</a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>