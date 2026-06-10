<?php
include '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $student_name = $_POST['name'];

    $query = "UPDATE students SET name = ? WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $student_name, $student_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: students.php?message=Subject updated successfully");
    } else {
        echo "Error updating subject: " . mysqli_error($conn);
    }
}
?>