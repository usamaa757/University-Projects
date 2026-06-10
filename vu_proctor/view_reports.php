<?php
include 'navbar.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied!");
}

// Fetch all uploaded reports
$reports = $conn->query("
    SELECT d.id as duty_id, e.exam_name, e.exam_date, e.center, u.full_name, d.report_file, d.report_uploaded_at
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    JOIN users u ON d.user_id = u.id
    WHERE d.report_file IS NOT NULL
    ORDER BY e.exam_date DESC
");
?>

<div class="table-container">
    <h2>Uploaded Attendance Reports</h2>

    <table>
        <tr>
            <th>Exam</th>
            <th>Date</th>
            <th>Center</th>
            <th>Superintendent</th>
            <th>Uploaded On</th>
            <th>Report File</th>
        </tr>

        <?php while ($row = $reports->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['exam_name']) ?></td>
            <td><?= htmlspecialchars($row['exam_date']) ?></td>
            <td><?= htmlspecialchars($row['center']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= $row['report_uploaded_at'] ?></td>
            <td>
                <a href="uploads/reports/<?= $row['report_file'] ?>" target="_blank">Download</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>