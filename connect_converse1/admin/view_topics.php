<?php
include 'header.php';
include '../db.php';
include '../user/parsedown/Parsedown.php';
$Parsedown = new Parsedown();

if (!isset($_GET['topic_id'])) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}

$topic_id = intval($_GET['topic_id']);

// Fetch post details
$stmt = $conn->prepare("SELECT title, content, created_at, user_id FROM discussion_topics WHERE topic_id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$stmt->bind_result($title, $content, $created_at, $post_user_id);
$stmt->fetch();
$stmt->close();

// Check if post exists
if (!$title) {
    echo "<script>alert('Post not found.'); window.location.href='index.php';</script>";
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if ($comment) {
        $stmt = $conn->prepare("INSERT INTO comments (topic_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $topic_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Comment added successfully!');</script>";
    } else {
        echo "<script>alert('Comment cannot be empty.');</script>";
    }
}

// Fetch comments for this post and their vote counts
$stmt = $conn->prepare("SELECT comment_id, comment, created_at, user_id FROM comments WHERE topic_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$stmt->store_result(); // Ensure the result set is stored before fetching
$stmt->bind_result($comment_id, $comment, $comment_created_at, $comment_user_id);

// Fetch vote counts (upvotes and downvotes) for each comment
$vote_counts = [];
$vote_stmt = $conn->prepare("
    SELECT comment_id, 
           SUM(CASE WHEN vote_type = 'upvote' THEN 1 ELSE 0 END) AS upvotes, 
           SUM(CASE WHEN vote_type = 'downvote' THEN 1 ELSE 0 END) AS downvotes 
    FROM comment_votes 
    GROUP BY comment_id
");
$vote_stmt->execute();
$vote_stmt->bind_result($vote_comment_id, $upvotes, $downvotes);

// Store the vote counts in an array
while ($vote_stmt->fetch()) {
    $vote_counts[$vote_comment_id] = ['upvotes' => $upvotes, 'downvotes' => $downvotes];
}
$vote_stmt->close();
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

    <!-- Comments and Add Comment Section Combined -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5>Comments</h5>
        </div>
        <div class="card-body">

            <!-- Add a comment form -->
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Add a comment..."></textarea>

                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>

            <?php
            // Display Comments
            while ($stmt->fetch()):
                // Fetch user details for the comment
                $user_stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
                $user_stmt->bind_param("i", $comment_user_id);
                $user_stmt->execute();
                $user_stmt->bind_result($comment_user_name);
                $user_stmt->fetch();
                $user_stmt->close();

                // Get vote counts
                $upvotes = isset($vote_counts[$comment_id]) ? $vote_counts[$comment_id]['upvotes'] : 0;
                $downvotes = isset($vote_counts[$comment_id]) ? $vote_counts[$comment_id]['downvotes'] : 0;
            ?>
                <div class="p-3 mb-3">
                    <p><strong><?= htmlspecialchars($comment_user_name) ?></strong>
                        (<?= date('d M, Y h:i A', strtotime($comment_created_at)) ?>)</p>
                    <?= $Parsedown->text($comment) ?>

                    <!-- Voting (Upvote / Downvote) -->
                    <form method="POST" action="../user/vote_comment.php" class="d-inline">
                        <input type="hidden" name="comment_id" value="<?= $comment_id ?>">
                        <button type="submit" name="vote" value="upvote" class="btn btn-sm btn-success">Upvote
                            (<?= $upvotes ?>)</button>
                        <button type="submit" name="vote" value="downvote" class="btn btn-sm btn-danger">Downvote
                            (<?= $downvotes ?>)</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php
$stmt->close();
?>