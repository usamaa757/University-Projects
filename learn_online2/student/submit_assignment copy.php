<?php
session_start();
if (!isset($_SESSION['student_email'])) {
    header("Location: ../../login/student_login.php");
    exit();
}

// Fetch assignments
include 'student_assingment_fetch_process.php';
include 'assignment_submission_process.php'; 

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
  
}
require('header.php');
?>
  
    <div class="container mt-5">
        <h1>Assignments for Course ID: <?php echo htmlspecialchars($course_id); ?></h1>
        <?php if (!empty($assignments)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Assignment File</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Uploaded At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td><a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download><?php echo htmlspecialchars($assignment['file_name']); ?></a></td>
                            <td><?php echo htmlspecialchars($assignment['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($assignment['uploaded_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php echo 'assignment not found'; ?></p>
        <?php endif; ?>

        <br><a href="upload_assignment.php?course_id=<?php echo htmlspecialchars($course_id); ?>">Back</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="../js/jquery-3.5.1.slim.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
