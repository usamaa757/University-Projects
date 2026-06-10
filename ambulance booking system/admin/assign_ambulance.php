<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('../db_connection.php');

// Check if form is submitted
if (isset($_POST['assign'])) {
    $admin_id = $_SESSION['admin_id'];
    $success = true; // Flag to check if everything went well

    foreach ($_POST['ambulance'] as $booking_id => $ambulance_id) {
        if (!empty($ambulance_id)) {
            $status = 'Busy';

            // Insert assignment into ambulance_user_assignment table
            $sql = "INSERT INTO ambulance_user_assignment (booking_id, ambulance_id, assigned_by, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $success = false;
                break;
            }
            $stmt->bind_param('iiis', $booking_id, $ambulance_id, $admin_id, $status);
            if (!$stmt->execute()) {
                $success = false;
                break;
            }

            // Update the status of the ambulance in ambulance_hospital_assignment table to 'Busy'
            $sql_update = "UPDATE ambulance_hospital_assignment SET status = 'Busy' WHERE ambulance_id = ? AND hosp_id = (SELECT hosp_id FROM bookings WHERE booking_id = ?)";
            $stmt_update = $conn->prepare($sql_update);
            if (!$stmt_update) {
                $success = false;
                break;
            }
            $stmt_update->bind_param('ii', $ambulance_id, $booking_id);
            if (!$stmt_update->execute()) {
                $success = false;
                break;
            }

            // Fetch the driver_id for the given ambulance_id
            $sql_driver = "SELECT driver_id FROM ambulance_driver_assignment WHERE ambulance_id = ?";
            $stmt_driver = $conn->prepare($sql_driver);
            if (!$stmt_driver) {
                $success = false;
                break;
            }
            $stmt_driver->bind_param('i', $ambulance_id);
            if (!$stmt_driver->execute()) {
                $success = false;
                break;
            }
            $driver_result = $stmt_driver->get_result();
            $driver_id = null;
            if ($driver_result->num_rows > 0) {
                $driver_row = $driver_result->fetch_assoc();
                $driver_id = $driver_row['driver_id'];
            }

            $stmt_driver->close();

            // Insert assignment into detailed_driver_record table
            if ($driver_id !== null) {
                $sql_detailed_driver = "INSERT INTO detailed_driver_record (booking_id, ambulance_id, driver_id) VALUES (?, ?, ?)";
                $stmt_detailed_driver = $conn->prepare($sql_detailed_driver);
                if (!$stmt_detailed_driver) {
                    $success = false;
                    break;
                }
                $stmt_detailed_driver->bind_param('iii', $booking_id, $ambulance_id, $driver_id);
                if (!$stmt_detailed_driver->execute()) {
                    $success = false;
                    break;
                }
                $stmt_detailed_driver->close();
            }
        }
    }
    $stmt->close();
    if ($success) {
        $_SESSION['message'] = "Ambulance assigned successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to assign ambulance.";
        $_SESSION['message_type'] = "danger";
    }
}
$conn->close();

// Redirect back to the booking details page
header("Location: manage_booking.php");
exit();
