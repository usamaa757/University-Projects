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
// Handle block 
if (isset($_GET['block_furniture'])) {
    $furniture_id = intval($_GET['block_furniture']);
    mysqli_query($conn, "UPDATE furniture SET status='block' WHERE id='$furniture_id'");
    $msg = "Furniture blocked successfully!";
}

if (isset($_GET['active'])) {
    $furniture_id = intval($_GET['active']);
    mysqli_query($conn, "UPDATE furniture SET status='available' WHERE id='$furniture_id'");
    $msg = "Furniture active successfully!";
}

// Delete furniture
if (isset($_GET['delete_furniture'])) {
    $id = $_GET['delete_furniture'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id=$id");
    $msg = "Furniture deleted successfully!";
}
?>

<div class="admin-container">
    <?php if (!empty($msg)): ?>
        <div class="success-box"><?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <!-- Furniture Table -->
    <h3>All Furniture Items</h3>
    <table>
        <tr>
            <th>Namae</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $furniture = mysqli_query($conn, "SELECT * FROM furniture");
        while ($row = mysqli_fetch_assoc($furniture)) {
            $img = $row['image'];
            echo "<tr>
                    <td>
                     <img src='uploads/$img' width='80' height='80' style='border-radius:5px; object-fit:cover;'>

                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>\${$row['price']}</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . htmlspecialchars($row['status']) . "</td>
                    <td class='action-links'>";
            if ($row['status'] != 'sold'):
                echo "<a href='admin.php?delete_furniture={$row['id']}' onclick=\"return confirm('Delete this item?');\">Delete</a>";
            else:
                echo " <strong>Sold Item</strong>";
            endif;
            if ($row['status'] == 'available') {

                echo "<a href='manage_furniture.php?block_furniture={$row['id']}' onclick=\"return confirm('Block this Furniture?');\">Block Furniture</a>";
            } elseif ($row['status'] == 'block') {
                echo "<a href='manage_furniture.php?active={$row['id']}' onclick=\"return confirm('Active this Furniture?');\">Active Furniture</a>";
            }
            echo "  </td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>

</html>