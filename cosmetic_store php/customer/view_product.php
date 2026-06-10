<?php
include 'header.php';
include '../db.php';

// Fetch distinct categories and brands for filter dropdowns
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM products");
$brands = mysqli_query($conn, "SELECT DISTINCT brand FROM products");

// Handle filter input
$type = $_GET['type'] ?? '';
$brand = $_GET['brand'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Build filter query
$query = "SELECT * FROM products  WHERE 1=1";
if ($type != '') $query .= " AND category = '" . mysqli_real_escape_string($conn, $type) . "'";
if ($brand != '') $query .= " AND brand = '" . mysqli_real_escape_string($conn, $brand) . "'";
if ($min_price != '') $query .= " AND price >= " . (float)$min_price;
if ($max_price != '') $query .= " AND price <= " . (float)$max_price;

$result = mysqli_query($conn, $query);
?>

<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>View Products</h2>
    </div>

    <!-- Filter Form -->
    <form method="get" class="filter-form">
        <div class="filter-row">
            <select name="type">
                <option value="">-- Select Type --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?= $cat['category'] ?>" <?= ($type == $cat['category']) ? 'selected' : '' ?>>
                        <?= $cat['category'] ?>
                    </option>
                <?php } ?>
            </select>

            <select name="brand">
                <option value="">-- Select Brand --</option>
                <?php while ($br = mysqli_fetch_assoc($brands)) { ?>
                    <option value="<?= $br['brand'] ?>" <?= ($brand == $br['brand']) ? 'selected' : '' ?>>
                        <?= $br['brand'] ?>
                    </option>
                <?php } ?>
            </select>

            <input type="number" name="min_price" placeholder="Min Price" value="<?= htmlspecialchars($min_price) ?>">
            <input type="number" name="max_price" placeholder="Max Price" value="<?= htmlspecialchars($max_price) ?>">

            <button type="submit" class="btn">Filter</button>
        </div>
    </form>

    <!-- Product Grid -->
    <div class="product-grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="product-img">
                    <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p>Type: <?= htmlspecialchars($row['category']) ?></p>
                    <p>Brand: <?= htmlspecialchars($row['brand']) ?></p>
                    <p>Price: $<?= number_format($row['price'], 2) ?></p>
                    <p>Quantity Available: <?= htmlspecialchars($row['quantity']) ?></p>
                    <?php
                    if ($row['quantity'] == 0) {
                        echo 'Out of stock';
                    } else {
                    ?>
                        <!-- Add to Cart Form with Quantity -->
                        <form action="add_to_cart.php" method="get">
                            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $row['quantity'] ?>" required>
                            <button type="submit" class="btn">Add to Cart</button>
                        </form>
                    <?php  }

                    ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No products found.</p>
        <?php endif; ?>
    </div>
</div>

</body>

</html>