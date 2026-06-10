<?php
include 'header.php';
include '../db.php';

$artist_id = $_SESSION['user_id'];
$message = "";
$error = "";

// Check artwork ID
if (!isset($_GET['id'])) {
    die("Invalid request.");
}
$artwork_id = $_GET['id'];

// Fetch artwork to edit (including category_id)
$artwork = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM artworks WHERE id = '$artwork_id' AND artist_id = '$artist_id'"));
if (!$artwork) {
    die("Artwork not found or unauthorized.");
}

// Fetch all categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = $_POST['category_id'];  // get from form
    $image_path = $artwork['image_path']; // default: old image

    if (!empty($_FILES['image']['name'])) {
        $new_image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $path = "uploads/" . basename($new_image);

        if (move_uploaded_file($tmp, $path)) {
            if (file_exists($image_path)) unlink($image_path);
            $image_path = $path;
        } else {
            $error = "Failed to upload new image.";
        }
    }

    if (empty($error)) {
        $sql = "UPDATE artworks 
                SET title = '$title', description = '$description', image_path = '$image_path', category_id = '$category_id'
                WHERE id = '$artwork_id' AND artist_id = '$artist_id'";

        if (mysqli_query($conn, $sql)) {
            $message = "Artwork updated successfully.";
            // Refresh updated info including new category
            $artwork = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM artworks WHERE id = '$artwork_id'"));
        } else {
            $error = "Database error while updating.";
        }
    }
}
?>


<div class="form-container">
    <h2>Edit Artwork</h2>

    <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="forms">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($artwork['title']); ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="3"
            required><?php echo htmlspecialchars($artwork['description']); ?></textarea>
        <label>Category:</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['category_id'] ?>"
                <?= ($cat['category_id'] == $artwork['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endwhile; ?>
        </select>

        <label>Current Image:</label><br>
        <img src="<?php echo $artwork['image_path']; ?>" alt="Current Artwork"><br>

        <label>Upload New Image (optional):</label>
        <input type="file" name="image" accept="image/*">


        <div class="text-center">
            <button type="submit" class="btn">Update</button>
        </div>
    </form>
</div>

</body>

</html>