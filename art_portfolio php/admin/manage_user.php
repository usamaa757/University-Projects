<?php
include 'header.php';
include '../db.php';
$message = '';
$error = '';
// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Prevent deleting admin
    $check_user = mysqli_query($conn, "SELECT role FROM users WHERE user_id = '$delete_id'");
    $user = mysqli_fetch_assoc($check_user);

    if ($user && $user['role'] != 'admin') {
        // Delete comments first to avoid foreign key issues
        mysqli_query($conn, "DELETE FROM comments WHERE user_id = '$delete_id'");
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id = '$delete_id'");
        mysqli_query($conn, "DELETE FROM follows WHERE user_id = '$delete_id'");

        // Delete the user
        if (mysqli_query($conn, "DELETE FROM users WHERE user_id = '$delete_id'")) {
            $message = "User and related data deleted successfully.";
        } else {
            $message = "Failed to delete user.";
        }
    } else {
        $message = "Cannot delete admin account.";
    }
}


$result = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin'");
?>

<h2>Manage Artists and Users</h2>

<a href="add_user.php" class="btn">Add New User</a><br><br>

<table>
    <?php if ($message): ?>
    <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
    <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <a class="btn" href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a>
                <a class="btn" href="?delete=<?= $row['user_id'] ?>"
                    onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>

</html>