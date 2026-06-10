<?php
include 'header.php';
include '../db.php';


if (!isset($_GET['art_id'])) {
    echo "<script>alert('No art selected.'); window.location.href='index.php';</script>";
    exit;
}

$art_id = $_GET['art_id'];

// Fetch the art
$stmt = $conn->prepare("SELECT * FROM arts WHERE art_id = ?");
$stmt->bind_param("i", $art_id);
$stmt->execute();
$art = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$art) {
    echo "<script>alert('Art not found.'); window.location.href='index.php';</script>";
    exit;
}
?>

<div class="container mt-5">
    <!-- Art Information -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4 shadow">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?= htmlspecialchars($art['image']) ?>" class="img-fluid rounded-start h-100 w-100"
                            style="object-fit: cover;" alt="<?= htmlspecialchars($art['art_name']) ?>">
                    </div>
                    <div class="col-md-8 p-4">
                        <h4 class="card-title"><?= htmlspecialchars($art['art_name']) ?></h4>
                        <p class="card-text"><strong>Price:</strong> Rs. <?= number_format($art['price'], 2) ?></p>
                        <p class="card-text"><?= nl2br(htmlspecialchars($art['description'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row justify-content-center mt-5" id="reviews">
        <div class="col-md-8">
            <h4 class="mb-3">User Reviews</h4>
            <?php
            $stmt = $conn->prepare("SELECT r.rating, r.comment, u.name, r.review_date FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.art_id = ? ORDER BY r.review_date DESC");
            $stmt->bind_param("i", $art_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0):
                while ($review = $result->fetch_assoc()):
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($review['name']) ?> - Rating:
                        <?= $review['rating'] ?>/5</h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <small class="text-muted">Posted on <?= date('d M, Y', strtotime($review['review_date'])) ?></small>
                </div>
            </div>
            <?php
                endwhile;
            else:
                ?>
            <p class="text-muted">No reviews yet. Be the first to review!</p>
            <?php endif;
            $stmt->close();
            ?>
        </div>
    </div>
</div>