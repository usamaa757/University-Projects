<?php
include('../db_connection.php');

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $user_id = $_SESSION['user_id'];
    $patient_name = $_POST['patient_name'];
    $patient_age = $_POST['patient_age'];
    $patient_gender = $_POST['patient_gender'];
    $disease_id = $_POST['disease_id'];
    $patient_status = $_POST['patient_status'];
    $destination = $_POST['destination'];
    $pickup_point = $_POST['pickup_point'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Insert patient details into the patients table
    $sql_patient = "INSERT INTO patients (disease_id, patient_status, patient_name, patient_age, patient_gender) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmt_patient = $conn->prepare($sql_patient);
    $stmt_patient->bind_param('issss', $disease_id, $patient_status, $patient_name, $patient_age, $patient_gender);

    if ($stmt_patient->execute()) {
        $patient_id = $stmt_patient->insert_id; // Get the last inserted patient_id
    } else {
        $_SESSION['error'] = "Failed to add patient details.";
        $stmt_patient->close();
        $conn->close();
        header("Location: book_ambulance.php"); // Adjust the redirect as needed
        exit();
    }
    $stmt_patient->close();

    // Insert booking data into the bookings table
    $sql_booking = "INSERT INTO bookings (user_id, patient_id, hosp_id, pickup_point, booking_date, booking_time)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_booking = $conn->prepare($sql_booking);
    $stmt_booking->bind_param('iissss', $user_id, $patient_id, $destination, $pickup_point, $date, $time);

    if ($stmt_booking->execute()) {
        $_SESSION['success'] = "Ambulance booked successfully.";
    } else {
        $_SESSION['error'] = "Failed to book ambulance.";
    }
    $stmt_booking->close();
    $conn->close();

    header("Location: book_ambulance.php"); // Adjust the redirect as needed
    exit();
}
