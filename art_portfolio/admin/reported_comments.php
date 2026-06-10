<?php
include '../db.php';
include 'header.php';
$message = $error = '';
// Get reported comments
$result = mysqli_query($conn, "
    SELECT c.*, u.name AS commenter, a.title AS artwork_title
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    JOIN artworks a ON c.artwork_id = a.id
    WHERE c.reported = 1
    ORDER BY c.created_at DESC
");
if (isset($_GET['ignore']) && isset($_GET['comment_id'])) {

    $id = $_GET['comment_id'];
    if (mysqli_query($conn, "UPDATE comments SET reported = 0 WHERE comment_id = '$id'")) {
        $message = "Comment ignore successfully!";
    } else {
        $error = "Database error.";
    }
}
if (isset($_GET['delete']) && isset($_GET['comment_id'])) {

    $id = $_GET['comment_id'];

    if (mysqli_query($conn, "DELETE FROM comments  WHERE comment_id = '$id'")) {
        $message = "Comment deleted successfully!";
    } else {
        $error = "Database error.";
    }
}
?>

<h2>Reported Comments</h2>
<?php if ($message): ?>
<p class="message"><?php echo $message; ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>Commenter</th>
        <th>Artwork</th>
        <th>Comment</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= htmlspecialchars($row['commenter']) ?></td>
        <td><?= htmlspecialchars($row['artwork_title']) ?></td>
        <td><?= htmlspecialchars($row['content']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
            <a href="reported_comments.php?comment_id=<?= $row['comment_id'] . '&delete' ?>" class="btn">Delete</a>
            <a href="reported_comments.php?comment_id=<?= $row['comment_id'] . '&ignore' ?>" class="btn">Ignore</a>

        </td>
    </tr>
    <?php endwhile; ?>
</table>