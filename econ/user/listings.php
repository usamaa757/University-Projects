<?php

include 'header.php';

include '../db.php';


$conditions = [];
$params = [];

if (!empty($_GET['listing_type'])) {
    $conditions[] = "p.listing_type = ?";
    $params[] = $_GET['listing_type'];
}
if (!empty($_GET['type'])) {
    $conditions[] = "p.type = ?";
    $params[] = $_GET['type'];
}
if (!empty($_GET['location'])) {
    $conditions[] = "p.city LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
}
if (!empty($_GET['min_price'])) {
    $conditions[] = "p.price >= ?";
    $params[] = $_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $conditions[] = "p.price <= ?";
    $params[] = $_GET['max_price'];
}

$sql = "
    SELECT p.*, 
           (SELECT image_path FROM property_images WHERE property_id = p.id LIMIT 1) AS image 
    FROM properties p WHERE status = 'Available'";

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY p.id DESC";


$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<form method="get">
    <div class="search-bar-container">
        <div class="search-bar">
            <select name="listing_type">
                <option value="">--- Select ---</option>
                <option value="Buy"
                    <?= isset($_GET['listing_type']) && $_GET['listing_type'] == 'Buy' ? 'selected' : '' ?>>Buy
                </option>
                <option value="Rent"
                    <?= isset($_GET['listing_type']) && $_GET['listing_type'] == 'Rent' ? 'selected' : '' ?>>Rent
                </option>
            </select>
            <select name="type">
                <option value="">All Types</option>
                <option value="House" <?= isset($_GET['type']) && $_GET['type'] == 'House' ? 'selected' : '' ?>>House
                </option>
                <option value="Plot" <?= isset($_GET['type']) && $_GET['type'] == 'Plot' ? 'selected' : '' ?>>Plot
                </option>
                <option value="Commercial"
                    <?= isset($_GET['type']) && $_GET['type'] == 'Commercial' ? 'selected' : '' ?>>Commercial
                </option>
            </select>
            <input type="text" name="location" placeholder="Enter location..." value="<?= $_GET['location'] ?? '' ?>">
            <input type="number" name="min_price" placeholder="Min Price" value="<?= $_GET['min_price'] ?? '' ?>">
            <input type="number" name="max_price" placeholder="Max Price" value="<?= $_GET['max_price'] ?? '' ?>">

            <button type="submit"
                style="padding: 0.5rem 1rem; background-color: #3AAFA9; color: white; border: none;">Search</button>
        </div>
    </div>
</form>


<section class="featured-listings">
    <div class="section-header">

        <h2>Featured Listings</h2>
    </div>

    <div class="property-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="property-card">
                <img src="../agent/<?php echo htmlspecialchars($row['image']); ?>" alt="Property Image" />
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p>$ <?php echo number_format($row['price']); ?> - <?php echo htmlspecialchars($row['city']); ?></p>
                <strong>Type: </strong>
                <p><?php echo htmlspecialchars($row['listing_type']); ?></p>
                <a href="property_detail.php?id=<?= $row['id'] ?>" class="btn">Details</a>
            </div>
        <?php endwhile; ?>
    </div>



</section>

<?php include '../footer.php'; ?>

</body>

</html>