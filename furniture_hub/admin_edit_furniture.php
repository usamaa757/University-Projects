<?php
include("config.php");
include("navbar.php");

if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$msg = $error = '';
// Get furniture id
if (!isset($_GET['id'])) {
    header("Location: management.php");
    exit();
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM furniture WHERE id=$id");
$item = mysqli_fetch_assoc($result);

// Update
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    mysqli_query($conn, "UPDATE furniture SET name='$name', price='$price', description='$description' WHERE id=$id");
    $msg = "Furniture updated successfully!";
}
?>

<div class="form-container">
    <h2>Edit Furniture</h2>
    <?php if (!empty($msg)): ?>
        <div class="success-box">
            <p><?php echo $msg; ?></p>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
        <input type="number" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
        <button type="submit" name="update">Update</button>
    </form>
</div>
</body>

</html>