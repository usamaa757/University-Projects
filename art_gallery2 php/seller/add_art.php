<?php include '../db.php';
include 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['art_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $seller_id = $_SESSION['user_id'];

    $image = $_FILES['image']['name'];

    $target = "uploads/" . basename($image);
    if (!is_dir('uploads')) {
        mkdir('uploads');
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $sql = "INSERT INTO art_items (seller_id, art_name, image, description, price) VALUES ('$seller_id', '$name', '$target', '$description', '$price')";
    $conn->query($sql);
    echo "<script>alert('Art Item Added!');</script>";
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6  rounded border shadow">
            <h3 class="text-center">Add Art Item</h3>
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Art Name</label>
                    <input type="text" name="art_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Add Art</button>
            </form>
        </div>
    </div>
</div>