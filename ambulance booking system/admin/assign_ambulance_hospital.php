<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch all ambulances and their assignments
$sql_ambulances = "
    SELECT 
        a.ambulance_id, 
        a.plate_number, 
        h.name AS hospital_name, 
        h.hosp_id AS assigned_hosp_id
    FROM ambulances a
    LEFT JOIN ambulance_hospital_assignment aha ON a.ambulance_id = aha.ambulance_id
    LEFT JOIN hospitals h ON aha.hosp_id = h.hosp_id
";
$result_ambulances = $conn->query($sql_ambulances);

$ambulances = [];
if ($result_ambulances->num_rows > 0) {
    while ($row = $result_ambulances->fetch_assoc()) {
        $ambulances[] = $row;
    }
}

// Fetch all hospitals
$sql_hospitals = "
    SELECT h.hosp_id, h.name
    FROM hospitals h
";
$result_hospitals = $conn->query($sql_hospitals);

$hospitals = [];
if ($result_hospitals->num_rows > 0) {
    while ($row = $result_hospitals->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign'])) {
    $ambulance_id = $_POST['ambulance_id'];
    $hosp_id = $_POST['hosp_id'];

    if ($ambulance_id && $hosp_id) {
        $stmt = $conn->prepare("INSERT INTO ambulance_hospital_assignment (ambulance_id, hosp_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $ambulance_id, $hosp_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Ambulance assigned to hospital successfully.";
            // Update the status of the ambulance to 'Busy'
            $update_status_sql = "UPDATE ambulances SET status = 'Busy' WHERE ambulance_id = ?";
            $update_status_stmt = $conn->prepare($update_status_sql);
            $update_status_stmt->bind_param("i", $ambulance_id);
            $update_status_stmt->execute();
            $update_status_stmt->close();
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Please select both ambulance and hospital.";
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
                    <h4 class="card-title">Assign Ambulance to Hospital</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-info">
                            <?php
                            echo htmlspecialchars($_SESSION['message']);
                            unset($_SESSION['message']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <form action="assign_ambulance_hospital.php" method="post">
                        <div class="form-group">
                            <label for="ambulance_id">Select Ambulance:</label>
                            <select class="form-control" id="ambulance_id" name="ambulance_id" required>
                                <option value="">Select Ambulance</option>
                                <?php foreach ($ambulances as $ambulance) : ?>
                                    <option value="<?php echo htmlspecialchars($ambulance['ambulance_id']); ?>">
                                        <?php
                                        echo htmlspecialchars($ambulance['plate_number']);
                                        // Display hospital name if assigned
                                        echo $ambulance['hospital_name'] ? " - " . htmlspecialchars($ambulance['hospital_name']) : " - Available";
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hosp_id">Select Hospital:</label>
                            <select class="form-control" id="hosp_id" name="hosp_id" required>
                                <option value="">Select Hospital</option>
                                <?php foreach ($hospitals as $hospital) : ?>
                                    <option value="<?php echo htmlspecialchars($hospital['hosp_id']); ?>">
                                        <?php echo htmlspecialchars($hospital['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="assign" class="btn btn-primary">Assign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>