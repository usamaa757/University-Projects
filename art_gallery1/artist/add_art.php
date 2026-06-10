<?php include '../db.php';
include 'header.php';


$artist_id =  $_SESSION['user_id'];
if (isset($_POST['addArt'])) {
    $art_name = $_POST['art_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    // Handle image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $imagePath = "../images/art_gallery/" . basename($imageName);

    if (move_uploaded_file($imageTmp, $imagePath)) {
        $sql = "INSERT INTO arts (artist_id, art_name, description, price, image) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issds", $artist_id, $art_name, $description, $price, $imagePath);

        if ($stmt->execute()) {
            echo "<script>alert('Art added successfully!');</script>";
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
                <h3 class="text-center">Add Art</h3>
                <div class="card-body">


                    <!-- Form for adding art -->
                    <form action="" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="art_name" class="form-label">Art Name</label>
                            <input type="text" class="form-control" id="art_name" name="art_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="addArt" class="btn">Add Art</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>