<?php
include '../db.php';

$car_id = $_GET['car_id'];
if ($conn->query("DELETE FROM cars WHERE car_id = $car_id")) {

    echo "<script>alert('Car deleted successfully!');window.location.href='car_list.php';</script>";

    exit;
} else {
    echo "<script>alert('Error deleting car: " . $conn->error . "');
    window.location.href='car_list.php';
    </script>";
    exit;
}
