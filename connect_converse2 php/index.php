<?php
include 'header.php';
include 'db.php';

// Fetch latest discussions
$latest_topics = $conn->query("SELECT dt.title, dt.topic_id, dt.created_at, u.name, c.category_name FROM discussion_topics dt
    JOIN users u ON dt.user_id = u.user_id
    JOIN categories c ON dt.category_id = c.category_id
    ORDER BY RAND() DESC LIMIT 5");
?>

<!-- Hero Section -->
<div class="hero"
    style="position: relative; background-image: url('https://images.unsplash.com/photo-1659356874266-c672f53e6473?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center; height: 90vh; color: white;">
    <div class="overlay"
        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; text-align: center; padding: 40px;">
        <div class="content">
            <h1 class="display-4 fw-bold">Welcome to Connect & Converse</h1>
            <p class="lead">A vibrant space for university students to share ideas, ask questions, and engage in
                meaningful conversations.</p>
            <a href="register.php" class="btn btn-light btn-lg mt-3">Join the Community</a>
        </div>
    </div>
</div>

<!-- Latest Discussions -->
<div class="container mt-5">
    <h2 class="mb-4 text-center">Latest Discussions</h2>
    <div class="list-group">
        <?php while ($row = $latest_topics->fetch_assoc()): ?>
        <span class="list-group-item list-group-item-action">
            <h5 class="mb-1"><?= htmlspecialchars($row['title']) ?></h5>
            <small class="text-muted">Posted by <?= htmlspecialchars($row['name']) ?> in
                <?= htmlspecialchars($row['category_name']) ?> on
                <?= date('F j, Y', strtotime($row['created_at'])) ?></small>
        </span>
        <?php endwhile; ?>
    </div>
</div>

<!-- Footer -->
<footer class="text-center mt-5 py-3 border-top">
    &copy; <?= date("Y") ?> Connect & Converse. All rights reserved.

</footer>

</body>

</html>