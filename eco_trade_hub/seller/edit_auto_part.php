<?php
include("../db_connection.php");
include("header.php");

if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];
$part_id = isset($_GET['part_id']) ? intval($_GET['part_id']) : 0;

if ($part_id === 0) {
    echo "Invalid part ID.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $part_name = trim($_POST['part_name']);
    $condition = trim($_POST['condition']);
    $price = floatval($_POST['price']);
    $location = trim($_POST['location']);
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $part_image = $_FILES['part_image'];

    // Check if a new image was uploaded
    if ($part_image['error'] === UPLOAD_ERR_OK) {
        $target_dir = 'uploads/';
        $imageName = $target_dir . basename($part_image['name']);
        $imagePath = $imageName;
        // Ensure the 'uploads' directory exists
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);
        }

        // Move the uploaded file to the server directory
        if (move_uploaded_file($part_image['tmp_name'], $imagePath)) {
            $imageUrl = basename($part_image['name']);
        } else {
            echo "Failed to upload image.";
            exit();
        }
    } else {
        // If no new image is uploaded, keep the existing one
        $imageUrl = $part['images'];
    }

    $sql = "UPDATE auto_parts SET part_name = ?, `condition` = ?, price = ?, location = ?, make = ?, model = ?, images = ?, created_at = NOW() WHERE part_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssssii", $part_name, $condition, $price, $location, $make, $model, $imageUrl, $part_id, $seller_id);

    if ($stmt->execute()) {
        header("Location: change_parts_detail.php?msg=" . urlencode("Part updated successfully."));
        exit();
    } else {
        echo "Error updating part: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM auto_parts WHERE part_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $part_id, $_SESSION['seller_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Part not found.";
        exit();
    }

    $part = $result->fetch_assoc();
}

?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="border shadow bg-white rounded">
                <h3 class="text-center heading-bg bg-dark text-white p-2">Edit Part</h3>
                <div class="p-3">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="part_name">Part Name:</label>
                            <input type="text" class="form-control" id="part_name" name="part_name" value="<?php echo htmlspecialchars($part['part_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="part_description">Condition:</label>
                            <input type="text" class="form-control" id="condition" name="condition" value="<?php echo htmlspecialchars($part['condition']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($part['price']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location:</label>
                            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($part['location']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Make:</label>
                            <input type="text" class="form-control" id="make" name="make" value="<?php echo htmlspecialchars($part['make']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Model:</label>
                            <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($part['model']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="part_image">Upload New Image:</label>
                            <input type="file" class="form-control-file" id="part_image" accept="image/*" name="part_image">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Part</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>