<?php
include 'header.php';
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}
$success = "";
$error = "";
$agent_id = $_SESSION['user_id'];
$property_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($property_id <= 0) {
    $error = "Invalid property ID.";
}

// Fetch property
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ? AND agent_id = ?");
$stmt->bind_param("ii", $property_id, $agent_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    $error = "Property not found or access denied.";
}

// Fetch features
$features = [];
$feat_stmt = $conn->prepare("SELECT feature FROM property_features WHERE property_id = ?");
$feat_stmt->bind_param("i", $property_id);
$feat_stmt->execute();
$feat_result = $feat_stmt->get_result();
while ($row = $feat_result->fetch_assoc()) {
    $features[] = $row['feature'];
}

// Fetch images
$images = [];
$img_stmt = $conn->prepare("SELECT id, image_path FROM property_images WHERE property_id = ?");
$img_stmt->bind_param("i", $property_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
while ($row = $img_result->fetch_assoc()) {
    $images[] = $row;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $listing_type = $_POST['listing_type'];
    $city = $_POST['city'];
    $price = $_POST['price'];
    $address = $_POST['address'];
    $new_status = $_POST['new_status'];
    $features = $_POST['features'] ?? [];
    $delete_images = $_POST['delete_images'] ?? [];

    // Update property
    $stmt = $conn->prepare("UPDATE properties SET title=?, description=?, type=?, listing_type=?, city=?, price=?, address= ?, status = ? WHERE id=? AND agent_id=?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        die;
    }

    $stmt->bind_param("sssssssssi", $title, $description, $type, $listing_type, $city, $price, $address, $new_status, $property_id, $agent_id);

    if (!$stmt->execute()) {
        $error = "Update failed: " . $stmt->error;
        die;
    }

    // Delete selected images
    foreach ($delete_images as $img_id) {
        if (!is_numeric($img_id)) continue;

        $img_id = (int)$img_id;

        $img_del = $conn->prepare("SELECT image_path FROM property_images WHERE id=? AND property_id=?");
        $img_del->bind_param("ii", $img_id, $property_id);
        $img_del->execute();
        $img_res = $img_del->get_result()->fetch_assoc();

        if ($img_res && file_exists($img_res['image_path'])) {
            unlink($img_res['image_path']);
        }

        $delete_stmt = $conn->prepare("DELETE FROM property_images WHERE id=? AND property_id=?");
        $delete_stmt->bind_param("ii", $img_id, $property_id);
        $delete_stmt->execute();
    }

    // Delete old features
    $delete_feat = $conn->prepare("DELETE FROM property_features WHERE property_id = ?");
    $delete_feat->bind_param("i", $property_id);
    $delete_feat->execute();

    // Insert new features
    foreach ($features as $feature) {
        $feature = trim($feature);
        if (!empty($feature)) {
            $stmt_feat = $conn->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
            $stmt_feat->bind_param("is", $property_id, $feature);
            $stmt_feat->execute();
        }
    }

    // Handle image uploads
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
            if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK) continue;

            $filename = basename($_FILES['images']['name'][$index]);
            $target_file = $upload_dir . time() . "_" . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt_img = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                $stmt_img->bind_param("is", $property_id, $target_file);
                $stmt_img->execute();
            }
        }
    }

    $success = " Property updated successfully!";
}
?>


<div class="container">
    <section class="section">


        <form action="" method="POST" enctype="multipart/form-data">
            <div class="section-header">

                <h2>Edit Property</h2>
            </div>

            <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
            <?php elseif ($error): ?>
            <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($property['title']) ?>" required>

            <label>Description</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($property['description']) ?></textarea>

            <label>Type</label>
            <select name="type">
                <option value="House" <?= $property['type'] == 'House' ? 'selected' : '' ?>>House</option>
                <option value="Plot" <?= $property['type'] == 'Plot' ? 'selected' : '' ?>>Plot</option>
                <option value="Commercial" <?= $property['type'] == 'Commercial' ? 'selected' : '' ?>>Commercial
                </option>
            </select>

            <label>Listing Type</label>
            <select name="listing_type" required>
                <option value="Buy" <?= $property['listing_type'] == 'Buy' ? 'selected' : '' ?>>For Sale</option>
                <option value="Rent" <?= $property['listing_type'] == 'Rent' ? 'selected' : '' ?>>For Rent</option>
            </select>

            <label>City</label>
            <input type="text" name="city" value="<?= htmlspecialchars($property['city']) ?>" required>

            <label>Price ($)</label>
            <input type="number" name="price" value="<?= $property['price'] ?>" required>

            <label>Address</label>
            <textarea name="address" rows="2" required><?= htmlspecialchars($property['address']) ?></textarea>
            <label for="status">Status</label>

            <select name="new_status">
                <option value="available" <?= $property['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                <option value="rent" <?= $property['status'] == 'Rent' ? 'selected' : '' ?>>Rent</option>
                <option value="sold" <?= $property['status'] == 'Sold' ? 'selected' : '' ?>>Sold</option>
            </select>
            <label>Property Features</label>
            <div id="features-container">
                <?php if (count($features) > 0): ?>
                <?php foreach ($features as $feat): ?>
                <input type="text" name="features[]" value="<?= htmlspecialchars($feat) ?>" required>
                <?php endforeach; ?>
                <?php else: ?>
                <input type="text" name="features[]" placeholder="Add another feature">
                <?php endif; ?>

                <input type="text" name="features[]" placeholder="Add another feature">
            </div>
            <button type="button" class="btn" onclick="addFeature()">Add Feature</button><br>

            <label>Existing Images</label><br>
            <div style="display: flex; flex-wrap: wrap;">
                <?php foreach ($images as $img): ?>
                <div class="image-box">
                    <img src="<?= $img['image_path'] ?>" alt="Property Image">
                    <label>
                        <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>"> Delete
                    </label>
                </div>
                <?php endforeach; ?>
            </div>


            <label>Upload New Images</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <div class="text-center">

                <button type="submit" class="btn">Update Property</button>
            </div>
        </form>
    </section>
</div>
<?php include '../footer.php'; ?>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'features[]';
    input.placeholder = 'Another feature';
    container.appendChild(input);
}
</script>