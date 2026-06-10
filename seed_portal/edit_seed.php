<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['agent_id'])) {
    header("Location: agent_login.php");
    exit();
}

$agent_id = $_SESSION['agent_id'];
$message = '';

if (!isset($_GET['id'])) {
    header("Location: agent_dashboard.php");
    exit();
}

$seed_id = $_GET['id'];

// Fetch seed details
$result = $conn->query("SELECT * FROM seeds WHERE seed_id='$seed_id' AND agent_id='$agent_id'");
if ($result->num_rows == 0) {
    die(" Seed not found or unauthorized access.");
}
$seed = $result->fetch_assoc();

// Update seed on form submit
if (isset($_POST['update'])) {
    $seed_name = $_POST['seed_name'];
    $category = $_POST['category'];
    $variety = $_POST['variety'];
    $price = $_POST['price_per_kg'];
    $quantity = $_POST['quantity_available'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    // Handle optional image update
    $image_sql = '';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target = "uploads/" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
        $image_sql = ", image='$image_name'";
    }

    $update_sql = "
        UPDATE seeds 
        SET seed_name='$seed_name', category='$category', variety='$variety', 
            description='$description', price_per_kg='$price', quantity_available='$quantity',
            status='$status' $image_sql
        WHERE seed_id='$seed_id' AND agent_id='$agent_id'
    ";

    if ($conn->query($update_sql)) {
        $message = "Seed updated successfully!";
        header("refresh:2; url=agent_dashboard.php");
    } else {
        $message = "Error updating seed: " . $conn->error;
    }
}
?>
<div class="container">

    <h2 style="text-align:center;">🌿 Edit Seed Details</h2>
    <p style="color:green; text-align:center;"><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data">
        <label>Seed Name:</label>
        <input type="text" name="seed_name" value="<?php echo htmlspecialchars($seed['seed_name']); ?>" required>

        <label>Category:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($seed['category']); ?>" required>

        <label>Variety:</label>
        <input type="text" name="variety" value="<?php echo htmlspecialchars($seed['variety']); ?>">

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($seed['description']); ?></textarea>

        <label>Price (PKR/kg):</label>
        <input type="number" name="price_per_kg" value="<?php echo htmlspecialchars($seed['price_per_kg']); ?>"
            required>

        <label>Quantity (kg):</label>
        <input type="number" name="quantity_available"
            value="<?php echo htmlspecialchars($seed['quantity_available']); ?>" required>

        <label>Status:</label>
        <select name="status">
            <option value="Available" <?php if ($seed['status'] == 'Available') echo 'selected'; ?>>Available</option>
            <option value="Unavailable" <?php if ($seed['status'] == 'Unavailable') echo 'selected'; ?>>Unavailable
            </option>
        </select>

        <label>Change Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <br><br>
        <button type="submit" name="update" class="btn">Update Seed</button>
        <a href="agent_dashboard.php" class="btn" style="margin-left:10px;">Back</a>
    </form>
</div>

</body>

</html>