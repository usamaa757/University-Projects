<?php
include 'header.php';
include '../db.php';

$artist_id = $_SESSION['user_id'];

$query = "
    SELECT c.*, u.name AS commenter_name, a.title AS artwork_title
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    JOIN artworks a ON c.artwork_id = a.id
    WHERE a.artist_id = '$artist_id' AND c.parent_id IS NULL
    ORDER BY c.created_at DESC
";

$result = mysqli_query($conn, $query);
?>

<h2>Comments on Your Artworks</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
<div class="comment-block">
    <strong><?= htmlspecialchars($row['commenter_name']) ?></strong>
    on <em><?= htmlspecialchars($row['artwork_title']) ?></em>:
    <p><?= htmlspecialchars($row['content']) ?></p>
    <small><?= $row['created_at'] ?></small>

    <!-- Show replies -->
    <?php
        $cid = $row['comment_id'];
        $replies = mysqli_query($conn, "
            SELECT r.*, u.name AS replier_name 
            FROM comments r
            JOIN users u ON r.user_id = u.user_id
            WHERE r.parent_id = '$cid'
            ORDER BY r.created_at ASC
        ");
        while ($r = mysqli_fetch_assoc($replies)): ?>
    <div class="reply">
        <strong><?= htmlspecialchars($r['replier_name']) ?>:</strong>
        <?= htmlspecialchars($r['content']) ?>
        <small><?= $r['created_at'] ?></small>
    </div>
    <?php endwhile; ?>

    <!-- Reply form -->
    <form method="POST" action="reply_comment.php" class="reply-form">
        <input type="hidden" name="parent_id" value="<?= $cid ?>">
        <input type="hidden" name="artwork_id" value="<?= $row['artwork_id'] ?>">
        <textarea name="content" placeholder="Write a reply..." required></textarea>
        <button type="submit" class="btn">Reply</button>
    </form>
</div>
<?php endwhile; ?>