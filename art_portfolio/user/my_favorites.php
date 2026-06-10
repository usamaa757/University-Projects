<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

$categoryFilter = '';
if (!empty($_GET['category'])) {
    $cat_id = (int)$_GET['category'];
    $categoryFilter = "AND a.category_id = $cat_id";
}

$result = mysqli_query($conn, "
    SELECT a.*, f.id AS favorite_id 
    FROM favorites f 
    JOIN artworks a ON f.artwork_id = a.id 
    WHERE f.user_id = '$user_id' $categoryFilter
");

?>

<h2>My Favorite Artworks</h2>
<form method="GET" class="filter-form">
    <label for="category">Filter by Category:</label>
    <select name="category" onchange="this.form.submit()">
        <option value="">-- All Categories --</option>
        <?php
        $cats = mysqli_query($conn, "SELECT * FROM categories");
        while ($cat = mysqli_fetch_assoc($cats)):
        ?>
        <option value="<?= $cat['category_id'] ?>"
            <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
        </option>
        <?php endwhile; ?>
    </select>
</form>
<div class="gallery">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="art-card">
        <img src="../artist/<?= $row['image_path']; ?>" alt="Artwork">
        <h4><?= htmlspecialchars($row['title']); ?></h4>
        <p><?= htmlspecialchars($row['description']); ?></p>

        <form method="POST" action="remove_favorite.php">
            <input type="hidden" name="favorite_id" value="<?= $row['favorite_id']; ?>">
            <button class="btn" type="submit">❌ Remove</button>
        </form>
    </div>
    <?php endwhile; ?>
</div>