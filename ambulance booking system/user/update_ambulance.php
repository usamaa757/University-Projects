<?php
session_start();
include("../db_connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $ambulance_id = isset($_POST['ambulance_id']) ? intval($_POST['ambulance_id']) : 0;
    $plate_number = $_POST['plate_number'];
    $model = $_POST['model'];
    $capacity = $_POST['capacity'];
    $location = $_POST['location'];

    if ($ambulance_id <= 0) {
        $_SESSION['error'] = "Invalid ambulance ID.";
        header("Location: edit_ambulance.php?ambulance_id=" . $ambulance_id);
        exit();
    }

    // Update ambulance details
    $sql = "UPDATE ambulances SET plate_number = ?, model = ?, capacity = ?, location = ? WHERE ambulance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $plate_number, $model, $capacity, $location, $ambulance_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Ambulance details updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update ambulance details.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the edit page
    header("Location: edit_ambulance.php?ambulance_id=" . $ambulance_id);
    exit();
}
