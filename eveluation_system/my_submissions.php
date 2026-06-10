<?php
include 'navbar.php';
require 'db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch all submissions by this student along with latest version
$submissions_result = mysqli_query($conn, "
    SELECT s.id AS submission_id, s.title AS submission_title, s.status AS submission_status,
           s.created_at AS submission_date, a.title AS assignment_title, a.category AS assignment_category,
           sv.id AS version_id, sv.file_path AS submission_file, sv.original_filename AS submission_filename
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    WHERE s.student_id = $student_id
    ORDER BY s.created_at DESC
");
?>

<div class="student-submissions-container">
    <h2>My Submissions</h2>
    <?php if (mysqli_num_rows($submissions_result) > 0): ?>
        <table class="student-submissions-table">
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Category</th>
                    <th>Submission Title</th>
                    <th>Submission Date</th>
                    <th>Uploaded File</th>
                    <th>Status</th>
                    <th>Faculty Feedback</th>
                    <th>Commented File</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($submissions_result)):
                    // Fetch latest evaluation for this version
                    $evaluation = [];
                    if ($row['version_id']) {
                        $eval_res = mysqli_query($conn, "SELECT * FROM evaluations WHERE submission_version_id={$row['version_id']} ORDER BY id DESC LIMIT 1");
                        $evaluation = mysqli_fetch_assoc($eval_res);
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['assignment_title']) ?></td>
                        <td><?= htmlspecialchars($row['assignment_category']) ?></td>
                        <td><?= htmlspecialchars($row['submission_title']) ?></td>
                        <td><?= date('d-m-Y', strtotime($row['submission_date'])) ?></td>
                        <td>
                            <?php if (!empty($row['submission_file']) && file_exists($row['submission_file'])): ?>
                                <a href="<?= htmlspecialchars($row['submission_file']) ?>" class="btn btn-green"
                                    target="_blank">Download</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['submission_status']) ?></td>
                        <td>
                            <?php
                            if (!empty($evaluation)) {
                                echo "<strong>Rating:</strong> " . htmlspecialchars($evaluation['overall_rating']) . "<br>";
                                echo "<strong>Comments:</strong> " . htmlspecialchars($evaluation['comments']);
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (!empty($evaluation['commented_file_path']) && file_exists($evaluation['commented_file_path'])) {
                                echo '<a href="' . htmlspecialchars($evaluation['commented_file_path']) . '" target="_blank" class="btn btn-blue">View</a>';
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="msg">You have not submitted any research papers yet.</p>
    <?php endif; ?>
</div>