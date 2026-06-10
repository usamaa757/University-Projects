<?php
include 'header.php';
include '../db.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['art_id'])) {
    $art_id = $_GET['art_id'];
    $stmt = $conn->prepare("SELECT * FROM arts WHERE art_id = ?");
    $stmt->bind_param("i", $art_id);
    $stmt->execute();
    $art = $stmt->get_result()->fetch_assoc();
}

// Handle review submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $art_id = $_POST['art_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO reviews (art_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $art_id, $user_id, $rating, $comment);
    if ($stmt->execute()) {
        echo "<script>
    alert('Review submitted successfully!');
    window.location.href = 'add_reviews.php?art_id=$art_id#reviews';
    </script>";
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>
        alert('Error in submitting reivews!');
        window.location.href = 'add_reviews.php?art_id=$art_id#reviews';
        </script>";
        exit;
    }
}
?>

<div class="container mt-5">
    <?php if (isset($art)): ?>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Art Display -->
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

    <!-- Review Form -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-3 shadow">
                <h3 class="text-center mb-3">Leave a Review</h3>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="art_id" value="<?= $art['art_id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Rating (1 to 5)</label>
                            <select name="rating" class="form-control" required>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <p class="text-danger text-center">Art not found.</p>
    <?php endif; ?>
</div