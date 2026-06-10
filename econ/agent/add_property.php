<?php
include 'header.php';
include '../db.php';


$agent_id = $_SESSION['user_id'];
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $city = $_POST['city'];
    $price = $_POST['price'];
    $address = $_POST['address'];
    $listing_type = $_POST['listing_type'];
    $features = $_POST['features'] ?? [];

    // Insert property
    $stmt = $conn->prepare("INSERT INTO properties (title, agent_id, description, type, listing_type, city, price, address) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssis", $title, $agent_id, $description, $type, $listing_type, $city, $price, $address);

    if ($stmt->execute()) {
        $property_id = $stmt->insert_id;

        // Insert features
        foreach ($features as $feature) {
            $feature = trim($feature);
            if (!empty($feature)) {
                $stmt_feat = $conn->prepare("INSERT INTO property_features (property_id, feature) VALUES (?, ?)");
                $stmt_feat->bind_param("is", $property_id, $feature);
                $stmt_feat->execute();
            }
        }

        // Upload images
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
            $filename = basename($_FILES['images']['name'][$index]);
            $target_file = $upload_dir . time() . "_" . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt_img = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                $stmt_img->bind_param("is", $property_id, $target_file);
                $stmt_img->execute();
            }
        }

        $success = "Property added successfully!";
    } else {
        $success = "Failed to add property: " . $stmt->error;
    }
}
$conn->close();
?>



<div class="container">
    <section class="section">
        <div class="section-header">

            <h2>Add New Property</h2>
        </div>
        <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        <form action="add_property.php" method="POST" enctype="multipart/form-data">

            <label>Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Type</label>
            <select name="type">
                <option value="House">House</option>
                <option value="Plot">Plot</option>
                <option value="Commercial">Commercial</option>
            </select>
            <label>Listing Type</label>
            <select name="listing_type" required>
                <option value="" disabled selected>-- Select --</option>
                <option value="Buy">For Sale</option>
                <option value="Rent">For Rent</option>
            </select>

            <label>Property Features</label>
            <div id="features-container">
                <input type="text" name="features[]" placeholder="e.g. 3 Bedrooms" required>
            </div>
            <button type="button" class="btn" onclick="addFeature()">Add Another Feature</button><br>

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

            <label>City</label>
            <input type="text" name="city" required>

            <label>Price ($)</label>
            <input type="number" name="price" required>

            <label>Address</label>
            <textarea name="address" rows="2" required></textarea>

            <label>Upload Images</label>
            <input type="file" name="images[]" multiple required accept="image/*">
            <div class="text-center">

                <button type="submit" class="btn">Add Property</button>
            </div>
        </form>
    </section>
</div>
<?php include '../footer.php'; ?>

</html>