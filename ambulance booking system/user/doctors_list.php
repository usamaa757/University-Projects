<?php

include('header.php');
include('../db_connection.php');

// Fetch doctors with hospital details
$sql = "
    SELECT d.name AS doctor_name, d.specialty, d.availability, h.name AS hospital_location
    FROM doctors d
    JOIN hospitals h ON d.hosp_id = h.hosp_id
    ORDER BY d.name ASC
";
$result = $conn->query($sql);

$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

$conn->close();
?>

<div class="container">
    <a href="user_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Doctor List</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Location</th>
                                <th>Timing</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctors as $doctor) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($doctor['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['hospital_location']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['availability']); ?></td>
                                </tr>
                            <?php endforeach; ?>
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