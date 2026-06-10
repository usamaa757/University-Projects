<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch available drivers
$sql_drivers = "SELECT * FROM drivers";
$result_drivers = $conn->query($sql_drivers);

$drivers = [];
if ($result_drivers->num_rows > 0) {
    while ($row = $result_drivers->fetch_assoc()) {
        $drivers[] = $row;
    }
}

$conn->close();
?>

<div class="container">
<a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Available Drivers</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Driver ID</th>
                                <th>Driver Name</th>
                                <th>Pone Number</th>
                                <th>License Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($drivers)) : ?>
                                <tr>
                                    <td colspan="2" class="text-center">No available drivers.</td>
                                </tr>
                            <?php else : ?>
                                <?php $sno =1; foreach ($drivers as $driver) : ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><?php echo htmlspecialchars($driver['driver_id']); ?></td>
                                        <td><?php echo htmlspecialchars($driver['name']); ?></td>
                                        <td><?php echo htmlspecialchars($driver['phone_number']); ?></td>
                                        <td><?php echo htmlspecialchars($driver['license_number']); ?></td>
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
