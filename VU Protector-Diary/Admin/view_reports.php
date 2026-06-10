<?php
include '../header/admin-header.php';
include '../connection.php';

// Fetch all uploaded reports along with exam and superintendent info
$reports = mysqli_query($con, "
    SELECT r.report_id, r.report_file, r.uploaded_at,
           u.name AS superintendent_name,
           e.exam_name, e.exam_date, e.center
    FROM reports r
    JOIN user u ON r.superintendent_id = u.id
    JOIN exams e ON r.exam_id = e.exam_id
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
                        <th>Superintendent</th>
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
                        <td><?= $row['superintendent_name'] ?></td>
                        <td><?= $row['exam_name'] ?></td>
                        <td><?= $row['exam_date'] ?></td>
                        <td><?= $row['center'] ?></td>
                        <td><?= $row['uploaded_at'] ?></td>
                        <td>
                            <a href="../superintendent/upload/reports/<?= $row['report_file'] ?>" target="_blank"
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