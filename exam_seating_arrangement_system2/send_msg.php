<?php
include 'db.php';
include 'header.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
// Fetch available courses
$courses = $conn->query("SELECT course_id, course_name FROM courses");
?>

<div class="container mt-4">
    <h3>Send Seating Emails to Students</h3>
    <form action="msg_process.php" method="POST">
        <div class="mb-3">
            <label for="courses" class="form-label">Select Courses</label>
            <select name="courses[]" id="courses" class="form-select" multiple required>
                <?php while ($row = $courses->fetch_assoc()): ?>
                <option value="<?= $row['course_id'] ?>"><?= htmlspecialchars($row['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</small>
        </div>

        <div class="mb-3">
            <label for="admin_message" class="form-label">Admin Message</label>
            <textarea name="admin_message" id="admin_message" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send Emails</button>
    </form>
</div>