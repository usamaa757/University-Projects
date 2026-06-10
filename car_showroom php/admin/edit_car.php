<?php
include '../db.php';
include 'header.php';

$car_id = $_GET['car_id'];
$car = $conn->query("SELECT * FROM cars WHERE car_id = $car_id")->fetch_assoc();

if (isset($_POST['updateCar'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $features = $_POST['features'];

    // Handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $uploadDir = 'uploads/';

        if (move_uploaded_file($imageTmp, $uploadDir . $imageName)) {
            $imagePath = $uploadDir . $imageName;

            // Update with image
            $conn->query("UPDATE cars SET 
                            brand='$brand', 
                            model='$model', 
                            price='$price', 
                            features='$features', 
                            image='$imagePath' 
                          WHERE car_id=$car_id");
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    } else {
        // Update without changing image
        $conn->query("UPDATE cars SET 
                        brand='$brand', 
                        model='$model', 
                        price='$price', 
                        features='$features' 
                      WHERE car_id=$car_id");
    }

    if (empty($error)) {
        echo "<script>alert('Car updated successfully!'); window.location.href = 'car_list.php';</script>";
        // Refresh the data after update
        $car = $conn->query("SELECT * FROM cars WHERE car_id = $car_id")->fetch_assoc();
    }
}

?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-3">
                <h3 class="text-center">Edit Car</h3>
                <div class="card-body">

                    <!-- Update Car Form -->
                    <form method="POST" enctype="multipart/form-data" class="form">
                        <div class="mb-3">
                            <label for="brand" class="form-label">Car Brand:</label>
                            <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($car['brand']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Model:</label>
                            <input type="text" id="model" name="model" value="<?= htmlspecialchars($car['model']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price:</label>
                            <input type="number" id="price" name="price" value="<?= htmlspecialchars($car['price']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status:</label>
                            <input type="text" id="status" name="status" value="<?= htmlspecialchars($car['status']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="features" class="form-label">Features:</label>
                            <textarea id="features" name="features" class="form-control" rows="4"
                                required><?= htmlspecialchars($car['features']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload Image (Optional):</label>
                            <input type="file" id="image" name="image" class="form-control">
                        </div>
                        <div class="text-center">

                            <button name="updateCar" class="btn">Update Car</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>