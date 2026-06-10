<?php
include '../other/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $student_id = $_POST['student_id'] ?? null;
    $assignment_id = $_POST['assignment_id'] ?? null;
    $course_id = $_POST['course_id'] ?? null;
    
    // Initialize message variable
    $message = '';
    
    // Check if file was uploaded
    if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['uploaded_file']['name']);
        
        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $upload_file)) {
            // Save assignment details in the database
            $stmt = $conn->prepare("INSERT INTO student_assignments (assignment_id, student_id, course_id, assignment_file) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $assignment_id, $student_id, $course_id, $upload_file);
            if ($stmt->execute()) {
                $message = "Assignment uploaded successfully.";
            } else {
                $message = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "No file uploaded or there was an upload error.";
    }
    
    $conn->close();
    
    // Redirect with message
    $message = urlencode($message);
    header("Location: view_assignments.php?msg=$message");
    exit;
}
?>
