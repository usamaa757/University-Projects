<?php
include 'header.php';
$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
include '../db.php';

$result = $conn->query("SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id FROM discussion_topics d JOIN users u ON d.user_id = u.user_id WHERE d.user_id != $user_id
 ORDER BY d.created_at DESC");

// Count user's own topics
$topicCountQuery = $conn->query("SELECT COUNT(*) AS total_posts FROM discussion_topics WHERE user_id = $user_id");
$total_posts = $topicCountQuery->fetch_assoc()['total_posts'];

// Count user's own comments
$commentCountQuery = $conn->query("
    SELECT COUNT(*) AS total_comments 
    FROM comments 
    JOIN discussion_topics ON comments.topic_id = discussion_topics.topic_id 
    WHERE discussion_topics.user_id = $user_id
");

$total_comments = $commentCountQuery->fetch_assoc()['total_comments'];


?>

<!-- Main Content -->
<div class="container mt-4">


    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Posts</h6>
                    <h3><?= $total_posts ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Comments</h6>
                    <h3><?= $total_comments ?></h3>

                </div>
            </div>
        </div>


    </div>



    <h5 class="mb-3">Recent Discussions</h5>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="list-group">
            <a href="view_topics.php?topic_id=<?= $row['topic_id'] ?>" class="list-group-item list-group-item-action">
                <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                <small>Posted on <?= htmlspecialchars($row['created_at']) ?></small>
            </a>
        </div>
    <?php endwhile ?>

</div>


</body>

</html>