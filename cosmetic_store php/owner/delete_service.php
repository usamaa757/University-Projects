<?php

include '../db.php';

if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];
    $stmt = $conn->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);


    if ($stmt->execute()) {
        echo "<script>alert('Service deleted successfully!'); window.location.href='services.php';</script>";
    } else {
        echo "<script>alert('Error deleting service'); window.location.href='services.php';</script>";
    }
} else {
    echo "<script>alert('Invalid service ID'); window.location.href='services.php';</script>";
}
