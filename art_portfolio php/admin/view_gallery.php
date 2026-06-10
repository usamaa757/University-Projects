<?php
include 'header.php';
include '../db.php';
$artist_id = $_SESSION['user_id'];

// Fetch artist's artworks
$result = mysqli_query($conn, "SELECT * FROM artworks ORDER BY created_at DESC");
?>

<h2>Your Gallery</h2>
<div class="gallery">
    <?php while ($row = mysqli_fetch_assoc($result)):
        $artwork_id = $row['id']; // assuming your artwork table has `id`
        $artist_id = $row['artist_id'];
    ?>
        <div class="art-card">
            <img src="../artist/<?php echo $row['image_path']; ?>" alt="Artwork">
            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
            <p><?php echo htmlspecialchars($row['description']); ?></p>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                <!-- Favorite Button -->
                <?php
                $uid = $_SESSION['user_id'];
                $fav_check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = '$uid' AND artwork_id = '$artwork_id'");
                if (mysqli_num_rows($fav_check) == 0): ?>
                    <a href="save_favorite.php?artwork_id=<?= $artwork_id ?>" class="btn">❤️ Favorite</a>
                <?php else: ?>
                    <span class="badge">❤️ Favorited</span>
                <?php endif; ?>

                <!-- Follow Artist -->
                <?php
                $follow_check = mysqli_query($conn, "SELECT * FROM follows WHERE user_id = '$uid' AND artist_id = '$artist_id'");
                if (mysqli_num_rows($follow_check) == 0): ?>
                    <a href="follow_artist.php?artist_id=<?= $artist_id ?>" class="btn">➕ Follow Artist</a>
                <?php else: ?>
                    <span class="badge">✅ Following</span>
                <?php endif; ?>

                <!-- Comment Form -->
                <form method="POST" action="add_comments.php" class="comment-form">
                    <input type="hidden" name="artwork_id" value="<?= $artwork_id ?>">
                    <textarea name="content" placeholder="Add a comment..." required></textarea>
                    <button type="submit" class="btn">💬 Comment</button>
                </form>

                <!-- Comments -->
                <div class="comments">
                    <?php
                    $comments = mysqli_query($conn, "
                    SELECT comments.*, users.name FROM comments 
                    JOIN users ON comments.user_id = users.user_id 
                    WHERE artwork_id = '$artwork_id' ORDER BY created_at DESC
                ");
                    while ($c = mysqli_fetch_assoc($comments)): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($c['name']) ?>:</strong>
                            <?= htmlspecialchars($c['content']) ?>
                            <div class="time"><?= date('M j, Y', strtotime($c['created_at'])) ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>