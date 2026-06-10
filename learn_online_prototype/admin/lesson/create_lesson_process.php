<?php

include '../../db_connection.php';
$resultMsg = "";
$errorMsg = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    if (isset($_POST['title'], $_FILES['lesson_file'], $_POST['start_date'], $_POST['end_date'])) {
      
        $title = $_POST['title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Assuming you're using session to store teacher ID

        $lesson_file = $_FILES['lesson_file'];
        $file_name = $lesson_file['name'];
        $file_tmp_name = $lesson_file['tmp_name'];
        $file_type = $lesson_file['type'];
        $file_size = $lesson_file['size'];

        // Explode file name to get extension
        $file_parts = explode('.', $file_name);
        $file_ext = strtolower(end($file_parts));

        // Error checking (optional, add more checks as needed)
        $extensions = array("mp4", "docx", "doc", "pdf");
        if (!in_array($file_ext, $extensions)) {
            $errorMsg = "Invalid file extension.";
            exit;
        }

        // Prepare SQL statement for inserting data
        $sql = "INSERT INTO lessons (title, file_name, file_size, file_type, file_ext, start_date, end_date)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Prepare statement and bind variables
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $errorMsg = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("sssssss", $title, $file_name, $file_size, $file_type, $file_ext, $start_date, $end_date);

            // Execute statement
            if ($stmt->execute()) {
                // Upload the file (optional, customize the upload path)
                $upload_dir = "videos/";
                $upload_file = $upload_dir . basename($file_name);
                if (move_uploaded_file($file_tmp_name, $upload_file)) {
                    // Success message with uploaded filename
                    $resultMsg = "Lesson uploaded successfully: " . $file_name;
                } else {
                    $errorMsg = "Error uploading file.";
                }
            } else {
                $errorMsg = "Error inserting Lesson data: " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        }

        // Close connection
        $conn->close();
    } else {
        $errorMsg = "Missing required fields.";
    }
}
?>
