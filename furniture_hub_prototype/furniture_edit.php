<?php
include("config.php");
include("navbar.php");
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$msg = $error = '';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM furniture WHERE id=$id AND seller_id='$user_id'");
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "<script>alert('Invalid furniture item!'); window.location='furniture.php';</script>";
    exit();
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $sql = "UPDATE furniture SET name='$name', price='$price', description='$desc' WHERE id=$id AND seller_id='$user_id'";
    if (mysqli_query($conn, $sql)) {
        $msg = "Furniture updated successfully!";
    }
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
        <input type="number" name="price" value="<?php echo $item['price']; ?>" required>
        <textarea name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
        <button type="submit" name="update">Update</button>
    </form>
</div>

</body>

</html>