<?php
include 'header.php';
include 'db_connection.php'; // Include your database connection file

// Initialize variables
$searchQuery = "";

// Check if the search form is submitted
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Query to fetch categories
$sql = "SELECT category_id, category_name, description FROM categories WHERE category_name LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $searchQuery . '%';
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

?>

<!-- View/Search Categories Section -->
<section class="categories-section">
    <div class="container">
        <h2 class="mb-4">Explore Categories</h2>

        <!-- Search Form -->
        <form method="GET" action="view_categories.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search categories..."
                    value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Categories List -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['category_name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p>No categories found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
include 'footer.php';
?>