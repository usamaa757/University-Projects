<?php
include 'header.php';
include '../db.php';
include '../user/parsedown/Parsedown.php';

$Parsedown = new Parsedown();
$msg = '';

if (!isset($_GET['topic_id']) || !is_numeric($_GET['topic_id'])) {
    $msg = "Post not found.";
    echo "<p>$msg</p><a href='topic_list.php'>Back</a>";
    exit;
}

$topic_id = intval($_GET['topic_id']);

$result = mysqli_query($conn, "SELECT title, content, created_at, user_id FROM discussion_topics WHERE topic_id = $topic_id");
if (!$result || mysqli_num_rows($result) == 0) {
    $msg = "Post not found.";
    echo "<p>$msg</p><a href='topic_list.php'>Back</a>";
    exit;
}
$row = mysqli_fetch_assoc($result);
$title = $row['title'];
$content = $row['content'];
$created_at = $row['created_at'];
$post_user_id = $row['user_id'];

$reply_to = $_GET['reply_to'] ?? null;

// Handle new comment or reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? null;
    $parent_comment_id = !empty($_POST['parent_comment_id']) ? intval($_POST['parent_comment_id']) : "NULL";

    if (!$user_id) {
        $error = "You must be logged in to comment.";
    } elseif (!empty($comment)) {
        $comment_escaped = mysqli_real_escape_string($conn, $comment);
        mysqli_query($conn, "INSERT INTO comments (topic_id, user_id, comment, created_at, parent_comment_id) VALUES ($topic_id, $user_id, '$comment_escaped', NOW(), $parent_comment_id)");
        $msg = "Comment added successfully!";
    } else {
        $error = "Comment cannot be empty.";
    }
}
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h4><?= htmlspecialchars($title) ?></h4>
        </div>
        <div class="card-body">
            <p><strong>Posted on:</strong> <?= date('d M, Y h:i A', strtotime($created_at)) ?></p>
            <?= $Parsedown->text($content) ?>
        </div>
    </div>

    <?php $msg = $_GET['msg'] ?? '';

    if (!empty($msg)): ?>
        <p class="text-success mt-2"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

      <?php $error = $_GET['error'] ?? '';

    if (!empty($error)): ?>
        <p class="text-danger mt-2"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <!-- Main Comment Form -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5><?= $reply_to ? 'Reply to Comment #' . intval($reply_to) : 'Post a Comment' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="parent_comment_id" value="<?= $reply_to ? intval($reply_to) : '' ?>">
                <div class="mb-3">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment or reply..."></textarea>
                </div>
                <button type="submit" class="btn btn-dark btn-sm">Post</button>
            </form>

            <?php
            // Fetch comments with basic PHP
            $comments_sql = "
                SELECT c.comment_id, c.user_id, c.comment, c.created_at,
                       c.is_flagged, c.flagged_reason, c.is_deleted, c.parent_comment_id,
                       u.name AS user_name,
                       (SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.comment_id AND vote_type = 'upvote') AS upvotes,
                       (SELECT COUNT(*) FROM comment_votes WHERE comment_id = c.comment_id AND vote_type = 'downvote') AS downvotes
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE c.topic_id = $topic_id
                ORDER BY c.created_at ASC
            ";
            $comments_result = mysqli_query($conn, $comments_sql);

            $comments_by_parent = [];
            while ($row = mysqli_fetch_assoc($comments_result)) {
                $comments_by_parent[$row['parent_comment_id']][] = $row;
            }

            function render_comments($parent_id = null, $depth = 0)
            {
                global $comments_by_parent, $Parsedown, $topic_id;

                if (isset($comments_by_parent[$parent_id])) {
                    foreach ($comments_by_parent[$parent_id] as $row) {
                        $id = $row['comment_id'];
                        $name = htmlspecialchars($row['user_name'] ?? 'Unknown');
                        $time = date('d M, Y h:i A', strtotime($row['created_at']));
                        $html = $Parsedown->text($row['comment']);
                        $up = $row['upvotes'];
                        $down = $row['downvotes'];
                        $flagged = $row['is_flagged'];
                        $deleted = $row['is_deleted'];
                        $reason = htmlspecialchars($row['flagged_reason'] ?? '');

                        echo "<div class='ms-" . ($depth * 4) . " p-3 border-start mb-2'>";
                        echo "<p><strong>$name</strong> <small>$time</small></p>";

                        if ($deleted) {
                            echo "<p class='text-muted fst-italic'>🗑️ This comment was deleted.</p>";
                        } else {
                            echo $html;
                            if ($flagged) {
                                echo "<p class='text-warning fst-italic'>⚠️ Flagged: $reason</p>";
                            }

                            echo "
                                <form method='POST' action='vote_comment.php' class='d-inline'>
                                    <input type='hidden' name='comment_id' value='$id'>
                                    <input type='hidden' name='topic_id' value='$topic_id'>
                                    <button type='submit' name='vote' value='upvote' class='btn btn-sm btn-success me-1'>👍 ($up)</button>
                                    <button type='submit' name='vote' value='downvote' class='btn btn-sm btn-danger'>👎 ($down)</button>
                                </form>
                                <a href='view_topics.php?topic_id=$topic_id&reply_to=$id' class='btn btn-sm btn-link'>Reply</a>
                            ";
                        }

                        echo "</div>";

                        // Recursively render replies
                        render_comments($id, $depth + 1);
                    }
                }
            }

            render_comments();
            ?>
        </div>
    </div>
</div>