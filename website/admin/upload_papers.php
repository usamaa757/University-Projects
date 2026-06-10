<?php
include '../db_connection.php'; // Ensure this file has the database connection logic
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $upload_type = $_POST['upload_type'] ?? '';
    $upload_dir = ($upload_type === 'mid') ? 'mid/' : 'final/';

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $uploadSuccess = true;
    $files = $_FILES[$upload_type . '_files']; // Access the files array dynamically based on upload type

    foreach ($files['name'] as $key => $fileName) {
        if ($files['error'][$key] == UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$key];
            $filePath = $upload_dir . basename($fileName);

            // Check if file with the same name exists in the directory
            if (file_exists($filePath)) {
                unlink($filePath); // Remove the existing file
            }

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($tmpName, $filePath)) {
                // Check if file already exists in the database by name
                $table = ($upload_type === 'mid') ? 'mid_term_papers' : 'final_term_papers';

                $stmt = $conn->prepare("SELECT id FROM $table WHERE name = ?");
                $stmt->bind_param("s", $filePath);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // If file already exists, update its record
                    $stmt->close();
                    $stmt = $conn->prepare("UPDATE $table SET name = ? WHERE name = ?");
                    $stmt->bind_param("ss", $filePath, $filePath);
                } else {
                    // Insert a new record if the file does not already exist
                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO $table (name) VALUES (?)");
                    $stmt->bind_param("s", $filePath);
                }

                if (!$stmt->execute()) {
                    $_SESSION[$upload_type . '_error'] = "<div class='alert alert-danger'>Error updating database: " . $stmt->error . "</div>";
                    $uploadSuccess = false;
                }

                $stmt->close(); // Close statement
            } else {
                $_SESSION[$upload_type . '_error'] = "<div class='alert alert-danger'>Error uploading file: $fileName</div>";
                $uploadSuccess = false;
            }
        } else {
            $_SESSION[$upload_type . '_error'] = "<div class='alert alert-danger'>Error with file upload: " . $files['error'][$key] . "</div>";
            $uploadSuccess = false;
        }
    }

    if ($uploadSuccess) {
        $_SESSION[$upload_type . '_success'] = "<div class='alert alert-success'>Files uploaded and database updated successfully</div>";
    }
}
?>

<br><br><br><br><br>
<!-- HTML Form Section -->
<div class="container  border p-0 shadow rounded col-md-4">
    <div class="bg-primary p-2">
        <h3 class="text-center text-white">Upload Mid Paper</h3>
    </div>

    <div class="form p-3">
        <?php
        if (isset($_SESSION['mid_success'])) {
            echo $_SESSION['mid_success'];
            unset($_SESSION['mid_success']);
        }

        if (isset($_SESSION['mid_error'])) {
            echo $_SESSION['mid_error'];
            unset($_SESSION['mid_error']);
        }
        ?>
        <form action="upload_papers.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="upload_type" value="mid"> <!-- Hidden field for mid upload type -->
            <div class="form-group">
                <label for="midFileUpload">Select files to upload:</label>
                <input type="file" class="form-control-file" name="mid_files[]" id="midFileUpload" multiple required>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<div class="container mt-4 border p-0 shadow rounded col-md-4">
    <div class="bg-primary p-2">
        <h3 class="text-center text-white">Upload Final Paper</h3>
    </div>

    <div class="form p-3">
        <?php
        if (isset($_SESSION['final_success'])) {
            echo $_SESSION['final_success'];
            unset($_SESSION['final_success']);
        }

        if (isset($_SESSION['final_error'])) {
            echo $_SESSION['final_error'];
            unset($_SESSION['final_error']);
        }
        ?>
        <form action="upload_papers.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="upload_type" value="final"> <!-- Hidden field for final upload type -->
            <div class="form-group">
                <label for="finalFileUpload">Select files to upload:</label>
                <input type="file" class="form-control-file" name="final_files[]" id="finalFileUpload" multiple
                    required>
            </div>

            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
</body>

</html>