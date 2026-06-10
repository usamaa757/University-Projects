<?php
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];

    $query = "UPDATE subjects SET subject_name = ? WHERE subject_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $subject_name, $subject_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: subjects.php?message=Subject updated successfully");
    } else {
        echo "Error updating subject: " . mysqli_error($conn);
    }
}
?>