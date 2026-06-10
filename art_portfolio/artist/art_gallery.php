<?php
include 'header.php';
include '../db.php';
$artist_id = $_SESSION['user_id'];

$message = "";
$error = "";

// Handle deletion
if (isset($_GET['delete'])) {
    $artwork_id = $_GET['delete'];
    $check = mysqli_query($conn, "SELECT * FROM artworks WHERE id = '$artwork_id' AND artist_id = '$artist_id'");
    if (mysqli_num_rows($check) === 1) {
        $artwork = mysqli_fetch_assoc($check);
        $image_path = $artwork['image_path'];

        // Delete record
        if (mysqli_query($conn, "DELETE FROM artworks WHERE id = '$artwork_id'")) {
            if (file_exists($image_path)) {
                unlink($image_path); // delete image file
            }
            $message = "Artwork deleted successfully.";
        } else {
            $error = "Failed to delete artwork.";
        }
    } else {
        $error = "Artwork not found or unauthorized.";
    }
}

// Fetch all artworks
$result = mysqli_query($conn, "
    SELECT a.*, c.name AS category_name
    FROM artworks a
    LEFT JOIN categories c ON a.category_id = c.category_id
    WHERE a.artist_id = '$artist_id'
    ORDER BY a.created_at DESC
");
?>


<h2>Your Uploaded Artworks</h2>

<?php if ($message): ?>
<p class="message"><?php echo $message; ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Uploaded At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Artwork" width="100"></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
            <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a class="btn edit-btn" href="edit_art.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="btn delete-btn" href="art_gallery.php?delete=<?= $row['id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this artwork?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>

</table>

</body>

</html>