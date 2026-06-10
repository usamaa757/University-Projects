<?php
include 'navbar.php';
require 'db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch all assignments
$assignments_result = mysqli_query($conn, "
    SELECT a.id, a.title, a.description, a.category, a.due_date, u.full_name AS faculty_name
    FROM assignments a
    LEFT JOIN users u ON a.faculty_id = u.id
    ORDER BY a.due_date ASC
");

// Fetch all submissions with latest version and supervisor feedback
$submissions_result = mysqli_query($conn, "
    SELECT s.id AS submission_id, s.assignment_id, s.student_id, s.title AS submission_title, 
           u.full_name AS student_name, u.student_id AS student_code,
           sv.file_path, sv.original_filename,
           e.comments AS supervisor_feedback, e.status AS evaluation_status, e.commented_file_path
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    LEFT JOIN evaluations e ON e.submission_version_id = sv.id
    ORDER BY s.assignment_id, u.full_name
");
$submissions = [];
while ($row = mysqli_fetch_assoc($submissions_result)) {
    $submissions[$row['assignment_id']][] = $row;
}
?>

<div class="assignment-container">
    <h2>Assignments & Submissions (Admin View)</h2>
    <table>
        <thead>
            <tr>
                <th>Assignment Title</th>
                <th>Category</th>
                <th>Faculty</th>
                <th>Due Date</th>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Uploaded Paper</th>
                <th>Evaluation Status</th>
                <th>Supervisor Feedback</th>
                <th>Commented File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($a = mysqli_fetch_assoc($assignments_result)): ?>
            <?php
                $subs = isset($submissions[$a['id']]) ? $submissions[$a['id']] : [];
                if (count($subs) == 0): ?>
            <tr>
                <td><?php echo $a['title']; ?></td>
                <td><?php echo $a['category']; ?></td>
                <td><?php echo $a['faculty_name']; ?></td>
                <td><?php echo $a['due_date']; ?></td>
                <td colspan="6" style="text-align:center;">No submissions yet</td>
            </tr>
            <?php else: ?>
            <?php foreach ($subs as $sub): ?>
            <tr>
                <td><?php echo $a['title']; ?></td>
                <td><?php echo $a['category']; ?></td>
                <td><?php echo $a['faculty_name']; ?></td>
                <td><?php echo $a['due_date']; ?></td>
                <td><?php echo $sub['student_name']; ?></td>
                <td><?php echo $sub['student_code']; ?></td>
                <td>
                    <?php if (!empty($sub['file_path']) && file_exists($sub['file_path'])): ?>
                    <a href="<?php echo $sub['file_path']; ?>" target="_blank">
                        <?php echo $sub['original_filename']; ?>
                    </a>
                    <?php else: ?>
                    Not uploaded
                    <?php endif; ?>
                </td>
                <td><?php echo $sub['evaluation_status'] ?? 'Pending'; ?></td>
                <td><?php echo $sub['supervisor_feedback'] ?? '-'; ?></td>
                <td>
                    <?php if (!empty($sub['commented_file_path']) && file_exists($sub['commented_file_path'])): ?>
                    <a href="<?php echo $sub['commented_file_path']; ?>" target="_blank">Download</a>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>