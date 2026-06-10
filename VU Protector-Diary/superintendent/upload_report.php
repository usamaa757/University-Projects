<?php
include '../header/superintendent-header.php';
include '../connection.php';

$superintendent_id = $_SESSION['id'];

// Fetch exams assigned to this superintendent
$exams = mysqli_query($con, "
    SELECT a.assignment_id, e.exam_id, e.exam_name, e.exam_date, e.center
    FROM assignments a
    JOIN exams e ON a.exam_id = e.exam_id
    WHERE a.user_id='$superintendent_id'
    ORDER BY e.exam_date ASC
");

if (isset($_POST['upload'])) {
    $exam_id = intval($_POST['exam_id']);

    // Handle file upload
    if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] == 0) {
        $file_name = $_FILES['report_file']['name'];
        $file_tmp  = $_FILES['report_file']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed   = ['pdf', 'doc', 'docx', 'xlsx', 'csv'];

        if (in_array($file_ext, $allowed)) {
            $new_file_name = time() . '_' . $file_name;
            $upload_path = 'upload/reports/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Save in DB
                $stmt = $con->prepare("INSERT INTO reports (superintendent_id, exam_id, report_file) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $superintendent_id, $exam_id, $new_file_name);
                $stmt->execute();
                echo "<script>alert('Report uploaded successfully!'); window.history.back();</script>";
            } else {
                echo "<script>alert('Failed to move uploaded file!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid file type! Allowed: pdf, doc, docx, xlsx, csv'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Please select a file!'); window.history.back();</script>";
    }
}
?>

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="text-center mb-4">Upload Exam Report</h3>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select Exam</label>
                <select name="exam_id" class="form-control" required>
                    <option value="">Select Exam</option>
                    <?php while ($exam = mysqli_fetch_assoc($exams)): ?>
                    <option value="<?= $exam['exam_id'] ?>">
                        <?= $exam['exam_name'] ?> | <?= $exam['exam_date'] ?> | <?= $exam['center'] ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Report File</label>
                <input type="file" name="report_file" class="form-control" required>
                <small class="text-muted">Allowed formats: pdf, doc, docx, xlsx, csv</small>
            </div>

            <button type="submit" name="upload" class="btn btn-success">Upload Report</button>
        </form>
    </div>
</div>

<?php
// Fetch only reports uploaded by this superintendent
$reports = mysqli_query($con, "
    SELECT r.report_id, r.report_file, r.uploaded_at,
           e.exam_name, e.exam_date, e.center
    FROM reports r
    JOIN exams e ON r.exam_id = e.exam_id
    WHERE r.superintendent_id='$superintendent_id'
    ORDER BY r.uploaded_at DESC
");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Uploaded Exam Reports</h3>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Exam</th>
                        <th>Date</th>
                        <th>Center</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($reports) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($reports)): ?>
                    <tr>
                        <td><?= $row['exam_name'] ?></td>
                        <td><?= $row['exam_date'] ?></td>
                        <td><?= $row['center'] ?></td>
                        <td><?= $row['uploaded_at'] ?></td>
                        <td>
                            <a href="upload/reports/<?= $row['report_file'] ?>" target="_blank"
                                class="btn btn-primary btn-sm">
                                Download
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No reports uploaded yet</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>