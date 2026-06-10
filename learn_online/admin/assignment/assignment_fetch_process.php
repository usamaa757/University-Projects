<?php

session_start();
include '../common_process/db_connection.php';



// Prepare the SQL statement with a placeholder for the email parameter
$sql = "SELECT * FROM teacher_subjects WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);

// Bind the email parameter to the prepared statement
$id = $_SESSION['teacher_id']; 
$stmt->bind_param("s", $id);

// Execute the prepared statement
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Array to store subjects
$subjects = array();
$subject_id = array();

// Loop through each row to fetch subject names
while ($row = $result->fetch_assoc()) {
    // Assuming 'subject_name' is the column name in your database table
    $subjects[] = $row['subject_name'];
    $subject_id[] = $row['SNo'];

}

// Close the statement and connection
$stmt->close();

$conn->close();

?>
