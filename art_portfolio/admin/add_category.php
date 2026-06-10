<?php
include 'header.php';
include '../db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = trim($_POST['name']);

    if (!empty($category_name)) {
        $category_name = mysqli_real_escape_string($conn, $category_name);

        // Check for duplicates
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE name = '$category_name'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Category already exists.";
        } else {
            if (mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category_name')")) {
                $message = "Category added successfully.";
            } else {
                $error = "Failed to add category.";
            }
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Delete related data first (if needed)
    mysqli_query($conn, "DELETE FROM artworks WHERE category_id = '$delete_id'");

    // Delete the artwork
    if (mysqli_query($conn, "DELETE FROM categories WHERE category_id = '$delete_id'")) {
        $message = "Category deleted successfully.";
    } else {
        $error = "Failed to delete Category.";
    }
}
// Fetch all categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id DESC");
?>

<h2>Add New Category</h2>

<?php if ($message): ?>
<p class="message"><?php echo $message; ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>
<div class="form-container">
    <form method="POST" class="forms">
        <label>Category Name</label>
        <input type="text" name="name" required>
        <button type="submit" class="btn">Add Category</button>
    </form>
</div>


<br>
<hr><br>

<h3>Existing Categories</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($categories)): ?>
        <tr>
            <td><?= $row['category_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>
                <a href="edit_category.php?id=<?= $row['category_id'] ?>" class="btn">Edit</a>

                <a href="?delete=<?= $row['category_id'] ?>" onclick="return confirm('Delete this category?')"
                    class="btn">Delete</a>

            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>

</html>