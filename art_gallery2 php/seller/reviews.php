<?php
include '../db.php';
include 'header.php';

$art_id = $_GET['art_id'];

// Fetch artwork details
$art_query = "SELECT * FROM art_items WHERE art_id = ?";
$art_stmt = $conn->prepare($art_query);
$art_stmt->bind_param("i", $art_id);
$art_stmt->execute();
$art_result = $art_stmt->get_result();
$art = $art_result->fetch_assoc();

// Fetch reviews
$review_query = "SELECT r.*, u.username 
                 FROM reviews r
                 JOIN users u ON r.user_id = u.user_id
                 WHERE r.art_id = ?
                 ORDER BY r.review_date DESC";

$stmt = $conn->prepare($review_query);
$stmt->bind_param("i", $art_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<div class="container mt-4">
    <div class="row">
        <!-- Artwork Details -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <img src="<?= $base_url . 'seller/' . htmlspecialchars($art['image']) ?>" class="card-img-top"
                    alt="Artwork Image" style="max-height: 300px; object-fit: contain;">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($art['art_name']) ?></h4>
                    <p class="card-text"><?= nl2br(htmlspecialchars($art['description'])) ?></p>
                    <p class="card-text"><strong>Price:</strong> $<?= number_format($art['price'], 2) ?></p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Reviews</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if ($reviews->num_rows > 0): ?>
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                            <div class="border-bottom pb-2 mb-3">
                                <strong><?= htmlspecialchars($review['username']) ?></strong>
                                <span class="text-warning">⭐ <?= $review['rating'] ?>/5</span><br>
                                <small class="text-muted"><?= date('d M Y', strtotime($review['review_date'])) ?></small>
                                <p class="mt-2"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No reviews yet for this artwork.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>