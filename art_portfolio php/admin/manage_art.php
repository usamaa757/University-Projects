<?php
include 'header.php';
include '../db.php';

$message = '';
$error = '';

// Handle delete artwork
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Delete related data first (if needed)
    mysqli_query($conn, "DELETE FROM favorites WHERE artwork_id = '$delete_id'");
    mysqli_query($conn, "DELETE FROM comments WHERE artwork_id = '$delete_id'");

    // Delete the artwork
    if (mysqli_query($conn, "DELETE FROM artworks WHERE id = '$delete_id'")) {
        $message = "Artwork and related data deleted successfully.";
    } else {
        $error = "Failed to delete artwork.";
    }
}

// Fetch all artworks
$result = mysqli_query($conn, "
    SELECT artworks.*, 
           users.name AS artist_name, 
           categories.name AS category_name 
    FROM artworks 
    JOIN users ON artworks.artist_id = users.user_id
    LEFT JOIN categories ON artworks.category_id = categories.category_id
");
?>

<h2>Manage Artworks</h2>

<a href="add_artwork.php" class="btn">Add New Artwork</a><br><br>

<?php if ($message): ?>
<p class="message"><?php echo $message; ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Artist</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['artist_name']) ?></td>
            <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
            <td>

                <a class="btn" href="?delete=<?= $row['id'] ?>"
                    onclick="return confirm('Delete this artwork?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>

</html>