<?php
include '../db.php';
include 'header.php';

$msg = "";
$error = "";

// Flag comment logic
if (isset($_POST['flag_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $flagged_reason = mysqli_real_escape_string($conn, trim($_POST['flagged_reason']));

    $sql = "UPDATE comments SET is_flagged = 1, flagged_reason = '$flagged_reason' WHERE comment_id = $comment_id";
    if (mysqli_query($conn, $sql)) {
        $msg = "Comment ID $comment_id has been flagged.";
    } else {
        $error = "Failed to flag comment ID $comment_id.";
    }
}

// Delete comment logic (soft delete)
if (isset($_POST['delete_comment'])) {
    $comment_id = intval($_POST['comment_id']);

    $sql = "UPDATE comments SET is_deleted = 1 WHERE comment_id = $comment_id";
    if (mysqli_query($conn, $sql)) {
        $msg = "Comment ID $comment_id has been deleted.";
    } else {
        $error = "Failed to delete comment ID $comment_id.";
    }
}

// Fetch non-deleted comments
$query = "SELECT * FROM comments WHERE is_deleted = 0 ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Moderate Comments</h3>

    <?php if (!empty($msg)): ?>
        <p class="text-success text-center"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="text-danger text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Comments Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Comment</th>
                <th>User ID</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['comment_id'] ?></td>
                    <td><?= htmlspecialchars($row['comment']) ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                        <?= $row['is_flagged'] ? '<span class="badge bg-warning">Flagged</span>' : '<span class="badge bg-success">OK</span>' ?>
                    </td>
                    <td>
                        <!-- Flag Form -->
                        <?php if (!$row['is_flagged']): ?>
                            <form method="post" class="mb-1">
                                <input type="hidden" name="comment_id" value="<?= $row['comment_id'] ?>">
                                <input type="text" name="flagged_reason" placeholder="Flag reason" class="form-control mb-1" required>
                                <button type="submit" name="flag_comment" class="btn btn-warning btn-sm w-100">Flag</button>
                            </form>
                        <?php endif; ?>

                        <!-- Delete Form -->
                        <form method="post">
                            <input type="hidden" name="comment_id" value="<?= $row['comment_id'] ?>">
                            <button type="submit" name="delete_comment" class="btn btn-danger btn-sm w-100">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
