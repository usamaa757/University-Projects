<?php
ob_start();
include '../db.php';
include 'header.php';

// Fetch comments along with discussion topic title and user details
$query = "
    SELECT comments.comment_id, comments.comment, comments.user_id, comments.created_at, 
           comments.is_flagged, comments.is_deleted, discussion_topics.title, users.name 
    FROM comments
    JOIN discussion_topics ON comments.topic_id = discussion_topics.topic_id
    JOIN users ON comments.user_id = users.user_id
    WHERE comments.is_deleted = 0
";
$result = $conn->query($query);
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Moderate Comments</h3>

    <!-- Comments Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>Comment ID</th>
                <th>Comment Text</th>
                <th>Discussion Topic</th>
                <th>User Name</th>
                <th>Created At</th>
                <th>Flagged</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['comment_id'] ?></td>
                <td><?= htmlspecialchars($row['comment']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td> <!-- Display the discussion topic -->
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if ($row['is_flagged']): ?>
                    <span class="badge bg-warning">Flagged</span>
                    <?php else: ?>
                    <span class="badge bg-success">Not Flagged</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!$row['is_deleted']): ?>
                    <!-- Flag Comment -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="comment_id" value="<?= $row['comment_id'] ?>">
                        <button type="submit" name="flag_comment" class="btn btn-sm btn-warning">Flag</button>
                    </form>
                    <!-- Delete Comment -->
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="comment_id" value="<?= $row['comment_id'] ?>">
                        <button type="submit" name="delete_comment" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                    <?php else: ?>
                    <span class="badge bg-secondary">Deleted</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
// Flag comment logic
if (isset($_POST['flag_comment'])) {
    $comment_id = $_POST['comment_id'];
    $stmt = $conn->prepare("UPDATE comments SET is_flagged = 1 WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_comments.php"); // Redirect to refresh the page
}

// Delete comment logic
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $stmt = $conn->prepare("UPDATE comments SET is_deleted = 1 WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_comments.php"); // Redirect to refresh the page
}
ob_end_clean();
?>