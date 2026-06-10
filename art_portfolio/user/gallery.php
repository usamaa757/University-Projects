<?php
include 'header.php';
include '../db.php';

if (!isset($_GET['artist_id'])) {
    echo "<p>No artist selected.</p>";
    exit;
}

$artist_id = $_GET['artist_id'];

// Get artist info
$artist = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$artist_id' AND role = 'artist'"));
if (!$artist) {
    echo "<p>Artist not found.</p>";
    exit;
}

// Get artworks for this artist
$result = mysqli_query($conn, "SELECT * FROM artworks WHERE artist_id = '$artist_id' ORDER BY created_at DESC");
?>

<h2><?= htmlspecialchars($artist['name']) ?>'s Gallery</h2>

<div class="gallery">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="art-card">
        <img src="../artist/<?= $row['image_path'] ?>" alt="Artwork">
        <h4><?= htmlspecialchars($row['title']) ?></h4>
        <p><?= htmlspecialchars($row['description']) ?></p>
    </div>
    <?php endwhile; ?>
</div>