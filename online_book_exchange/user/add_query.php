<?php
include 'header.php';
include '../db_connection.php';

// Initialize variables for form submission status
$msg = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    $uploaded_file = "";
    $user_id = $_SESSION['user_id'];

    // Handle file upload if a file is submitted
    if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != "") {
        $target_dir = "uploads/issues/";
        $uploaded_file = $target_dir . basename($_FILES['file']['name']);
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
            $error = "Error uploading file.";
        }
    }

    if (!$error) {
        // Insert the issue report into the database
        $query = "INSERT INTO issue_reports (user_id, issue_type, description, uploaded_file) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $issue_type, $description, $uploaded_file);

        if ($stmt->execute()) {
            $msg = "Issue reported successfully.";
        } else {
            $error = "Error reporting the issue. Please try again.";
        }
    }
}
?>

<div class="container mt-5 border shadow round p-0">
    <div class=" text-center bg-dark text-white">
        <h3 class="mb-4 p-2">Reported Issues</h3>
    </div>
    <div class="p-3">


        <!-- Display success or error messages -->
        <?php if ($msg): ?>
        <div class="text-success"><?php echo $msg; ?></div>
        <?php elseif ($error): ?>
        <div class="text-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Issue Reporting Form -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="issueType">Issue Type</label>
                <select class="form-control" id="issueType" name="issue_type" required>
                    <option value="Book Listing Issue">Book Listing Issue</option>
                    <option value="Exchange Request Issue">Exchange Request Issue</option>
                    <option value="Profile Issue">Profile Issue</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="file">Upload File (optional)</label>
                <input type="file" class="form-control-file" id="file" name="file">
            </div>
            <div class="text-center">

                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

</body>

</html>