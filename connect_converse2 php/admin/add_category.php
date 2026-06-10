<?php
include '../db.php';
include 'header.php';

$msg = "";
$error = "";

// Add Category
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $check = mysqli_query($conn, "SELECT * FROM categories WHERE category_name = '$name'");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO categories (category_name) VALUES ('$name')");
            $msg = "Category added successfully.";
        } else {
            $error = "Category already exists.";
        }
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM categories WHERE category_id = $id");
    $msg = "Category deleted.";
}

// Edit Category
if (isset($_POST['update_category'])) {
    $id = intval($_POST['category_id']);
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        mysqli_query($conn, "UPDATE categories SET category_name = '$name' WHERE category_id = $id");
        $msg = "Category updated.";
    } else {
        $error = "Category name cannot be empty.";
    }
}

// Fetch all categories
$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id DESC");
?>

<div class="container mt-5 shadow rounded border p-4">
    <h3 class="text-center mb-4">Manage Categories</h3>

    <?php if ($msg): ?>
        <p class="text-success text-center"><?= $msg ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="text-danger text-center"><?= $error ?></p>
    <?php endif; ?>

    <!-- Add Category Form -->
    <form method="post" class="mb-4">
        <div class="row g-2">
            <div class="col-md-10">
                <input type="text" name="category_name" class="form-control" placeholder="Category Name" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_category" class="btn btn-success w-100">Add</button>
            </div>
        </div>
    </form>

    <!-- Categories Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['category_id'] ?></td>
                    <td>
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['category_id']): ?>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                                <input type="text" name="category_name" class="form-control me-2" value="<?= htmlspecialchars($row['category_name']) ?>" required>
                                <button type="submit" name="update_category" class="btn btn-success btn-sm">Save</button>
                            </form>
                        <?php else: ?>
                            <?= htmlspecialchars($row['category_name']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?edit=<?= $row['category_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="?delete=<?= $row['category_id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
