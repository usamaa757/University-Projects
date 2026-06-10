<?php
include '../db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $service_id = $_POST['service_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $special_requests = $_POST['special_requests'] ?? '';

    // Insert booking into the database
    $query = "INSERT INTO bookings (customer_id, service_id, booking_date, booking_time, special_requests) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iisss", $customer_id, $service_id, $booking_date, $booking_time, $special_requests);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        echo "<script>alert('Your booking has been confirmed!'); window.location.href='booking_confirmation.php';</script>";
    } else {
        echo "<script>alert('Booking failed. Please try again.'); window.location.href='book_service.php?service_id=$service_id';</script>";
    }
}