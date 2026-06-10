<?php
include 'header.php';
include '../db.php';

$id = $_GET['id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_name = mysqli_real_escape_string($conn, $new_name);

    if (!empty($new_name)) {
        if (mysqli_query($conn, "UPDATE categories SET name = '$new_name' WHERE category_id = $id")) {
            $message = "Category updated successfully.";
        } else {
            $error = "Failed to update category.";
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Fetch category
$result = mysqli_query($conn, "SELECT * FROM categories WHERE category_id = $id");
$category = mysqli_fetch_assoc($result);
?>

<h2>Edit Category</h2>

<?php if ($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>
<?php if ($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
    <label>Category Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
    <button type="submit" class="btn">Update Category</button>
</form>

</body>

</html>