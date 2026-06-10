<?php
include 'header.php';
include '../db.php';
$artist_id = $_SESSION['user_id'];
$message = $error = "";

// Handle artwork upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $title = $_POST["title"];
    $desc  = $_POST["description"];
    $category_id  = $_POST["category_id"];
    $image = $_FILES["image"]["name"];
    $tmp   = $_FILES["image"]["tmp_name"];

    $path = "uploads/" . basename($image);
    if (move_uploaded_file($tmp, $path)) {
        $sql = "INSERT INTO artworks (artist_id, title, description, image_path, category_id) 
                VALUES ('$artist_id', '$title', '$desc', '$path', '$category_id')";
        if (mysqli_query($conn, $sql)) {
            $message = "Artwork uploaded successfully!";
        } else {
            $error = "Database error.";
        }
    } else {
        $error = "Failed to upload image.";
    }
}
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

?>

<h2>Upload New Artwork</h2>

<div class="form-container">
    <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="forms">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" rows="3" required></textarea>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <div class="text-center">
            <button type="submit" class="btn">Upload</button>
        </div>
    </form>

</div>

</body>

</html>