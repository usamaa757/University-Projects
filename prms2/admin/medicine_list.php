<?php
include 'sidebar.php';
include '../db.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $medicine_id = $_GET['delete'];
    $conn->query("DELETE FROM medicines WHERE medicine_id = $medicine_id");
    echo "<script>alert('Medication deleted successfully');window.location='medicine_list.php';</script>";
}

// Fetch all medicines
$result = $conn->query("SELECT * FROM medicines");
?>

<div class="main-content">
    <div class="container p-3 card">
        <h2 class="mb-4 text-center">Medicines</h2>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['medicine_id'] ?></td>
                        <td><?= $row['medicine_name'] ?></td>
                        <td><?= $row['dosage'] ?></td>
                        <td><?= $row['frequency'] ?></td>
                        <td>
                            <a href="edit_medicine.php?medicine_id=<?= $row['medicine_id'] ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <a href="?delete=<?= $row['medicine_id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile ?>
                </tbody>
            </table>

        </div>

        </html>