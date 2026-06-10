<?php
include 'header.php';

include '../db.php';
// Handle activate/deactivate request
if (isset($_GET['action'], $_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $newStatus = $_GET['action'] === 'deactivate' ? 'inactive' : 'active';

    $update = $conn->prepare("UPDATE users SET status=? WHERE user_id=?");
    $update->bind_param("si", $newStatus, $userId);
    $update->execute();
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['delet_action'], $_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    $delete = $conn->prepare("DELETE FROM prayer_records WHERE user_id= ?");
    $delete->bind_param("i", $userId);

    if ($delete->execute()) {

        $delete = $conn->prepare("DELETE FROM users WHERE user_id= ?");
        $delete->bind_param("i", $userId);
        if ($delete->execute()) {
            echo "<script>alert('User delete successfully!');window.location.href='manage_users.php';</script>";
            exit();
        }
    }
}

// Fetch all users
$users = $conn->query("SELECT *  FROM users ORDER BY user_id DESC");
?>

<!-- Page Content -->
<div class="container mt-5">
    <h3 class="mb-4">Manage Users</h3>

    <table class="table table-bordered table-hover table-striped  text-center">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows > 0): ?>
            <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'danger' ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] === 'active'): ?>
                    <a href="?action=deactivate&user_id=<?= $row['user_id'] ?>"
                        class="btn btn-sm btn-warning">Deactivate</a>
                    <?php else: ?>
                    <a href="?action=activate&user_id=<?= $row['user_id'] ?>"
                        class="btn btn-sm btn-success">Activate</a>
                    <?php endif; ?>
                    <a href="?delet_action=delete&user_id=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure to delete this user')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No users found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>

</html>