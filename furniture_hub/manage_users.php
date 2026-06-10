<?php
include("config.php");
include("navbar.php");

// Only admin can access this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$msg = $error = '';

// Delete user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM furniture WHERE seller_id=$id");
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");

    $msg = "User deleted successfully!";
}

if (isset($_GET['block_user'])) {
    $block_user_id = intval($_GET['block_user']);
    mysqli_query($conn, "UPDATE users SET status='block' WHERE id='$block_user_id'");
    $msg = "User blocked successfully!";
}
if (isset($_GET['active'])) {
    $block_user_id = intval($_GET['active']);
    mysqli_query($conn, "UPDATE users SET status='active' WHERE id='$block_user_id'");
    $msg = "User activated successfully!";
}

// Approve or reject user actions
if (isset($_GET['approve_user'])) {
    $approve_id = intval($_GET['approve_user']);
    mysqli_query($conn, "UPDATE users SET status='active' WHERE id='$approve_id'");
    $msg = "User approved successfully!";
}

if (isset($_GET['reject_user'])) {
    $reject_id = intval($_GET['reject_user']);
    mysqli_query($conn, "DELETE FROM users WHERE id='$reject_id'");
    $msg = "User rejected successfully!";
}
?>
<div class="admin-container">
    <?php if (!empty($msg)): ?><?php echo $msg; ?>
</div>
<?php endif; ?>

<!-- Users Table -->
<h3>Registered Users</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php
    $users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' AND status != 'pending'");
    while ($row = mysqli_fetch_assoc($users)) {
        echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['role']) . "</td>
                    <td>" . htmlspecialchars($row['status']) . "</td>
                    <td class='action-links'>
                        <a href='manage_users.php?delete_user={$row['id']}' onclick=\"return confirm('Delete this user?');\">Delete</a>";
        if ($row['status'] == 'active') {

            echo "<a href='manage_users.php?block_user={$row['id']}' onclick=\"return confirm('Block this User?');\">Block User</a>";
        } elseif ($row['status'] == 'block') {
            echo "<a href='manage_users.php?active={$row['id']}' onclick=\"return confirm('Active this User?');\">Active User</a>";
        }
        echo "  </td>
                  </tr>";
    }
    ?>
</table>

<!-- Pending Registration Requests -->
<h3>Pending Registration Requests</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    <?php
    $pending_users = mysqli_query($conn, "SELECT * FROM users WHERE status='pending'");
    while ($row = mysqli_fetch_assoc($pending_users)) {
        echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['role']) . "</td>
                    <td class='action-links'>
                        <a href='manage_users.php?approve_user={$row['id']}' onclick=\"return confirm('Approve this user?');\">Approve</a>
                        <a href='manage_users.php?reject_user={$row['id']}' onclick=\"return confirm('Reject this user?');\">Reject</a>
                    </td>
                  </tr>";
    }
    ?>
</table>
</div>



</body>

</html>