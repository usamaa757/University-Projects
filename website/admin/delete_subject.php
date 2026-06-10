<?php
include '../db_connection.php';

// Check if the `id` parameter is set in the URL
if (isset($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Prepare the DELETE statement
    $query = "DELETE FROM subjects WHERE subject_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind the `subject_id` to the prepared statement
        mysqli_stmt_bind_param($stmt, 'i', $subject_id);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the subject management page with a success message
            header("Location: subjecs.php?message=Subject deleted successfully");
            exit();
        } else {
            echo "Error: Could not execute the delete statement. " . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: Could not prepare the delete statement. " . mysqli_error($conn);
    }
} else {
    echo "Error: Subject ID not specified.";
}

// Close the database connection
mysqli_close($conn);
?>