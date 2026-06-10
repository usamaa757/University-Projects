<?php

use Stripe\Terminal\Location;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $plant_id = $_POST['plant_id'];
    $user_id = $_POST['user_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    // Handle file upload for photos
    $photo_url = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $file_ext;

        // Move uploaded photo to the destination folder
        if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            $photo_url = $upload_dir . $new_file_name;
        }
    }

    // Insert review data into the database
    include '../db_connection.php';
    $query = "INSERT INTO plant_reviews (plant_id, user_id, rating, review_text, photo_url) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiiss', $plant_id, $user_id, $rating, $review_text, $photo_url);

    if ($stmt->execute()) {
        $success_message = "Review submitted successfully!";
    } else {
        $error_message = "Error submitting review.";
    }
    header('location: feedback.php?plant_id=' . $plant_id);
}
