<?php
include '../db.php';
include 'header.php';

if (!isset($_GET['art_id'])) {
    echo "<script>alert('Art ID is required'); window.location.href = 'art_list.php';</script>";
    exit;
}


// Assume you're fetching art details
$art_id = $_GET['art_id']; // Get the art ID from URL or wherever it's set
$result = $conn->query("SELECT * FROM arts WHERE art_id = $art_id");
$art = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the form data
    $art_name = $_POST['art_name'];
    $price = $_POST['price'];

    // Handle image upload
    $image_path = $art['image']; // Default to current image if no new image is uploaded

    if (isset($_FILES['art_image']) && $_FILES['art_image']['error'] == 0) {
        // Get file details
        $image = $_FILES['art_image'];
        $image_name = $image['name'];
        $image_tmp = $image['tmp_name'];
        $image_size = $image['size'];
        $image_error = $image['error'];

        // Generate a new unique name for the image
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($image_ext), $allowed_extensions)) {
            // Create a unique file name and move the file to a designated directory
            $new_image_name = uniqid('', true) . '.' . $image_ext;
            $image_dir = '../images/art_gallery/'; // Make sure this directory is writable
            $image_path = $image_dir . $new_image_name;

            if (move_uploaded_file($image_tmp, $image_path)) {
                // Update the database with the new image path
                $update_query = "UPDATE arts SET art_name = '$art_name', price = '$price', image = '$image_path' WHERE art_id = $art_id";
                if ($conn->query($update_query)) {
                    echo "<script>alert('Art updated successfully'); window.location.href = 'art_list.php';</script>";
                } else {
                    echo "<script>alert('Failed to update art.'); window.location.href = 'edit_art.php?art_id=$art_id';</script>";
                }
            } else {
                echo "<script>alert('Error uploading the image.'); window.location.href = 'edit_art.php?art_id=$art_id';</script>";
            }
        } else {
            echo "<script>alert('Invalid image type. Only JPG, JPEG, PNG, GIF are allowed.'); window.location.href = 'edit_art.php?art_id=$art_id';</script>";
        }
    } else {
        // If no new image is uploaded, update without changing the image
        $update_query = "UPDATE arts SET art_name = '$art_name', price = '$price' WHERE art_id = $art_id";
        if ($conn->query($update_query)) {
            echo "<script>alert('Art updated successfully'); window.location.href = 'art_list.php';</script>";
        } else {
            echo "<script>alert('Failed to update art.'); window.location.href = 'edit_art.php?art_id=$art_id';</script>";
        }
    }
}


?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-3">
                <h3 class="text-center">Edit Art</h3>


                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Art Name</label>
                        <input type="text" name="art_name" class="form-control" required
                            value="<?= htmlspecialchars($art['art_name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" class="form-control" required
                            value="<?= $art['price'] ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Art Image</label>
                        <input type="file" name="art_image" class="form-control">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn">Update Art</button>
                        <a href="art_list.php" class="btn ms-2">Cancel</a>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>