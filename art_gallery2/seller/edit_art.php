<?php
include '../db.php';
include 'header.php';

$art_id = $_GET['art_id'] ?? null;

if ($art_id) {
    $query = "SELECT * FROM art_items WHERE art_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $art_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $art_id = $_POST['art_id']; // Ensure `art_id` is available in POST request
    $art_name = $_POST['art_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        $update_query = "UPDATE art_items SET art_name = ?, description = ?, price = ?, image = ? WHERE art_id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssdsi", $art_name, $description, $price, $image, $art_id);
    } else {
        $update_query = "UPDATE art_items SET art_name = ?, description = ?, price = ? WHERE art_id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssdi", $art_name, $description, $price, $art_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Art updated successfully!'); window.location='view_art.php';</script>";
    } else {
        echo "<script>alert('Update failed. Try again!');</script>";
    }
}
?>

<div class="container mt-5 border rounded shadow">
    <h2 class="text-center">Edit Art</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" enctype="multipart/form-data" class="card p-4 shadow">
                <input type="hidden" name="art_id" value="<?php echo $art_id; ?>">

                <div class="mb-3">
                    <label class="form-label">Art Name</label>
                    <input type="text" name="art_name" class="form-control" value="<?php echo $row['art_name']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                        required><?php echo $row['description']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control">
                    <small>Current Image: <?php echo $row['image']; ?></small>
                </div>
                <button type="submit" class="btn btn-success w-100">Update Art</button>
            </form>
        </div>
    </div>
</div>