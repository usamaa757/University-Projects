<?php
include 'navbar.php';
require 'db.php';

// Check if faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

$faculty_id = $_SESSION['user_id'];
$msg = '';
$error = '';

// Get assignment ID
if (!isset($_GET['assignment_id'])) {
    die('Assignment ID required');
}
$assignment_id = intval($_GET['assignment_id']);

// Fetch assignment details
$assignment = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM assignments WHERE id=$assignment_id AND faculty_id=$faculty_id"));
if (!$assignment) die('Assignment not found');

// Fetch submissions for this assignment along with latest submission file
$submissions_result = mysqli_query($conn, "
    SELECT s.id AS submission_id, s.title, s.status, s.created_at, u.full_name AS student_name,
           sv.file_path, sv.original_filename
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    LEFT JOIN submission_versions sv ON sv.id = (
        SELECT id FROM submission_versions
        WHERE submission_id = s.id
        ORDER BY version_no DESC LIMIT 1
    )
    WHERE s.assignment_id = $assignment_id
    ORDER BY s.created_at DESC
");

// Handle evaluation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
    $submission_id = intval($_POST['submission_id']);
    $status = $_POST['status'];
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    $overall_rating = intval($_POST['overall_rating']);

    // Handle commented file upload
    $commented_file_path = null;
    if (isset($_FILES['commented_file']) && $_FILES['commented_file']['error'] === 0) {
        $upload_dir = 'uploads/evaluations/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['commented_file']['name']);
        $target_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['commented_file']['tmp_name'], $target_path)) {
            $commented_file_path = $target_path;
        } else {
            $error = "Failed to upload commented file.";
        }
    }

    // Get latest submission version
    $version = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM submission_versions WHERE submission_id=$submission_id ORDER BY version_no DESC LIMIT 1"));
    if ($version && empty($error)) {
        $version_id = $version['id'];

        // Check if an evaluation already exists for this version
        $existing_eval = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM evaluations WHERE submission_version_id=$version_id AND evaluator_id=$faculty_id ORDER BY id DESC LIMIT 1"));

        if ($existing_eval) {
            // Update existing evaluation
            $update_sql = "UPDATE evaluations SET overall_rating=$overall_rating, comments='$comments', status='$status'";
            if ($commented_file_path) $update_sql .= ", commented_file_path='$commented_file_path'";
            $update_sql .= " WHERE id=" . $existing_eval['id'];
            mysqli_query($conn, $update_sql);
            mysqli_query($conn, "UPDATE submissions SET status='$status' WHERE id=$submission_id");
            $msg = "Evaluation updated successfully.";
        } else {
            // Insert new evaluation
            mysqli_query($conn, "INSERT INTO evaluations (submission_version_id, evaluator_id, overall_rating, comments, status, commented_file_path) VALUES ($version_id, $faculty_id, $overall_rating, '$comments', '$status', '" . ($commented_file_path ? $commented_file_path : '') . "')");
            mysqli_query($conn, "UPDATE submissions SET status='$status' WHERE id=$submission_id");
            $msg = "Evaluation submitted successfully.";
        }
    } elseif (!$version) {
        $error = "Submission version not found.";
    }
}
?>

<div class="assignment-container">
    <h2>Submissions for: <?php echo $assignment['title']; ?></h2>

    <?php if (!empty($msg)) echo '<p class="msg">' . $msg . '</p>'; ?>
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>

    <div class="submissions-grid">
        <?php while ($sub = mysqli_fetch_assoc($submissions_result)) {
            // Fetch latest evaluation if exists
            $version = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM submission_versions WHERE submission_id=" . $sub['submission_id'] . " ORDER BY version_no DESC LIMIT 1"));
            $eval = null;
            if ($version) {
                $eval = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM evaluations WHERE submission_version_id=" . $version['id'] . " AND evaluator_id=$faculty_id ORDER BY id DESC LIMIT 1"));
            }
            $saved_status = $eval['status'] ?? '';
            $saved_rating = $eval['overall_rating'] ?? '';
            $saved_comments = $eval['comments'] ?? '';
            $saved_file = $eval['commented_file_path'] ?? '';
        ?>
            <div class="submission-card">
                <h3><?php echo $sub['title']; ?></h3>
                <p><strong>Student:</strong> <?php echo $sub['student_name']; ?></p>
                <p><strong>Status:</strong> <?php echo $sub['status']; ?></p>
                <p><strong>Submitted On:</strong> <?php echo $sub['created_at']; ?></p>
                <p><strong>Uploaded File:</strong>
                    <?php if (!empty($sub['file_path']) && file_exists($sub['file_path'])) { ?>
                        <a href="<?php echo $sub['file_path']; ?>" target="_blank">
                            <?php echo $sub['original_filename']; ?>
                        </a>
                    <?php } else {
                        echo 'No file uploaded';
                    } ?>
                </p>
                <form action="" method="POST" enctype="multipart/form-data" class="eval-form">
                    <input type="hidden" name="submission_id" value="<?php echo $sub['submission_id']; ?>">

                    <label>Status</label>
                    <select name="status" required>
                        <option value="Accepted" <?php if ($saved_status == 'Accepted') echo 'selected'; ?>>Accepted
                        </option>
                        <option value="Rejected" <?php if ($saved_status == 'Rejected') echo 'selected'; ?>>Rejected
                        </option>
                        <option value="Needs Improvement"
                            <?php if ($saved_status == 'Needs Improvement') echo 'selected'; ?>>Needs Improvement</option>
                        <option value="Accepted and Published"
                            <?php if ($saved_status == 'Accepted and Published') echo 'selected'; ?>>Accepted and Published
                        </option>
                    </select>

                    <label>Rating (1-5)</label>
                    <input type="number" name="overall_rating" min="1" max="5" value="<?php echo $saved_rating; ?>"
                        required>

                    <label>Comments</label>
                    <textarea name="comments" placeholder="Comments"
                        required><?php echo htmlspecialchars($saved_comments); ?></textarea>

                    <label>Upload Commented File (Optional)</label>
                    <input type="file" name="commented_file">
                    <?php if (!empty($saved_file) && file_exists($saved_file)) { ?>
                        <p>Existing file: <a href="<?php echo $saved_file; ?>" target="_blank">View</a></p>
                    <?php } ?>

                    <button type="submit" class="btn btn-green">Evaluate</button>
                </form>
            </div>
        <?php } ?>
    </div>
</div>