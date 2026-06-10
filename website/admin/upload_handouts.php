<?php
include '../db_connection.php';
include 'header.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Specify the directory where files will be uploaded
    $uploadDir = 'handouts/'; // Ensure this directory exists and is writable

    // Create the uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Initialize an array to hold uploaded file names
    $uploadedFiles = [];
    $uploadSuccess = true; // Flag to track overall success

    // Loop through each uploaded file
    foreach ($_FILES['files']['name'] as $key => $fileName) {
        // Check for any upload errors
        if ($_FILES['files']['error'][$key] == UPLOAD_ERR_OK) {
            // Move the file to the upload directory
            $tmpName = $_FILES['files']['tmp_name'][$key];
            $filePath = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmpName, $filePath)) {
                $uploadedFiles[] = $fileName; // Add the file name to the array

                // Insert file name into the database
                $stmt = $conn->prepare("INSERT INTO handouts (name) VALUES (?)");
                $stmt->bind_param("s", $fileName); // Use file name only
                if (!$stmt->execute()) {
                    // Log the error if insertion fails
                    $_SESSION['error'] = "<div class='alert alert-danger'>Error saving file name to database: " . $stmt->error . "</div>";
                    $uploadSuccess = false; // Set flag to false if any upload fails
                }
                $stmt->close(); // Close the statement
            } else {
                $_SESSION['error'] = "<div class='alert alert-danger'>Error uploading file: $fileName</div>";
                $uploadSuccess = false; // Set flag to false if any upload fails
            }
        } else {
            $_SESSION['error'] = "<div class='alert alert-danger'>Error with file " . $_FILES['files']['error'][$key] . "</div>";
            $uploadSuccess = false; // Set flag to false if any upload fails
        }
    }

    // Only set success message if all uploads and insertions were successful
    if ($uploadSuccess) {
        $_SESSION['success'] = "<div class='alert alert-success'>Files uploaded and saved successfully</div>";
    } else {
        $_SESSION['error'] = "<div class='alert alert-danger'>Some files were not processed successfully</div>";
    }
}

?>
<br><br><br><br><br>
<div class="container border p-0 shadow rounded col-md-4">
    <div class="bg-primary p-2">
        <h3 class="text-center text-white">Upload Handouts</h3>
    </div>

    <div class="form p-3">
        <form action="upload_handouts.php" method="post" enctype="multipart/form-data">
            <?php
            // Display success or error messages
            if (isset($_SESSION['success'])) {
                echo $_SESSION['success'];
                unset($_SESSION['success']); // Clear the success message after displaying
            }

            if (isset($_SESSION['error'])) {
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Clear the error message after displaying
            }
            ?>
            <div class="form-group">
                <label for="fileUpload">Select files to upload:</label>
                <input type="file" class="form-control-file" name="files[]" id="fileUpload" multiple required>
            </div>
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>