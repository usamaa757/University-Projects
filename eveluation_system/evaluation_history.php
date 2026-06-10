<?php
include 'navbar.php';
require 'db.php';

// Ensure faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

$faculty_id = $_SESSION['user_id'];

// Fetch all assignments by this faculty
$assignments_result = mysqli_query($conn, "
    SELECT * FROM assignments WHERE faculty_id=$faculty_id ORDER BY due_date ASC
");

// Fetch submissions and their latest evaluation for all students under this faculty
$submissions_query = "
    SELECT s.id AS submission_id, s.title AS submission_title, s.status AS submission_status,
           s.created_at AS submission_date, u.full_name AS student_name, u.student_id,
           sv.id AS version_id, sv.file_path AS submission_file, sv.original_filename AS submission_filename,
           e.overall_rating, e.comments AS evaluation_comments, e.status AS evaluation_status,
           e.commented_file_path
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions 
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    LEFT JOIN evaluations e ON e.submission_version_id = sv.id AND e.evaluator_id=$faculty_id
    WHERE s.assignment_id IN (SELECT id FROM assignments WHERE faculty_id=$faculty_id)
    ORDER BY s.created_at DESC
";
$submissions_result = mysqli_query($conn, $submissions_query);

?>

<div class="faculty-report-container">
    <h2>Past Submissions and Evaluations</h2>
    <table class="faculty-report-table">
        <thead>
            <tr>
                <th>Assignment</th>
                <th>Student</th>
                <th>Student ID</th>
                <th>Submission Title</th>
                <th>Submission Date</th>
                <th>Submission File</th>
                <th>Submission Status</th>
                <th>Evaluation Status</th>
                <th>Rating</th>
                <th>Comments</th>
                <th>Commented File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($submissions_result)): ?>
            <tr>
                <td><?php
                        $assignment_title = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM assignments WHERE id=(SELECT assignment_id FROM submissions WHERE id={$row['submission_id']})"));
                        echo htmlspecialchars($assignment_title['title']);
                        ?></td>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['submission_title']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['submission_date'])) ?></td>
                <td>
                    <?php if (!empty($row['submission_file']) && file_exists($row['submission_file'])): ?>
                    <a href="<?= htmlspecialchars($row['submission_file']) ?>"
                        target="_blank"><?= htmlspecialchars($row['submission_filename']) ?></a>
                    <?php else: ?>
                    N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['submission_status']) ?></td>
                <td><?= htmlspecialchars($row['evaluation_status'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['overall_rating'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['evaluation_comments'] ?? '-') ?></td>
                <td>
                    <?php if (!empty($row['commented_file_path']) && file_exists($row['commented_file_path'])): ?>
                    <a href="<?= htmlspecialchars($row['commented_file_path']) ?>" target="_blank">View</a>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
