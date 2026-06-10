<?php
include 'header.php';
include '../db.php';

$msg = "";
$error = "";

// Get user_id from URL and validate it
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Fetch user info
    $user_result = mysqli_query($conn, "SELECT name, email, profile_pic FROM users WHERE user_id = $user_id");
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user = mysqli_fetch_assoc($user_result);
        $name = $user['name'];
        $email = $user['email'];
        $profile_pic = $user['profile_pic'];
    } else {
        $error = "User not found.";
    }
} else {
    $error = "Invalid user ID.";
}
?>

<div class="container mt-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!empty($msg)): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($error)): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- User Profile Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <?php if (!empty($profile_pic)): ?>
                            <img src="<?= htmlspecialchars("../user/" . $profile_pic) ?>" class="rounded-circle mb-3" width="120" height="120" alt="Profile Picture">
                        <?php else: ?>
                            <img src="default-avatar.png" class="rounded-circle mb-3" width="120" height="120" alt="Default Avatar">
                        <?php endif; ?>
                        <h4 class="mb-1"><?= htmlspecialchars($name) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($email) ?></p>
                    </div>
                </div>

                <!-- User Posts Section -->
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><?= htmlspecialchars($name) ?>'s Posts</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $posts = mysqli_query($conn, "SELECT topic_id, title, created_at FROM discussion_topics WHERE user_id = $user_id ORDER BY created_at DESC");
                        if ($posts && mysqli_num_rows($posts) > 0):
                            while ($post = mysqli_fetch_assoc($posts)):
                        ?>
                                <li class="list-group-item">
                                    <a href="view_topics.php?topic_id=<?= $post['topic_id'] ?>" class="fw-bold">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                    <div class="text-muted small">
                                        Posted on <?= date('d M, Y h:i A', strtotime($post['created_at'])) ?>
                                    </div>
                                </li>
                        <?php endwhile;
                        else: ?>
                            <li class="list-group-item text-center text-muted">No posts available.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
