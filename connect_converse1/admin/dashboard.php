<?php
include 'header.php';
$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
include '../db.php';

// Fetching discussion topics that are not created by the current user
$result = $conn->query("SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id 
                       FROM discussion_topics d 
                       JOIN users u ON d.user_id = u.user_id
                       WHERE d.status = 'approved'

                       ORDER BY d.created_at DESC");

// Count user's own topics
$topicCountQuery = $conn->query("SELECT COUNT(*) AS total_posts FROM discussion_topics");
$total_posts = $topicCountQuery->fetch_assoc()['total_posts'];

// Count user's own comments
$commentCountQuery = $conn->query("SELECT COUNT(*) AS total_comments FROM comments");

$total_comments = $commentCountQuery->fetch_assoc()['total_comments'];

// Count upvotes
$upvoteQuery = $conn->query("SELECT COUNT(*) AS total_upvotes FROM comment_votes WHERE vote_type = 'upvote'");
$total_upvotes = $upvoteQuery->fetch_assoc()['total_upvotes'];

// Count downvotes
$downvoteQuery = $conn->query("SELECT COUNT(*) AS total_downvotes FROM comment_votes WHERE vote_type = 'downvote'");
$total_downvotes = $downvoteQuery->fetch_assoc()['total_downvotes'];

?>

<!-- Main Content -->
<div class="container mt-4">
    <div class="row mb-4">
        <!-- Admin view: Total stats -->
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Posts</h6>
                    <h3><?= $total_posts ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Comments</h6>
                    <h3><?= $total_comments ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Upvotes</h6>
                    <h3><?= $total_upvotes ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Downvotes (All Users)</h6>
                    <h3><?= $total_downvotes ?></h3>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Recent Discussions</h5>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                <small>Posted on <?= htmlspecialchars($row['created_at']) ?> in Academics</small>
            </a>
        </div>
    <?php endwhile ?>
</div>

</body>

</html>