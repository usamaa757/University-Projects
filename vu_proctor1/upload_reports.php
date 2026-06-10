<?php
include 'navbar.php';
include 'db.php';

// Allow only superintendents
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superintendent') {
    die("Access denied!");
}

$user_id = $_SESSION['user_id'];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['report'])) {
    $duty_id = intval($_POST['duty_id']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $targetDir = "uploads/reports/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES["report"]["name"]);
    $targetFile = $targetDir . $fileName;

    // Allow only certain file types
    $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($ext, $allowedTypes)) {
        if (move_uploaded_file($_FILES["report"]["tmp_name"], $targetFile)) {
            $sql = "UPDATE duties 
                    SET report_file = '$fileName', report_uploaded_at = NOW() 
                    WHERE id = $duty_id AND user_id = $user_id";
            if ($conn->query($sql)) {
                $msg = "Report uploaded successfully!";
            } else {
                $error = "Database error: " . $conn->error;
            }
        } else {
            $error = "File upload failed!";
        }
    } else {
        $error = "Invalid file type. Only PDF, DOC, DOCX, JPG, PNG allowed.";
    }
}

// Fetch assigned duties for this superintendent
$duties = $conn->query("
    SELECT d.id as duty_id, e.exam_name, e.exam_date, e.center, e.session, d.report_file
    FROM duties d
    JOIN exams e ON d.exam_id = e.id
    WHERE d.user_id = $user_id
    ORDER BY e.exam_date DESC
");
?>

<div class="table-container">
    <h2>Upload Attendance Report</h2>

    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <?php if (isset($error)) echo "<p class='msg error'>$error</p>"; ?>

    <table>
        <tr>
            <th>Exam</th>
            <th>Date</th>
            <th>Center</th>
            <th>Session</th>
            <th>Uploaded Report</th>
            <th>Upload New</th>
        </tr>

        <?php while ($row = $duties->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['exam_name']) ?></td>
                <td><?= htmlspecialchars($row['exam_date']) ?></td>
                <td><?= htmlspecialchars($row['center']) ?></td>
                <td><?= ucfirst($row['session']) ?></td>
                <td>
                    <?php if ($row['report_file']): ?>
                        <a href="uploads/reports/<?= $row['report_file'] ?>" target="_blank">View Report</a>
                    <?php else: ?>
                        Not uploaded
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="duty_id" value="<?= $row['duty_id'] ?>">
                        <input type="file" name="report" accept=".pdf,.doc,.docx,.jpg,.png" required>
                        <textarea name="remarks" placeholder="Enter remarks (optional)" rows="2"></textarea>
                        <button type="submit">Upload</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>