<?php
include '../db.php';
include 'header.php'; // contains Bootstrap & session_start()



$msg = "";

// Delete a topic
if (isset($_POST['delete_topic'])) {
    $topic_id = intval($_POST['topic_id']);
    $stmt = $conn->prepare("DELETE FROM discussion_topics WHERE topic_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $topic_id, $user_id);
    $stmt->execute();
    $stmt->close();
    $msg = "Post deleted successfully.";
}

// Fetch user info
$user_q = $conn->query("SELECT name, email FROM users WHERE user_id = $user_id");
$user = $user_q->fetch_assoc();

// Fetch user posts
$topics = $conn->query("SELECT * FROM discussion_topics WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Welcome, <?= htmlspecialchars($user['name']) ?></h3>

    <?php if (!empty($msg)): ?>
        <p class="text-success text-center"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Your Profile</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>
