<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch booking details with related hospital and disease information where no ambulance is assigned
$sql = "SELECT b.booking_id, b.user_id, b.pickup_point, b.hosp_id, b.booking_date, b.booking_time,
               d.name as disease_name, h.name as hospital_name,
               p.patient_id, p.patient_name, p.patient_age, p.patient_gender, p.patient_status
        FROM bookings b 
        JOIN hospitals h ON b.hosp_id = h.hosp_id 
        JOIN patients p ON b.patient_id = p.patient_id
        JOIN diseases d ON p.disease_id = d.disease_id 
        LEFT JOIN ambulance_user_assignment aua ON b.booking_id = aua.booking_id 
        WHERE aua.ambulance_id IS NULL 
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);


$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Fetch ambulances for each hospital, excluding busy ambulances
$ambulances = [];
foreach ($bookings as $booking) {
    $hospital_id = $booking['hosp_id'];
    $sql_ambulance = "
        SELECT a.ambulance_id, a.plate_number, aha.status 
        FROM ambulances a 
        JOIN ambulance_hospital_assignment aha ON a.ambulance_id = aha.ambulance_id
        LEFT JOIN ambulance_user_assignment aua ON a.ambulance_id = aua.ambulance_id
        WHERE aha.hosp_id = ? AND (aha.status = 'Available')
    ";
    $stmt = $conn->prepare($sql_ambulance);
    $stmt->bind_param("i", $hospital_id);
    $stmt->execute();
    $result_ambulance = $stmt->get_result();

    $ambulances[$hospital_id] = [];
    if ($result_ambulance->num_rows > 0) {
        while ($row_ambulance = $result_ambulance->fetch_assoc()) {
            $ambulances[$hospital_id][] = $row_ambulance;
        }
    }
    $stmt->close();
}

$conn->close();
?>

<div class="container-fluid mt-5">
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Booking Details</h4>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($_SESSION['message'])) {
                        $message = $_SESSION['message'];
                        $message_type = $_SESSION['message_type'];
                        echo "<div class='alert alert-{$message_type}' role='alert'>{$message}</div>";
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    }
                    ?>
                    <form action="assign_ambulance.php" method="post">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User ID</th>
                                    <th>Pick-up Point</th>
                                    <th>Hospital</th>
                                    <th>Booking Date</th>
                                    <th>Booking Time</th>
                                    <th>Disease</th>
                                    <th>Patient Status</th>
                                    <th>Patient Name</th>
                                    <th>Patient Age</th>
                                    <th>Patient Gender</th>
                                    <th>Available Ambulances</th>
                                    <th>Assign Ambulance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookings)) : ?>
                                    <tr>
                                        <td colspan="13" class="text-center">No bookings available.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($bookings as $booking) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['user_id']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['pickup_point']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['hospital_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['disease_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['patient_status']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['patient_age']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['patient_gender']); ?></td>
                                            <td>
                                                <?php
                                                if (isset($ambulances[$booking['hosp_id']])) {
                                                    echo '<select name="ambulance[' . $booking['booking_id'] . ']" class="form-control">';
                                                    echo '<option value="">Select Ambulance</option>';
                                                    foreach ($ambulances[$booking['hosp_id']] as $ambulance) {
                                                        echo '<option value="' . htmlspecialchars($ambulance['ambulance_id']) . '">' . htmlspecialchars($ambulance['plate_number']) . '</option>';
                                                    }
                                                    echo '</select>';
                                                } else {
                                                    echo 'No ambulances available.';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button type="submit" name="assign" value="<?php echo htmlspecialchars($booking['booking_id']); ?>" class="btn btn-primary btn-sm">Assign</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
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
