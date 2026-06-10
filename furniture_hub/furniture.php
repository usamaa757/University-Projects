<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$msg = $error = '';

$user_id = $_SESSION['user_id'];

// Add furniture
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $desc = trim($_POST['description']);

    if (!empty($name) && !empty($price)) {
        $sql = "INSERT INTO furniture (seller_id, name, price, description)
                VALUES ('$user_id', '$name', '$price', '$desc')";
        mysqli_query($conn, $sql);
        $msg = "Furniture added successfully!";
    }
}

// Delete furniture
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM furniture WHERE id=$id AND seller_id='$user_id'");
    $msg = "Furniture deleted successfully!";
}
?>

<div class="container">
    <h2>Manage Your Furniture</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Furniture Name" required>
        <input type="number" name="price" placeholder="Price (USD)" required>
        <textarea name="description" placeholder="Description"></textarea>
        <button type="submit" name="add">Add Furniture</button>
    </form>

    <h3>Your Furniture List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM furniture WHERE seller_id='$user_id'");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>\${$row['price']}</td>
                    <td>{$row['description']}</td>
                    <td class='action-links'>
                        <a href='furniture_edit.php?id={$row['id']}'>Edit</a>
                        <a href='furniture.php?delete={$row['id']}' onclick=\"return confirm('Delete this item?');\">Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>

</html>