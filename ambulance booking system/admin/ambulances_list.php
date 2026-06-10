<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch available ambulances
$sql_ambulances = "SELECT * FROM ambulances";
$result_ambulances = $conn->query($sql_ambulances);

$ambulances = [];
if ($result_ambulances->num_rows > 0) {
    while ($row = $result_ambulances->fetch_assoc()) {
        $ambulances[] = $row;
    }
}

$conn->close();
?>

<div class="container">
<a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Available Ambulances</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ambulance ID</th>
                                <th>Plate Number</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ambulances)) : ?>
                                <tr>
                                    <td colspan="2" class="text-center">No available ambulances.</td>
                                </tr>
                            <?php else : ?>
                                <?php $sno =1; foreach ($ambulances as $ambulance) : ?>
                                    <tr>
                                        <td><?php echo $sno++ ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['ambulance_id']); ?></td>
                                        <td><?php echo htmlspecialchars($ambulance['plate_number']); ?></td>
                                      
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
