<?php
include '../db.php';
include 'header.php';

$msg = "";

// Delete user
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    $msg = "User deleted successfully.";
}

// Fetch all users (you can add WHERE status = 'Pending' if needed)
$result = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY user_id DESC");
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Manage Users</h3>
    <div class="table-responsive">
        <?php if ($msg): ?>
        <p class="text-success text-center"><?= $msg ?></p>
        <?php endif; ?>

        <table class="table table-bordered table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>

                    <td>
                        <a href="edit_user.php?user_id=<?= $row['user_id'] ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="manage_users.php?delete=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this user?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>