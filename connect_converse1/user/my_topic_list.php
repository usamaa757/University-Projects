<?php
include 'header.php';
include '../db.php';
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id FROM discussion_topics d JOIN users u ON d.user_id = u.user_id  WHERE d.user_id = $user_id AND d.status = 'approved' ORDER BY d.created_at DESC");
$categories = $conn->query("SELECT category_id, category_name FROM categories");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">💬 MY Discussion Threads</h3>
    </div>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3 shadow-sm border-0 topic-card hover-shadow">
            <div class="card-body">
                <h5 class="card-title mb-1">
                    <a href="view_thread.php?topic_id=<?= $row['topic_id'] ?>"
                        class="text-dark text-decoration-none fw-semibold">
                        <?= htmlspecialchars($row['title']) ?>
                    </a>
                </h5>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        Posted by
                        <a href="view_profile.php?user_id=<?= $row['user_id'] ?>" class="text-decoration-none text-primary">
                            <?= htmlspecialchars($row['name']) ?>
                        </a>
                        on <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?>
                    </small>
                    <a href="view_topics.php?topic_id=<?= htmlspecialchars($row['topic_id']) ?>"
                        class="btn btn-sm btn-outline-secondary">View
                        Thread</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<style>
    .topic-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #0d6efd;
    }

    .topic-card:hover {
        transform: scale(1.01);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
</style>