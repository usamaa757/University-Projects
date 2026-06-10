<?php
include 'header.php';
$seller_id = htmlspecialchars($_GET['seller_id']);
include '../db_connection.php';

$message = ""; // Initialize the message variable

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $seller_id = intval($_POST['seller_id']);
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $review_text = htmlspecialchars($_POST['review_text']);
    $created_at = date("Y-m-d H:i:s");

    // Insert review into the database
    $sql = "INSERT INTO seller_reviews (seller_id, user_id, rating, review_text, created_at)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $seller_id, $user_id, $rating, $review_text, $created_at);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Review added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }

    $stmt->close();
}

$conn->close();
?>

<div class="container mt-5 shadow border round">
    <div>
        <h3>Add Seller Reviews</h3>

        <!-- Display the message if it exists -->
        <?php if (!empty($message)) echo $message; ?>

        <form action="" method="POST">
            <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>">
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="review_text">Review:</label>
                <textarea name="review_text" id="review_text" class="form-control" rows="3" required></textarea>
            </div>
            <div class="text-center mb-2">
                <button type="submit" class="btn text-white bg-primary">Submit Review</button>
            </div>
        </form>
    </div>
</div>