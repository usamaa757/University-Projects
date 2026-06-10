<?php
include 'header.php';
include '../db.php';

$user_id = $_SESSION['user_id'];
$art_id = $_GET['art_id'];

// Fetch artwork details
$art_sql = "SELECT * FROM art_items WHERE art_id = ?";
$art_stmt = $conn->prepare($art_sql);
$art_stmt->bind_param("i", $art_id);
$art_stmt->execute();
$art_result = $art_stmt->get_result();
$art = $art_result->fetch_assoc();

// Check if user has purchased the artwork
$sql = "SELECT * FROM orders WHERE customer_id = ? AND art_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $art_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php if ($result->num_rows > 0): ?>
    <div class="container mt-4 ">
        <div class="row">
            <!-- Left Column - Artwork Details -->
            <div class="col-md-6 ">
                <div class="card h-100">

                    <h3 class="text-center">Reviewing: <strong><?= htmlspecialchars($art['art_name']) ?></strong></h3>

                    <img src="<?= $base_url . 'seller/' . $art['image'] ?>" class="card-img-top" alt="Artwork Image"
                        style="max-height: 300px; object-fit: contain;">
                    <div class="card-body ">
                        <p><strong>Description:</strong> <?= htmlspecialchars($art['description']) ?></p>
                        <p><strong>Price:</strong> $<?= number_format($art['price'], 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Right Column - Review Form -->
            <div class="col-md-6">
                <div class="card h-100">


                    <h3 class="text-center">Leave a Review</h3>

                    <div class="card-body ">
                        <form action="submit_review.php" method="post">
                            <input type="hidden" name="art_id" value="<?= $art_id ?>">

                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">Select rating</option>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="review_text" class="form-label">Review</label>
                                <textarea name="review_text" class="form-control" rows="3" required
                                    placeholder="Write your thoughts..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning text-center mt-5 col-md-6 mx-auto">
        You need to purchase this artwork before leaving a review.
    </div>
<?php endif; ?>