<?php
include 'header.php';
include '../db.php';
$error = $msg = "";
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id FROM discussion_topics d JOIN users u ON d.user_id = u.user_id  WHERE d.user_id = $user_id ORDER BY d.created_at DESC");
$categories = $conn->query("SELECT category_id, category_name FROM categories");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_topic'])) {
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





?>

<div class="container mt-5 rounded shadow-sm p-4 border">


    <h3 class="fw-bold text-center mb-3"> MY Discussion Threads</h3>




    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered text-center">
            <?php if ($msg): ?>
                <p class="text-success text-center"><?= $msg ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="text-danger text-center"><?= $error ?></p>
            <?php endif; ?>

            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>

                        <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="view_topics.php?topic_id=<?= $row['topic_id'] ?>" class="btn btn-sm btn-success">View</a>
                            <!-- Edit Button (link to edit page) -->
                            <a href="edit_post.php?topic_id=<?= $row['topic_id'] ?>" class="btn btn-sm btn-primary">Edit</a>

                            <!-- Delete Form -->
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="topic_id" value="<?= $row['topic_id'] ?>">
                                <button type="submit" name="delete_topic" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-danger">You haven't posted anything yet.</p>
    <?php endif; ?>
</div>