<?php
include 'header.php';

include '../config/database.php';


// Handle update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $description = $_POST['description'];


    $stmt = $conn->prepare("UPDATE medicines SET name=?, type=?, description=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $type, $description, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all medicines
$result = $conn->query("SELECT * FROM medicines");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Medicines</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5 border rounded shadow p-4">
        <h3 class="mb-4 text-center">Manage Medicines</h3>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Description</th>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td><input type="text" name="name" class="form-control"
                                value="<?= htmlspecialchars($row['name']) ?>"></td>
                        <td><input type="text" name="type" class="form-control"
                                value="<?= htmlspecialchars($row['type']) ?>"></td>
                        <td><input type="text" name="description" class="form-control"
                                value="<?= htmlspecialchars($row['description']) ?>"></td>

                        <td style="white-space: nowrap;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="update" class="btn btn-success btn-sm">Update</button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this medicine?');">Delete</button>
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php $conn->close(); ?>