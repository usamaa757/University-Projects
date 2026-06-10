<?php include '../db.php'; ?>
<?php include 'header.php'; ?>

<?php
$msg = '';
if (isset($_POST['addCar'])) {
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $features = $_POST['features'];

    // Handle image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = "../images/car_pic/" . basename($imageName);

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $sql = "INSERT INTO cars (brand, model, price, features, image) 
                VALUES ('$brand', '$model', '$price', '$features', '$imagePath')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Car added successfully!');</script>";
        } else {

            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image!');</script>";
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-3">
                <h3 class="text-center">Add Car</h3>
                <div class="card-body">


                    <?= $msg ?>


                    <!-- Form for adding car -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="brand" class="form-label">Car Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand" required>
                        </div>

                        <div class="mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label for="features" class="form-label">Car Features</label>
                            <textarea class="form-control" id="features" name="features" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>
                        <div class="text-center">

                            <button type="submit" name="addCar" class="btn">Add Car</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>