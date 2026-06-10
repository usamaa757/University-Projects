<?php
session_start();
include "../db.php";

// Check if user is logged in and is a seeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seeker') {
    echo "<script>alert('Please log in to apply for jobs.'); window.location='login.php';</script>";
    exit();
}

$seeker_id = $_SESSION['user_id'];

// Check if job_id is passed
if (!isset($_POST['job_id']) || empty($_POST['job_id'])) {
    echo "<script>alert('Invalid job selection.'); window.location='search_jobs.php';</script>";
    exit();
}

$job_id = $_POST['job_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {


    $cv = $_FILES['cv'];
    $cv_name = basename($cv['name']);
    $upload_dir = 'uploads/cvs/';
    $unique_name = uniqid() . '_' . $cv_name;
    $target_file = $upload_dir . $unique_name;

    // Create folder if needed
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($cv['tmp_name'], $target_file)) {
        // Save application
        $escaped_cv_path =  mysqli_escape_string($conn, $target_file);
        $insertQuery = "INSERT INTO job_applications (job_id, seeker_id, cv_file) 
                        VALUES ($job_id, $seeker_id, '$escaped_cv_path')";

        if (mysqli_query($conn, $insertQuery)) {
            echo "<script>alert('Application submitted successfully.'); window.location.href='search_jobs.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error submitting application.');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload CV.');</script>";
    }
}