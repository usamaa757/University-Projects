<?php
include 'header.php';
$uploadDir = 'image_library/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true); // create directory if it doesn't exist
}


if (isset($_FILES['images'])) {



    $files = $_FILES['images'];

    foreach ($files['tmp_name'] as $index => $tmpName) {
        if ($files['error'][$index] === UPLOAD_ERR_OK) {
            $filename = basename($files['name'][$index]);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $targetPath)) {
                echo "Uploaded: $filename<br>";
            } else {
                echo "Failed to upload: $filename<br>";
            }
        } else {
            echo "Error with file: " . $files['name'][$index] . "<br>";
        }
    }
}

?>



<!-- Upload Form -->
<div class="container">
    <div class="form-section">
        <h1>Upload Images to Your Library</h1>
        <form method="POST" enctype="multipart/form-data">
            <label>Upload Images:</label>
            <input type="file" name="images[]" multiple><br><br>


            <div class="btn">
                <button type="submit">UPload</button>

            </div>
        </form>

    </div>
</div>