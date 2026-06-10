<?php
include 'header.php';
include '../db.php';

if (!isset($_GET['service_id'])) {
    echo "No service selected.";
    exit;
}

$service_id = $_GET['service_id'];
$query = "SELECT * FROM services WHERE service_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $service_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$service = mysqli_fetch_assoc($result);

if (!$service) {
    echo "service not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = $_POST['service_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $update_query = "UPDATE services SET service_name = ?, category = ?, description = ?, price = ? WHERE service_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssdi", $service_name, $category, $description, $price, $service_id);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Service updated successfully!'); window.location.href='edit_service.php?service_id=$service_id';</script>";
    } else {
        echo "Error updating service.";
    }
}
?>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Edit service</h2>
    </div>

    <form method="POST" class="form">
        <label>Service Name:</label>
        <input type="text" name="service_name" value="<?= htmlspecialchars($service['service_name']) ?>"
            required><br><br>

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($service['category']) ?>" required><br><br>

        <label>Description:</label>
        <input type="text" name="description" value="<?= htmlspecialchars($service['description']) ?>" required><br><br>

        <label>Price ($):</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($service['price']) ?>"
            required><br><br>

        <div class="btn-div">
            <button type="submit" class="btn">Update</button>
        </div>

    </form>
</div>
</body>

</html>