<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
include('header.php');
include('../db_connection.php');

// Fetch assigned ambulances and driver details
$sql = "SELECT ada.id, a.ambulance_id, a.plate_number, d.driver_id, d.name AS driver_name, 
               d.license_number, d.phone_number
        FROM ambulance_driver_assignment ada
        JOIN ambulances a ON ada.ambulance_id = a.ambulance_id
        JOIN drivers d ON ada.driver_id = d.driver_id
        ORDER BY d.name ASC";

$result = $conn->query($sql);

$assignments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}

$conn->close();
?>

<div class="container mt-5">
<a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Assigned Ambulances to Drivers</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ambulance ID</th>
                                <th>Plate Number</th>
                                <th>Driver ID</th>
                                <th>Driver Name</th>
                                <th>License Number</th>
                                <th>Contact Number</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assignments)) : ?>
                                <tr>
                                    <td colspan="8" class="text-center">No assignments available.</td>
                                </tr>
                            <?php else : ?>
                                <?php $sno =1; foreach ($assignments as $assignment) : ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><?php echo htmlspecialchars($assignment['ambulance_id']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['driver_id']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['driver_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['license_number']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['phone_number']); ?></td>
                                       
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
