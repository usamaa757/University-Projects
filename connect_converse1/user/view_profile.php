<?php
include 'header.php';
include '../db.php';

if (!isset($_GET['user_id'])) {
    echo "<script>alert('User not found.'); window.location.href='index.php';</script>";
    exit;
}

$user_id = intval($_GET['user_id']);

$stmt = $conn->prepare("SELECT name, email, profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $profile_pic);
$stmt->fetch();
$stmt->close();
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4><?= htmlspecialchars($name) ?>'s Profile</h4>
        </div>
        <div class="card-body text-center">
            <?php if ($profile_pic): ?>
            <img src="<?php echo str_replace("user/", "", $profile_pic); ?>" class="rounded-circle" width="100"
                height="100" alt="Current Picture">
            <?php endif; ?>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        </div>
    </div>
    <!-- User Posts Section -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h5><?= htmlspecialchars($name) ?>'s Posts</h5>
        </div>
        <ul class="list-group list-group-flush">
            <?php
            // Fetch posts made by this user
            $stmt = $conn->prepare("SELECT topic_id, title, created_at FROM discussion_topics WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($topic_id, $title, $created_at);
            $hasPosts = false;
            while ($stmt->fetch()):
                $hasPosts = true;
            ?>
            <li class="list-group-item">
                <a href="view_topics.php?topic_id=<?= $topic_id ?>"><?= htmlspecialchars($title) ?></a>
                <div class="small text-muted">Posted on <?= date('d M, Y h:i A', strtotime($created_at)) ?></div>
            </li>
            <?php endwhile;
            $stmt->close();

            // If no posts exist for this user
            if (!$hasPosts) {
                echo "<li class='list-group-item text-muted text-center'>No posts yet.</li>";
            }
            ?>
        </ul>
    </div>

</div>