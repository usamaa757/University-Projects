<?php
include '../db.php';
include 'header.php';

// Add Category
if (isset($_POST['add_category'])) {
    $name = $_POST['category_name'];
    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM categories WHERE category_id = $id");
}

// Edit Category
if (isset($_POST['update_category'])) {
    $id = $_POST['category_id'];
    $name = $_POST['category_name'];
    $stmt = $conn->prepare("UPDATE categories SET category_name=? WHERE category_id=?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Manage Categories</h3>

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
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['category_id'] ?></td>
                <td><?= $row['category_name'] ?></td>
                <td>
                    <!-- Edit Modal Trigger -->
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#editModal<?= $row['category_id'] ?>">Edit</button>
                    <a href="?delete=<?= $row['category_id'] ?>" onclick="return confirm('Delete this category?')"
                        class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $row['category_id'] ?>" tabindex="-1"
                aria-labelledby="editModalLabel<?= $row['category_id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="post" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel<?= $row['category_id'] ?>">Edit Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="category_id" value="<?= $row['category_id'] ?>">
                            <div class="mb-3">
                                <label for="category_name<?= $row['category_id'] ?>">Category Name</label>
                                <input type="text" name="category_name" class="form-control"
                                    id="category_name<?= $row['category_id'] ?>" value="<?= $row['category_name'] ?>"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update_category" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>


            <?php endwhile; ?>
        </tbody>
    </table>
</div>