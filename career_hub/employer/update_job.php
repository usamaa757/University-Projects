<?php
include '../db_connect.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'];
    $job_title = $_POST['job_title'];
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $salary_range = $_POST['salary_range'];
    $application_deadline = $_POST['application_deadline'];

    // Update job in database
    $sql = "UPDATE jobs SET job_title = ?, company_name = ?, location = ?, job_type = ?, salary_range = ?, application_deadline = ? WHERE job_id = ? AND employer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $job_title, $company_name, $location, $job_type, $salary_range, $application_deadline, $job_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo "<script>alert('Job updated successfully!'); window.location.href='job_list.php';</script>";
    } else {
        echo "<script>alert('Error updating job!'); window.location.href='edit_job.php?job_id=$job_id';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request!'); window.location.href='job_list.php';</script>";
}