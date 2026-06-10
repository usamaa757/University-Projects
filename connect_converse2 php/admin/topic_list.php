<?php
include 'header.php';
include '../db.php';

// Fetch all approved discussions
$result = $conn->query("
    SELECT d.topic_id, d.title, d.created_at, u.name, u.user_id, c.category_name 
    FROM discussion_topics d 
    JOIN users u ON d.user_id = u.user_id 
    LEFT JOIN categories c ON d.category_id = c.category_id 
    ORDER BY d.created_at DESC
");
?>

<div class="container mt-5 rounded shadow-sm p-4 border bg-white">

    <h2 class="fw-bold text-primary text-center"><i class="bi bi-megaphone-fill me-2"></i>Discussions</h2>



    <div class="row g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm topic-tile bg-light">
                    <div class="card-body ">
                        <h5 class="card-title mb-1">
                            <p class="text-dark text-decoration-none fw-semibold">
                                <?= htmlspecialchars($row['title']) ?>
                            </p>
                        </h5>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <small class="text-muted d-block">
                                    Posted By <a href="view_profile.php?user_id=<?= $row['user_id'] ?>"
                                        class="text-decoration-none text-primary">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </a>
                                </small>
                                <small class="text-muted"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></small>
                            </div>
                            <a href="view_topics.php?topic_id=<?= $row['topic_id'] ?>"
                                class="btn btn-outline-secondary btn-sm  bg-success rounded-pill">
                                <i class="bi bi-arrow-right-circle text-white"></i>
                            </a>
                        </div>
                    </div>
                    <?php if (!empty($row['category_name'])): ?>
                        <div class="card-footer bg-transparent border-0">
                            <span class="badge bg-secondary"><?= htmlspecialchars($row['category_name']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
    .topic-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease-in-out;
    }

    .card-title a:hover {
        color: #0d6efd;
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 1rem;
        }
    }
</style>