<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch booking details with assigned ambulance and driver information
$sql = "SELECT 
            b.booking_id, 
            b.user_id, 
            b.pickup_point, 
            b.booking_date, 
            b.booking_time, 
            p.patient_name, 
            p.patient_age, 
            p.patient_gender,
            p.patient_status,
            h.name AS hospital_name, 
            d.name AS disease_name,
            aua.ambulance_id,
            a.plate_number,
            drd.driver_id,
            dr.name AS driver_name, 
            drd.assigned_at AS driver_assigned_at
        FROM bookings b
        JOIN hospitals h ON b.hosp_id = h.hosp_id
        JOIN patients p ON b.patient_id = p.patient_id
        JOIN diseases d ON p.disease_id = d.disease_id
        LEFT JOIN ambulance_user_assignment aua ON b.booking_id = aua.booking_id
        LEFT JOIN ambulances a ON aua.ambulance_id = a.ambulance_id
        LEFT JOIN detailed_driver_record drd ON aua.ambulance_id = drd.ambulance_id
        LEFT JOIN drivers dr ON drd.driver_id = dr.driver_id
        WHERE aua.ambulance_id IS NOT NULL
        ORDER BY b.booking_id DESC";

$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
}

$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$conn->close();
?>

<div class="container-fluid">
<a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card w-100">
                <div class="card-header">
                    <h4 class="card-title">Assigned Ambulance Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Pick-up Point</th>
                                    <th>Hospital</th>
                                    <th>Booking Date</th>
                                    <th>Booking Time</th>
                                    <th>Disease</th>
                                    <th>Patient Status</th>
                                    <th>Plate Number</th>
                                    <th>Driver ID</th>
                                    <th>Driver Name</th>
                                    <th>Driver Assigned At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)) : ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No bookings available.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($bookings as $booking) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($booking['pickup_point']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['hospital_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['disease_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['patient_status']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['plate_number']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['driver_id']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['driver_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['driver_assigned_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
