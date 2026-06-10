<?php
include 'navbar.php';
require 'db.php';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}

$student_id = $_SESSION['user_id'];

// Fetch all assignments
$assignments_result = mysqli_query($conn, "
    SELECT a.id, a.title, a.description, a.category, a.due_date, u.full_name AS faculty_name
    FROM assignments a
    LEFT JOIN users u ON a.faculty_id = u.id
    ORDER BY a.due_date ASC
");

// Fetch student's submissions
$submissions_result = mysqli_query($conn, "
    SELECT assignment_id, status FROM submissions WHERE student_id = $student_id
");

$submission_status = [];
while ($row = mysqli_fetch_assoc($submissions_result)) {
    $submission_status[$row['assignment_id']] = $row['status'];
}
?>

<div class="assignment-container">
    <h2>Available Assignments</h2>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Faculty</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($a = mysqli_fetch_assoc($assignments_result)):

                $status = isset($submission_status[$a['id']]) ? $submission_status[$a['id']] : 'Not Submitted';
                $can_submit = (strtotime($a['due_date']) >= time()) && $status != 'Accepted and Published';

            ?>
            <tr>
                <td><?php echo $a['title']; ?></td>
                <td><?php echo $a['category']; ?></td>
                <td><?php echo $a['faculty_name']; ?></td>
                <td><?php echo $a['due_date']; ?></td>
                <td><?php echo $status; ?></td>
                <td>
                    <?php

                        if ($can_submit) { ?>
                    <a class="btn btn-blue" href="submit_research.php?assignment_id=<?php echo $a['id']; ?>">Submit
                        Paper</a>
                    <?php } else { ?>
                    <span class="btn btn-grey">Deadline Passed</span>
                    <?php } ?>
                </td>

            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>

</html>