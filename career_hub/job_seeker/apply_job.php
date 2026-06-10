<?php
session_start();
include "../db_connect.php"; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role']) == 'job_seeker') {
    echo "<script>alert('Please log in to apply for jobs.'); window.location='login.php';</script>";
    exit();
}

// Check if job_id is passed
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    echo "<script>alert('Invalid job selection.'); window.location='jobs.php';</script>";
    exit();
}

$job_id = intval($_GET['job_id']);
$job_seeker_id = $_SESSION['user_id'];

// Check if already applied
$check = $conn->prepare("SELECT * FROM job_applications WHERE job_id = ? AND job_seeker_id = ?");
$check->bind_param("ii", $job_id, $job_seeker_id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('You have already applied for this job.'); window.location='search_jobs.php';</script>";
    exit();
}

// Insert application with default status 'Pending'
$stmt = $conn->prepare("INSERT INTO job_applications (job_id, job_seeker_id, status) VALUES (?, ?, 'Pending')");
$stmt->bind_param("ii", $job_id, $job_seeker_id);

if ($stmt->execute()) {
    echo "<script>alert('Application submitted successfully!'); window.location='search_jobs.php';</script>";
} else {
    echo "<script>alert('Error applying for the job. Please try again.');</script>";
}