<?php
include 'header.php';
include 'db.php';
$error = '';
if (isset($_GET['id'])) {
    $error  = 'You need login to see full details';
}
// Fetch public artworks (all artworks for now)
$categoryFilter = '';
if (!empty($_GET['category'])) {
    $cat_id = (int)$_GET['category'];
    $categoryFilter = "WHERE a.category_id = $cat_id";
}

$result = mysqli_query($conn, "
    SELECT a.*, u.name AS artist_name 
    FROM artworks a 
    JOIN users u ON a.artist_id = u.user_id 
    $categoryFilter 
    ORDER BY a.created_at DESC
");
?>

<h2>Explore Art Gallery</h2>

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
<?php if ($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>
<div class="gallery">


    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="art-card">
        <a href="public_gallery.php?id=<?= $row['id'] ?>">
            <div class="card-img">
                <img src="artist/<?= $row['image_path'] ?>" alt="Artwork">
            </div>
            <div class="card-body">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p>By: <?= htmlspecialchars($row['artist_name']) ?></p>
            </div>
        </a>
    </div>
    <?php endwhile; ?>
</div>