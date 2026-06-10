<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Check if form is submitted to update status
if (isset($_POST['set_available'])) {
    $ambulance_id = $_POST['ambulance_id'];

    // Update the status in ambulance_hospital_assignment and ambulance_user_assignment tables
    $sql = "UPDATE ambulance_hospital_assignment 
            SET status = 'Available' 
            WHERE ambulance_id = ? AND status = 'Busy'";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('i', $ambulance_id);
        if ($stmt->execute()) {
            $stmt->close();

            $sql = "UPDATE ambulance_user_assignment 
                    SET status = 'Completed' 
                    WHERE ambulance_id = ? AND status = 'Busy'";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $ambulance_id);
                if ($stmt->execute()) {
                    $_SESSION['message'] = "Ambulance status updated successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error updating ambulance_user_assignment: " . $stmt->error;
                    $_SESSION['message_type'] = "danger";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Error preparing ambulance_user_assignment statement: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Error updating ambulance_hospital_assignment: " . $stmt->error;
            $_SESSION['message_type'] = "danger";
            $stmt->close();
        }
    } else {
        $_SESSION['message'] = "Error preparing ambulance_hospital_assignment statement: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }

    // Redirect back to the busy ambulances page
    header("Location: booking_status.php");
    exit();
}

// Fetch details of ambulances that are busy with pickups based on ambulance_hospital_assignment
$sql = "SELECT aua.booking_id, aua.ambulance_id, a.plate_number, b.pickup_point, 
               b.booking_date, b.booking_time, h.name as hospital_name, 
               d.name as disease_name, p.patient_name, p.patient_age, p.patient_gender
        FROM ambulance_user_assignment aua
        JOIN ambulances a ON aua.ambulance_id = a.ambulance_id
        JOIN bookings b ON aua.booking_id = b.booking_id
        JOIN hospitals h ON b.hosp_id = h.hosp_id
        JOIN patients p ON b.patient_id = p.patient_id
        JOIN diseases d ON p.disease_id = d.disease_id  -- Correct join condition here
        JOIN ambulance_hospital_assignment ah ON a.ambulance_id = ah.ambulance_id
        WHERE ah.status = 'Busy'
        ORDER BY b.booking_date DESC";

$result = $conn->query($sql);

$busy_ambulances = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $busy_ambulances[] = $row;
    }
}

$conn->close();
?>

<div class="container mt-3">
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Booking Status</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>" role="alert">
                            <?php 
                            echo htmlspecialchars($_SESSION['message']); 
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Pick-up Point</th>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
                                <th>Hospital</th>
                                <th>Disease</th>
                                <th>Patient Name</th>
                                <th>Patient Age</th>
                                <th>Patient Gender</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($busy_ambulances)) : ?>
                                <tr>
                                    <td colspan="10" class="text-center">No Booking request yet.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($busy_ambulances as $ambulance) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ambulance['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['pickup_point']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['booking_date']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['booking_time']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['hospital_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['disease_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['patient_age']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['patient_gender']); ?></td>
                                        <td>
                                            

                                            <form action="booking_status.php" method="post">
                                                <input type="hidden" name="ambulance_id" value="<?php echo htmlspecialchars($ambulance['ambulance_id']); ?>">
                                                <button type="submit" name="set_available" class="btn btn-success btn-sm">Set Available</button>
                                            </form>
                                        </td>
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
