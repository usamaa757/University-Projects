<?php
include 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];
$msg = '';
$error = '';

if (!isset($_GET['id'])) {
    die("Submission ID required.");
}

$submission_id = intval($_GET['id']);

// Fetch existing submission
$submission = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM submissions WHERE id=$submission_id AND student_id=$student_id"));
if (!$submission) die("Submission not found.");

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['research_file']) && $_FILES['research_file']['error'] === 0) {
        $allowed_ext = ['pdf', 'doc', 'docx'];
        $file_name = $_FILES['research_file']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $file_tmp = $_FILES['research_file']['tmp_name'];

        if (!in_array($file_ext, $allowed_ext)) {
            $error = "Only PDF or MS Word files are allowed.";
        } else {
            $upload_dir = 'uploads/research/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $new_file_name = time() . '_' . $file_name;
            $target_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                // Insert new submission version
                $max_version = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(MAX(version_no),0) as max_ver FROM submission_versions WHERE submission_id=$submission_id"));
                $new_version = $max_version['max_ver'] + 1;

                mysqli_query($conn, "
                    INSERT INTO submission_versions (submission_id, version_no, file_path, original_filename)
                    VALUES ($submission_id, $new_version, '$target_path', '$file_name')
                ");

                // Update submission status to Submitted
                mysqli_query($conn, "UPDATE submissions SET status='Submitted' WHERE id=$submission_id");

                $msg = "Submission uploaded successfully (Version $new_version).";
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "Please select a research paper to upload.";
    }
}

// Fetch all versions
$versions = mysqli_query($conn, "SELECT * FROM submission_versions WHERE submission_id=$submission_id ORDER BY version_no DESC");

?>

<div class="submission-container">
    <h2>Upload / Update Research Paper</h2>

    <?php if (!empty($msg)) echo '<p class="msg">' . $msg . '</p>'; ?>
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Upload Research Paper (PDF/DOC/DOCX)</label>
        <input type="file" name="research_file" required>
        <button type="submit" class="btn btn-green">Upload</button>
    </form>

    <h3>Previous Versions</h3>
    <table>
        <thead>
            <tr>
                <th>Version</th>
                <th>Uploaded On</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ver = mysqli_fetch_assoc($versions)) { ?>
            <tr>
                <td><?php echo $ver['version_no']; ?></td>
                <td><?php echo $ver['uploaded_at']; ?></td>
                <td>
                    <?php if (!empty($ver['file_path']) && file_exists($ver['file_path'])) { ?>
                    <a href="<?php echo $ver['file_path']; ?>"
                        target="_blank"><?php echo $ver['original_filename']; ?></a>
                    <?php } else {
                            echo 'File not found';
                        } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>