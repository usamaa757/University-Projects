<?php
include '../other/db_connection.php';  // Include the database connection file

// Check if the required parameters (id and status) are set in the URL query string
if (isset($_GET['id'], $_GET['status'])) {
    // Retrieve and sanitize the inputs using real_escape_string to avoid SQL injection
    $id = $conn->real_escape_string($_GET['id']);
    $status = $conn->real_escape_string($_GET['status']);

    // Validate that the status is one of the allowed values
    if (in_array($status, ['approved', 'rejected'])) {
        // Construct the SQL update query
        $query = "UPDATE student_registeration SET status = '$status' WHERE id = $id";

        // Execute the query and check for success
        if ($conn->query($query) === TRUE) {
            echo "Record updated successfully.";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Invalid status value.";
    }
} else {
    echo "Required parameters not provided.";
}

// Close the database connection
$conn->close();

// Redirect back to the listing page
header("Location: ../admin/list_teachers.php");
exit;
?>
