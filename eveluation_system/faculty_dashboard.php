<?php
include 'navbar.php';
require 'db.php';

// Check if faculty is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header('Location: login.php');
    exit;
}

$faculty_id = $_SESSION['user_id'];

// Fetch faculty assignments
$assignments_result = mysqli_query($conn, "
    SELECT a.id, a.title, a.description, a.due_date, a.category
    FROM assignments a
    WHERE a.faculty_id = $faculty_id
    ORDER BY a.created_at DESC
");
?>

<div class="assignment-container">
    <h2>My Assignments</h2>
    <a class="btn btn-green" href="create_assignment.php">+ Create New Assignment</a>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($assignment = mysqli_fetch_assoc($assignments_result)) { ?>
            <tr>
                <td><?php echo $assignment['title']; ?></td>
                <td><?php echo $assignment['category']; ?></td>
                <td><?php echo $assignment['due_date']; ?></td>
                <td>
                    <a class="btn btn-grey"
                        href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>">View
                        Submissions</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>

</html>