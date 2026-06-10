<?php
include "header.php";
include "../db_connection.php";
$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 0;
if ($seller_id === 0) {
    echo "Invalid seller ID.";
    exit();
}
?>
<div class="container mt-3">
    <a href="products_list.php">

    <button class="btn btn-primary">Back</button>
    </a>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Feadback & Review</h3>
                <div class="p-3">

                    <form action="submit_review.php" method="POST">
                        <?php if (isset($_GET['msg']) || isset($_GET['error'])) : ?>
                            <?php if (isset($_GET['msg'])) : ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($_GET['msg']); ?>
                                </div>
                            <?php elseif (isset($_GET['error'])) : ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <input type="hidden" name="seller_id" value="<?php echo htmlspecialchars($seller_id); ?>">
                        <input type="hidden" name="buyer_id" value="<?php echo htmlspecialchars($_SESSION['buyer_id']); ?>">

                        <div class="form-group">
                            <label for="rating">Rating (1 to 5):</label>
                            <input type="number" id="rating" name="rating" class="form-control" min="1" max="5" required>
                        </div>

                        <div class="form-group">
                            <label for="review_text">Review:</label>
                            <textarea id="review_text" name="review_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>