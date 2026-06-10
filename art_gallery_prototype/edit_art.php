<?php

include 'db.php';
include 'header.php';
if (!isset($_SESSION['role']) == 'seller') {
    header("Location: login.php");
}

if (isset($_GET['art_id'])) {
    $art_id = $_GET['art_id'];
    $query = "SELECT * FROM art_items WHERE art_id = '$art_id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $update_query = "UPDATE art_items SET name='$name', description='$description', price='$price' WHERE art_id='$art_id'";
    if ($conn->query($update_query)) {
        echo "<script>alert('Art updated successfully!'); window.location='view_art.php';</script>";
    } else {
        echo "<script>alert('Update failed. Try again!');</script>";
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center">Edit Art</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" class="card p-4 shadow">
                <div class="mb-3">
                    <label class="form-label">Art Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
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
                <button type="submit" class="btn btn-success w-100">Update Art</button>
            </form>
        </div>
    </div>
</div>