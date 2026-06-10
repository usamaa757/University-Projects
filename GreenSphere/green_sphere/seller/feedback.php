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
$query = "SELECT r.rating, r.review_text, r.photo_url, u.user_name, r.plant_id
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
<div class="container mt-5">
    <?php if ($reviews_result->num_rows > 0): ?>
    <!-- Reviews Table -->
    <div class="container mt-5 rounded border shadow p-4">
        <h3>Reviews for <?php echo htmlspecialchars($plant_name); ?></h3>
        <table class="table table-bordered table-striped ">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Plant ID</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Photo</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $reviews_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['plant_id']); ?></td>
                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['rating']); ?> Stars</td>
                    <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td>
                    <td>
                        <?php if ($review['photo_url']): ?>
                        <img src="<?php echo '../user/' . htmlspecialchars($review['photo_url']); ?>" alt="Review Photo"
                            class="img-fluid" style="max-width: 150px; height:150px">
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
</div>

</body>

</html>