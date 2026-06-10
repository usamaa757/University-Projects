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

// Delete furniture
if (isset($_GET['delete_furniture'])) {
    $id = $_GET['delete_furniture'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id=$id");
    $msg = "Furniture deleted successfully!";
}
?>

<div class="admin-container">
    <h2>Management</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>
    <!-- users Table -->
    <h3>Registered users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php
        $users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin'");
        while ($row = mysqli_fetch_assoc($users)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td class='action-links'>
                        <a href='edit_user.php?id={$row['id']}'>Edit</a>
                        <a href='management.php?delete_user={$row['id']}' onclick=\"return confirm('Delete this user?');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>

    <!-- Furniture Table -->
    <h3>All Furniture Items</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php
        $furniture = mysqli_query($conn, "SELECT * FROM furniture");
        while ($row = mysqli_fetch_assoc($furniture)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>\${$row['price']}</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td class='action-links'>
                        <a href='admin_edit_furniture.php?id={$row['id']}'>Edit</a>
                        <a href='admin.php?delete_furniture={$row['id']}' onclick=\"return confirm('Delete this item?');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>

</html>