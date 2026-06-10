<?php
include '../db.php';
include 'header.php';

// Handle user status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['update_status'];

    if ($conn->query("UPDATE users SET status = '$new_status' WHERE user_id = $user_id")) {
        echo "<script>alert('User status updated.'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Failed to update status.'); window.location.href = 'manage_users.php';</script>";
    }
    exit();
}

// Fetch all users
$users = $conn->query("SELECT * FROM users WHERE status = 'pending' ORDER BY user_id DESC");
?>

<div class="container my-5">
    <div class="card shadow border-0 rounded-4">
        <h3 class="text-center mb-4">Manage Users</h3>
        <div class="card-body table-responsive">
            <table class="table table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span
                                class="badge 
                                <?= $user['status'] == 'approved' ? 'bg-success' : ($user['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                                <?= ucfirst($user['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="post" class="d-flex justify-content-center gap-2">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" name="update_status" value="approved"
                                    class="btn btn-sm">Approve</button>
                                <button type="submit" name="update_status" value="rejected"
                                    class="btn btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($users->num_rows == 0): ?>
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>