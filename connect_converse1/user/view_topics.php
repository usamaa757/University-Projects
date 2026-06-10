<?php
include 'header.php';
include '../db.php';
include 'parsedown/Parsedown.php';

$Parsedown = new Parsedown();

// Validate topic_id from GET
if (!isset($_GET['topic_id']) || !is_numeric($_GET['topic_id'])) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}

$topic_id = intval($_GET['topic_id']);

// Fetch topic/post details
$stmt = $conn->prepare("SELECT title, content, created_at, user_id FROM discussion_topics WHERE topic_id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}

$stmt->bind_result($title, $content, $created_at, $post_user_id);
$stmt->fetch();
$stmt->close();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo "<script>alert('You must be logged in to comment.');</script>";
    } elseif ($comment) {
        $stmt = $conn->prepare("INSERT INTO comments (topic_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $topic_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();

        // Fetch the email of the post owner (user who created the topic)
        $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $post_user_id);
        $stmt->execute();
        $stmt->bind_result($post_owner_email);
        $stmt->fetch();
        $stmt->close();

        // Send email notification to the post owner
        if ($post_owner_email) {
            $subject = "New Comment on Your Post: " . htmlspecialchars($title);
            $message = "
                <html>
                <head>
                    <title>New Comment on Your Post</title>
                </head>
                <body>
                    <p>Hello,</p>
                    <p>A new comment has been posted on your discussion titled <strong>'$title'</strong>.</p>
                    <p>Click <a href='view_topics.php?topic_id=$topic_id'>here</a> to view the comment.</p>
                    <p>Best Regards,</p>
                    <p>Your Forum Team</p>
                </body>
                </html>
            ";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: no-reply@forum.com" . "\r\n";

            // Use PHP's mail function to send the email
            mail($post_owner_email, $subject, $message, $headers);
        }

        // Show success message and redirect back to the same post
        echo "<script>alert('Comment added successfully!'); window.location.href='view_topics.php?topic_id=$topic_id';</script>";
        exit;
    } else {
        echo "<script>alert('Comment cannot be empty.');</script>";
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

    <!-- Comments and Add Comment Section -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5>Comments</h5>
        </div>
        <div class="card-body">

            <!-- Add Comment Form -->
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..."></textarea>
                </div>
                <button type="submit" class="btn btn-dark btn-sm">Post Comment</button>
            </form>

            <?php
            // Fetch all comments for this topic along with user name and vote counts
            $query = "
                SELECT c.comment_id, c.user_id, c.comment, c.created_at, 
                       c.is_flagged, c.flagged_reason, c.is_deleted,
                       u.name AS user_name,
                       COALESCE(SUM(v.vote_type = 'upvote'), 0) AS upvotes,
                       COALESCE(SUM(v.vote_type = 'downvote'), 0) AS downvotes
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.user_id
                LEFT JOIN comment_votes v ON c.comment_id = v.comment_id
                WHERE c.topic_id = ?
                GROUP BY c.comment_id
                ORDER BY c.created_at DESC
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $topic_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()):
                $comment_id = $row['comment_id'];
                $comment = $row['comment'];
                $comment_user_name = $row['user_name'] ?? 'Unknown';
                $comment_created_at = $row['created_at'];
                $upvotes = $row['upvotes'];
                $downvotes = $row['downvotes'];
                $is_flagged = $row['is_flagged'];
                $is_deleted = $row['is_deleted'];
                $flagged_reason = $row['flagged_reason'];
            ?>

                <div class="p-3 mb-3 border">
                    <p><strong><?= htmlspecialchars($comment_user_name) ?></strong>
                        (<?= date('d M, Y h:i A', strtotime($comment_created_at)) ?>)
                    </p>

                    <?php if ($is_deleted): ?>
                        <p class="text-muted fst-italic">🗑️ This comment has been deleted by an admin.</p>
                    <?php else: ?>
                        <?= $Parsedown->text($comment) ?>

                        <?php if ($is_flagged): ?>
                            <p class="text-warning fst-italic">⚠️ This comment has been flagged for:
                                <?= htmlspecialchars($flagged_reason) ?></p>

                        <?php endif; ?>

                        <!-- Voting Buttons -->
                        <form method="POST" action="vote_comment.php" class="d-inline">
                            <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                            <button type="submit" name="vote" value="upvote" class="btn btn-sm btn-success">
                                Upvote (<?= $upvotes ?>)
                            </button>
                            <button type="submit" name="vote" value="downvote" class="btn btn-sm btn-danger">
                                Downvote (<?= $downvotes ?>)
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php $stmt->close(); ?>