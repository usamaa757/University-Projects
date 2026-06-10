<?php
include 'header.php';
include '../config/database.php';

// Fetch accepted receptionists
$accepted_receptionists = $conn->query("SELECT * FROM receptionists WHERE status = 'accepted' ORDER BY name");
?>

<div class="container mt-4 border rounded shadow p-4">
    <h3 class="text-center">Receptionists</h3>

    <?php if ($accepted_receptionists->num_rows > 0): ?>
    <table class="table table-bordered">
        <thead class="table-success">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th style="width: 200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($doc = $accepted_receptionists->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($doc['name']) ?></td>
                <td><?= htmlspecialchars($doc['email']) ?></td>
                <td>
                    <!-- Edit Button -->
                    <a href="edit_receptionist.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>

                    <!-- Delete Button with confirmation -->
                    <a href="delete_user.php?id=<?= $doc['id'] ?>&type=receptionist" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this receptionist?');">
                        <i class="bi bi-trash"></i> Delete
                    </a>


                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="alert alert-info">No accepted receptionists yet.</div>
    <?php endif; ?>
</div>