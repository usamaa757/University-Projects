<?php
include '../db.php';
include 'header.php';
$msg = "";
// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $topic_id = (int) $_POST['topic_id'];

    $conn->begin_transaction();

    try {
        // 1. Delete comment votes related to topic's comments
        $stmt1 = $conn->prepare("DELETE cv FROM comment_votes cv
            JOIN comments c ON cv.comment_id = c.comment_id
            WHERE c.topic_id = ?");
        $stmt1->bind_param("i", $topic_id);
        $stmt1->execute();
        $stmt1->close();

        // 2. Delete comments related to the topic
        $stmt2 = $conn->prepare("DELETE FROM comments WHERE topic_id = ?");
        $stmt2->bind_param("i", $topic_id);
        $stmt2->execute();
        $stmt2->close();

        // 3. Delete from topic_tags
        $stmt3 = $conn->prepare("DELETE FROM topic_tags WHERE topic_id = ?");
        $stmt3->bind_param("i", $topic_id);
        $stmt3->execute();
        $stmt3->close();

        // 4. Finally, delete the topic itself
        $stmt4 = $conn->prepare("DELETE FROM discussion_topics WHERE topic_id = ?");
        $stmt4->bind_param("i", $topic_id);
        $stmt4->execute();
        $stmt4->close();

        $conn->commit();
        $msg = 'Topic data deleted successfully.';
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Failed to delete topic:' . addslashes($e->getMessage());
    }
}
// Fetch approved posts
$approved = $conn->query("
    SELECT discussion_topics.*, users.name 
    FROM discussion_topics 
    JOIN users ON discussion_topics.user_id = users.user_id 
");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Manage Discussion Posts</h3>

    <?php if (!empty($msg)): ?>
        <p class="text-success text-center"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php if ($approved->num_rows > 0): ?>
                <?php while ($row = $approved->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="topic_id" value="<?= $row['topic_id'] ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No approved posts.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>