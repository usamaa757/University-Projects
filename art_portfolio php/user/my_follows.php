<?php
include '../db.php';
include 'header.php';

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
    SELECT users.*
    FROM follows 
    JOIN users ON follows.artist_id = users.user_id 
    WHERE follows.user_id = '$user_id'
");
?>

<h2>Artists You Follow</h2>

<div class="artist-list">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="artist-card">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
        <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($row['bio'])) ?></p>
        <p><strong>Style:</strong> <?= nl2br(htmlspecialchars($row['style'])) ?></p>
        <p><strong>Project Detail:</strong> <?= nl2br(htmlspecialchars($row['project_description'])) ?></p>
        <a href="gallery.php?artist_id=<?= $row['user_id'] ?>" class="btn"> View Arts</a>
        <a href="unfollow_artist.php?artist_id=<?= $row['user_id'] ?>" class="btn"> ❌ Unfollow</a>

    </div>
    <?php endwhile; ?>
</div>