<?php
include '../../db_connection.php';

$errorMsg = "";
$resultMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if a new video file is uploaded
    if ($_FILES['lesson_file']['error'] === UPLOAD_ERR_OK) {
        $new_file_name = $_FILES['lesson_file']['name'];
        $new_file_type = $_FILES['lesson_file']['type'];
        $new_file_size = $_FILES['lesson_file']['size'];
        $new_file_path = 'videos/' . $new_file_name;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['lesson_file']['tmp_name'], $new_file_path)) {
            // Update lesson details in the database
            $stmt = $conn->prepare("UPDATE lessons SET file_name = ?, file_type = ?, file_size = ?, start_date = ?, end_date = ? WHERE lesson_id = ?");
            $stmt->bind_param("sssssi", $new_file_name, $new_file_type, $new_file_size, $start_date, $end_date, $lesson_id);
            if ($stmt->execute()) {
                $resultMsg = "Lesson details updated successfully.";
                
                header("Location: edit_lesson.php?result=" . urlencode($resultMsg));

                $file_name = $new_file_name;
            } else {
                $errorMsg = "Failed to update lesson details.";
            header("Location: edit_lesson.php?error=" . urlencode($errorMsg));

            }
            $stmt->close();
        } else {
            $errorMsg = "Failed to move uploaded file.";
        }
    } else {
        // Update lesson details in the database without changing the file
        $stmt = $conn->prepare("UPDATE lessons SET start_date = ?, end_date = ? WHERE lesson_id = ?");
        $stmt->bind_param("ssi", $start_date, $end_date, $lesson_id);
        if ($stmt->execute()) {
            $resultMsg = "Lesson details updated successfully.";
            header("Location: edit_lesson.php?result=" . urlencode($resultMsg));

        } else {
            $errorMsg = "Failed to update lesson details.";
            header("Location: edit_lesson.php?error=" . urlencode($errorMsg));

        }
        $stmt->close();
    }
}