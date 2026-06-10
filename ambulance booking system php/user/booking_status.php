<?php

// Check if the user is logged in, if not redirect to login page


include('header.php');
include('../db_connection.php');

// Fetch booking IDs for the logged-in user
$user_id = $_SESSION['user_id'];
$sql_booking_ids = "
    SELECT aua.booking_id, aua.ambulance_id
    FROM ambulance_user_assignment aua
    JOIN bookings b ON b.booking_id = aua.booking_id
    WHERE b.user_id = ?
";
$stmt_booking_ids = $conn->prepare($sql_booking_ids);
$stmt_booking_ids->bind_param("i", $user_id);
$stmt_booking_ids->execute();
$result_booking_ids = $stmt_booking_ids->get_result();

$booking_ids = [];
$ambulance_ids = [];
if ($result_booking_ids->num_rows > 0) {
    while ($row = $result_booking_ids->fetch_assoc()) {
        $booking_ids[] = $row['booking_id'];
        $ambulance_ids[] = $row['ambulance_id'];
    }
}

$stmt_booking_ids->close();

// Initialize array to hold booking details
$bookings = [];

if (!empty($ambulance_ids)) {
    // Prepare placeholders for the booking IDs
    $placeholders_booking = implode(',', array_fill(0, count($booking_ids), '?'));

    $sql_details = "
        SELECT aua.booking_id, 
               a.plate_number AS ambulance_plate_number, 
               d.name AS driver_name, 
               a.ambulance_id, 
               p.patient_status
        FROM ambulance_user_assignment aua
        JOIN ambulances a ON aua.ambulance_id = a.ambulance_id
        JOIN bookings b ON aua.booking_id = b.booking_id
        LEFT JOIN patients p ON b.patient_id = p.patient_id
        LEFT JOIN ambulance_driver_assignment ada ON a.ambulance_id = ada.ambulance_id
        LEFT JOIN drivers d ON ada.driver_id = d.driver_id
        WHERE aua.booking_id IN ($placeholders_booking) AND aua.status = 'Busy'
    ";

    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param(str_repeat('i', count($booking_ids)), ...$booking_ids);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();

    $bookings = [];
    if ($result_details->num_rows > 0) {
        while ($row = $result_details->fetch_assoc()) {
            $bookings[] = $row;
        }
    }

    $stmt_details->close();
}


$conn->close();
?>

<div class="container">
    <a href="user_dashboard.php" class="btn btn-secondary mt-3 mb-3">Go to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Your Booking Status</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success'];
                            unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error'];
                            unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($bookings)) : ?>
                        <p class="text-center">No bookings found.</p>
                    <?php else : ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Ambulance Plate Number</th>
                                    <th>Driver Name</th>
                                    <th>Patient Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['ambulance_plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['driver_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['patient_status']); ?></td>
                                        <td>
                                            <a href="edit_ambulance.php?ambulance_id=<?php echo urlencode($booking['ambulance_id']); ?>"
                                                class="btn btn-warning btn-sm">Edit Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>