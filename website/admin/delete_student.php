<?php
include '../db_connection.php';

// Check if the `id` parameter is set in the URL
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare the DELETE statement
    $query = "DELETE FROM students WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind the `student_id` to the prepared statement
        mysqli_stmt_bind_param($stmt, 'i', $student_id);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the Student management page with a success message
            header("Location: students.php?message=Student deleted successfully");
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
    echo "Error: Student ID not specified.";
}

// Close the database connection
mysqli_close($conn);
?>