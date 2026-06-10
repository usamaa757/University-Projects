<?php
include 'navbar.php';
require 'db.php';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
$student_id = $_SESSION['user_id'];

$msg = '';
$error = '';

// Fetch active assignments (not past due date) along with supervisor
$assignments_result = mysqli_query($conn, "
    SELECT a.id, a.title, a.description, a.due_date, a.faculty_id, u.full_name AS faculty_name
    FROM assignments a
    LEFT JOIN users u ON a.faculty_id = u.id
    WHERE a.due_date >= NOW()
    ORDER BY a.due_date ASC
");

// Fetch existing submissions to show status
$my_submissions = [];
$sub_res = mysqli_query($conn, "
    SELECT * FROM submissions WHERE student_id = $student_id
");
while ($row = mysqli_fetch_assoc($sub_res)) {
    $my_submissions[$row['assignment_id']] = $row; // keyed by assignment_id
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'])) {
    $assignment_id = intval($_POST['assignment_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $abstract = mysqli_real_escape_string($conn, $_POST['abstract']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);

    // Get supervisor from assignment automatically
    $assignment_res = mysqli_query($conn, "SELECT faculty_id FROM assignments WHERE id=$assignment_id");
    $assignment_data = mysqli_fetch_assoc($assignment_res);
    $supervisor_id = $assignment_data['faculty_id'];

    // File upload validation
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
                // Insert into submissions table or update if already exists
                $existing = mysqli_query($conn, "SELECT id FROM submissions WHERE assignment_id=$assignment_id AND student_id=$student_id");
                if (mysqli_num_rows($existing) > 0) {
                    $sub = mysqli_fetch_assoc($existing);
                    $submission_id = $sub['id'];
                    mysqli_query($conn, "
                        INSERT INTO submission_versions (submission_id, version_no, file_path, original_filename)
                        VALUES ($submission_id, (SELECT IFNULL(MAX(version_no),0)+1 FROM submission_versions WHERE submission_id=$submission_id), '$target_path', '$file_name')
                    ");
                    $msg = "Submission updated successfully.";
                } else {
                    mysqli_query($conn, "
                        INSERT INTO submissions (assignment_id, student_id, title, abstract, keywords, selected_supervisor_id)
                        VALUES ($assignment_id, $student_id, '$title', '$abstract', '$keywords', $supervisor_id)
                    ");
                    $submission_id = mysqli_insert_id($conn);
                    mysqli_query($conn, "
                        INSERT INTO submission_versions (submission_id, version_no, file_path, original_filename)
                        VALUES ($submission_id, 1, '$target_path', '$file_name')
                    ");
                    $msg = "Research paper submitted successfully.";
                }
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "Please select a research paper to upload.";
    }
}
?>

<div class="submission-container">
    <h2>Submit Research Paper</h2>
    <?php if (!empty($msg)) echo '<p class="msg">' . $msg . '</p>'; ?>
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>

    <?php while ($a = mysqli_fetch_assoc($assignments_result)) {
        $status = isset($my_submissions[$a['id']]) ? $my_submissions[$a['id']]['status'] : 'Not Submitted';
    ?>
    <div class="assignment">
        <h3><?php echo $a['title']; ?></h3>
        <p><strong>Description:</strong> <?php echo $a['description']; ?></p>
        <p><strong>Due Date:</strong> <?php echo $a['due_date']; ?></p>
        <p><strong>Supervisor:</strong> <?php echo $a['faculty_name']; ?></p>
        <p><strong>Status:</strong> <?php echo $status; ?></p>

        <?php if (strtotime($a['due_date']) >= time()) { ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>">
            <label for="title_<?php echo $a['id']; ?>">Paper Title</label>
            <input type="text" name="title" id="title_<?php echo $a['id']; ?>" required>

            <label for="abstract_<?php echo $a['id']; ?>">Abstract</label>
            <textarea name="abstract" id="abstract_<?php echo $a['id']; ?>" rows="4" required></textarea>

            <label for="keywords_<?php echo $a['id']; ?>">Keywords</label>
            <input type="text" name="keywords" id="keywords_<?php echo $a['id']; ?>" required>

            <label for="research_file_<?php echo $a['id']; ?>">Upload Research Paper (PDF/DOC/DOCX)</label>
            <input type="file" name="research_file" id="research_file_<?php echo $a['id']; ?>" required>

            <button type="submit">Submit Paper</button>
        </form>
        <?php } else { ?>
        <p style="color:red; font-weight:bold;">Deadline Passed</p>
        <?php } ?>
    </div>
    <?php } ?>

    <div class="submissions">
        <h3>My Submissions</h3>
        <table>
            <thead>
                <tr>
                    <th>Assignment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($my_submissions as $sub) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($sub['assignment_id']); ?></td>
                    <td><?php echo $sub['status']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>

</html>