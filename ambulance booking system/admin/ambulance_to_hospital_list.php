<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch ambulances assigned to hospitals
$sql = "SELECT a.ambulance_id, a.plate_number, h.name as hospital_name, ah.status
        FROM ambulances a
        JOIN ambulance_hospital_assignment ah ON a.ambulance_id = ah.ambulance_id
        JOIN hospitals h ON ah.hosp_id = h.hosp_id
        WHERE ah.status = 'Busy' OR ah.status = 'Available'
        ORDER BY h.name, a.ambulance_id";
$result = $conn->query($sql);

$ambulances_by_hospital = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospital_name = $row['hospital_name'];
        if (!isset($ambulances_by_hospital[$hospital_name])) {
            $ambulances_by_hospital[$hospital_name] = [];
        }
        $ambulances_by_hospital[$hospital_name][] = $row;
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
                    <h4 class="card-title">Ambulances Assigned to Hospitals</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($ambulances_by_hospital)) : ?>
                        <p class="text-center">No ambulances assigned to hospitals.</p>
                    <?php else : ?>
                        <?php foreach ($ambulances_by_hospital as $hospital_name => $ambulances) : ?>
                            <h5><?php echo htmlspecialchars($hospital_name); ?></h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ambulance ID</th>
                                        <th>Plate Number</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ambulances as $ambulance) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($ambulance['ambulance_id']); ?></td>
                                            <td><?php echo htmlspecialchars($ambulance['plate_number']); ?></td>
                                            <td><?php echo htmlspecialchars($ambulance['status']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
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
