<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch ambulances assigned to drivers
$sql = "SELECT a.ambulance_id, a.plate_number, d.name as driver_name, d.status, d.driver_id
        FROM ambulances a
        JOIN ambulance_driver_assignment aua ON a.ambulance_id = aua.ambulance_id
        JOIN drivers d ON aua.driver_id = d.driver_id
        ORDER BY a.ambulance_id";
$result = $conn->query($sql);

$ambulances_to_drivers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ambulances_to_drivers[] = $row;
    }
}

$conn->close();
?>

<div class="container mt-5">
<a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ambulances Assigned to Drivers</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ambulance ID</th>
                                <th>Plate Number</th>
                                <th>Driver ID</th>
                                <th>Driver Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ambulances_to_drivers)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center">No ambulances assigned to drivers.</td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($ambulances_to_drivers as $ambulance) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ambulance['ambulance_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['plate_number']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['driver_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['driver_name']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['status']); ?></td>
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
