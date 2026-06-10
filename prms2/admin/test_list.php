<?php
include 'sidebar.php';
include '../db.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $test_id = $_GET['delete'];
    $conn->query("DELETE FROM tests WHERE test_id = $test_id");
    echo "<script>alert('Medication deleted successfully');window.location='test_list.php';</script>";
}

// Fetch all tests
$result = $conn->query("SELECT * FROM tests");
?>

<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Tests</h2>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>

                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['test_id'] ?></td>
                        <td><?= $row['test_name'] ?></td>

                        <td>
                            <a href="edit_test.php?test_id=<?= $row['test_id'] ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <a href="?delete=<?= $row['test_id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>

        </div>

        </html>