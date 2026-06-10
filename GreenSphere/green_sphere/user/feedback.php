<?php
// Include the header
include 'header.php';

// Check if plant_id is provided in the URL
if (isset($_GET['plant_id'])) {
    $plant_id = $_GET['plant_id'];

    // Include the database connection
    include '../db_connection.php';

    // Fetch the plant details
    $query = "SELECT * FROM plants WHERE plant_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $plant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plant = $result->fetch_assoc();
}

// Fetch reviews for the current plant
$query = "SELECT r.rating, r.review_text, r.photo_url, u.user_name 
          FROM plant_reviews r 
          JOIN users u ON r.user_id = u.user_id 
          WHERE r.plant_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $plant_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

// Store plant name for later use in the HTML file
$plant_name = isset($plant['plant_name']) ? $plant['plant_name'] : "Plant";
?>

<!-- Review Form -->
<div class="container mt-5 rounded border shadow p-4" style="max-width: 600px;">
    <h3 class="text-center mb-4">Review for <?php echo htmlspecialchars($plant_name); ?></h3>
    <form action="submit_review.php" method="POST" enctype="multipart/form-data">
        <?php
        if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="rating">Rating (1-5):</label>
            <select id="rating" name="rating" class="form-control" required>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
        </div>

        <!-- Review Text -->
        <div class="form-group">
            <label for="review_text">Review:</label>
            <textarea id="review_text" name="review_text" class="form-control" rows="4" required></textarea>
        </div>

        <!-- Photo Upload -->
        <div class="form-group">
            <label for="photo">Upload Photo (Optional):</label>
            <input type="file" name="photo" class="form-control-file" accept="image/*">
        </div>

        <input type="hidden" name="plant_id" value="<?php echo htmlspecialchars($plant_id); ?>">
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

        <!-- Submit Button -->
        <div class="text-center mt-4">
            <button type="submit" class="btn text-white bg-primary">Submit Review</button>
        </div>
    </form>
</div>

<?php if ($reviews_result->num_rows > 0): ?>
    <!-- Reviews Table -->
    <div class="container mt-5 rounded border shadow p-4">
        <h3>Reviews for <?php echo htmlspecialchars($plant_name); ?></h3>
        <table class="table table-bordered table-striped ">
            <thead class="bg-primary text-white">
                <tr>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Photo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($review['rating']); ?> Stars</td>
                        <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td>
                        <td>
                            <?php if ($review['photo_url']): ?>
                                <img src="<?php echo htmlspecialchars($review['photo_url']); ?>" alt="Review Photo"
                                    class="img-fluid" style="max-width: 100px;">
                            <?php else: ?>
                                No Photo
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="container mt-5">
        <p>No reviews yet for this plant.</p>
    </div>
<?php endif; ?>

</body>

</html>