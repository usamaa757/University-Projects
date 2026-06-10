<?php
include 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch student's submissions along with latest research file and latest evaluation
$submissions_result = mysqli_query($conn, "
    SELECT s.id AS submission_id, s.title, s.status, s.created_at,
           a.title AS assignment_title, a.category,
           u.full_name AS supervisor_name,
           sv.file_path AS research_file, sv.original_filename AS research_filename,
           e.commented_file_path AS feedback_file, e.comments AS feedback_comments
    FROM submissions s
    JOIN assignments a ON s.assignment_id = a.id
    LEFT JOIN users u ON s.selected_supervisor_id = u.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    LEFT JOIN evaluations e ON e.submission_version_id = sv.id
    WHERE s.student_id = $student_id
    ORDER BY s.created_at DESC
");
?>

<div class="assignment-container">
    <h2>My Research Submissions</h2>
    <table>
        <thead>
            <tr>
                <th>Assignment</th>
                <th>Title</th>
                <th>Category</th>
                <th>Supervisor</th>
                <th>Status</th>
                <th>Submitted On</th>
                <th>Research File</th>
                <th>Feedback</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($submission = mysqli_fetch_assoc($submissions_result)) { ?>
            <tr>
                <td><?php echo $submission['assignment_title']; ?></td>
                <td><?php echo $submission['title']; ?></td>
                <td><?php echo $submission['category']; ?></td>
                <td><?php echo $submission['supervisor_name']; ?></td>
                <td><?php echo $submission['status']; ?></td>
                <td><?php echo $submission['created_at']; ?></td>
                <td>
                    <?php if (!empty($submission['research_file']) && file_exists($submission['research_file'])): ?>
                    <a class="btn btn-grey" href="<?php echo $submission['research_file']; ?>" target="_blank">
                        Research File
                    </a>
                    <?php else: ?>
                    Not uploaded
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($submission['feedback_file']) && file_exists($submission['feedback_file'])): ?>
                    <a class="btn btn-grey" href="<?php echo $submission['feedback_file']; ?>" target="_blank">
                        Feedback
                    </a>
                    <?php elseif (!empty($submission['feedback_comments'])): ?>
                    <span>Comments Available</span>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-blue"
                        href="upload_submission.php?id=<?php echo $submission['submission_id']; ?>">Upload/Update</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>