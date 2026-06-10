<?php

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
if ($result_booking_ids->num_rows > 0) {
    while ($row = $result_booking_ids->fetch_assoc()) {
        $booking_ids[] = $row['booking_id'];
    }
}

$stmt_booking_ids->close();

// Initialize array to hold booking details
$bookings = [];

// Fetch driver details, and patient details based on booking details
if (!empty($booking_ids)) {
    $placeholders_booking = implode(',', array_fill(0, count($booking_ids), '?'));
    $sql_details = "
        SELECT aua.booking_id, a.plate_number AS ambulance_plate_number, d.name AS driver_name, aua.assigned_at, aua.status, p.patient_name, p.patient_age, p.patient_gender
        FROM ambulance_user_assignment aua
        JOIN ambulances a ON aua.ambulance_id = a.ambulance_id
        LEFT JOIN ambulance_driver_assignment ada ON a.ambulance_id = ada.ambulance_id
        LEFT JOIN drivers d ON ada.driver_id = d.driver_id
        LEFT JOIN bookings b ON aua.booking_id = b.booking_id
        LEFT JOIN patients p ON b.patient_id = p.patient_id
        WHERE aua.booking_id IN ($placeholders_booking)
    ";

    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bind_param(str_repeat('i', count($booking_ids)), ...$booking_ids);
    $stmt_details->execute();
    $result_details = $stmt_details->get_result();

    if ($result_details->num_rows > 0) {
        while ($row = $result_details->fetch_assoc()) {
            $bookings[] = $row;
        }
    }

    $stmt_details->close();
}

$conn->close();
?>

<div class="container-fluid">
    <a href="user_dashboard.php" class="btn btn-secondary mt-3 mb-3">Go to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Your Booking History</h4>
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
                                    <th>Assigned At</th>
                                    <th>Status</th>
                                    <th>Patient Name</th>
                                    <th>Patient Age</th>
                                    <th>Patient Gender</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['ambulance_plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['driver_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['assigned_at']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['patient_age']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['patient_gender']); ?></td>
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>