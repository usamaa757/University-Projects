<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

// Fetch available ambulances that are not already assigned
$sql_ambulances = "
    SELECT a.ambulance_id, a.plate_number
    FROM ambulances a
    LEFT JOIN ambulance_driver_assignment ada ON a.ambulance_id = ada.ambulance_id
    WHERE a.status = 'available' AND ada.ambulance_id IS NULL
";
$result_ambulances = $conn->query($sql_ambulances);

$ambulances = [];
if ($result_ambulances->num_rows > 0) {
    while ($row = $result_ambulances->fetch_assoc()) {
        $ambulances[] = $row;
    }
}

// Fetch drivers who are not currently assigned to any ambulance (for new assignments)
$sql_available_drivers = "
    SELECT d.driver_id, d.name
    FROM drivers d
    LEFT JOIN ambulance_driver_assignment ada ON d.driver_id = ada.driver_id
    WHERE ada.driver_id IS NULL
";
$result_available_drivers = $conn->query($sql_available_drivers);

$available_drivers = [];
if ($result_available_drivers->num_rows > 0) {
    while ($row = $result_available_drivers->fetch_assoc()) {
        $available_drivers[] = $row;
    }
}

// Fetch all drivers (for editing assignments)
$sql_all_drivers = "SELECT driver_id, name FROM drivers";
$result_all_drivers = $conn->query($sql_all_drivers);

$all_drivers = [];
if ($result_all_drivers->num_rows > 0) {
    while ($row = $result_all_drivers->fetch_assoc()) {
        $all_drivers[] = $row;
    }
}

// Fetch current assignments
$sql_current_assignments = "
    SELECT ada.id, a.ambulance_id, a.plate_number, d.driver_id, d.name as driver_name
    FROM ambulance_driver_assignment ada
    JOIN ambulances a ON ada.ambulance_id = a.ambulance_id
    JOIN drivers d ON ada.driver_id = d.driver_id
";
$result_current_assignments = $conn->query($sql_current_assignments);

$current_assignments = [];
if ($result_current_assignments->num_rows > 0) {
    while ($row = $result_current_assignments->fetch_assoc()) {
        $current_assignments[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['assign'])) {
        $ambulance_id = $_POST['ambulance_id'];
        $driver_id = $_POST['driver_id'];

        if ($ambulance_id && $driver_id) {
            $stmt = $conn->prepare("INSERT INTO ambulance_driver_assignment (ambulance_id, driver_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $ambulance_id, $driver_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Ambulance assigned to driver successfully.";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['message'] = "Please select both ambulance and driver.";
        }
    } elseif (isset($_POST['update'])) {
        $assignment_id = $_POST['assignment_id'];
        $new_driver_id = $_POST['new_driver_id'];

        if ($assignment_id && $new_driver_id) {
            $stmt = $conn->prepare("UPDATE ambulance_driver_assignment SET driver_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_driver_id, $assignment_id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Assignment updated successfully.";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $_SESSION['message'] = "Please select a new driver.";
        }
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
                    <h4 class="card-title">Assign or Edit Ambulance-Driver Assignments</h4>
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

                    <form action="assign_ambulance_driver.php" method="post">
                        <h5>Assign New Ambulance</h5>
                        <div class="form-group">
                            <label for="ambulance_id">Select Ambulance:</label>
                            <select class="form-control" id="ambulance_id" name="ambulance_id" required>
                                <option value="">Select Ambulance</option>
                                <?php foreach ($ambulances as $ambulance) : ?>
                                    <option value="<?php echo htmlspecialchars($ambulance['ambulance_id']); ?>">
                                        <?php echo htmlspecialchars($ambulance['ambulance_id'])." - ".htmlspecialchars($ambulance['plate_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="driver_id">Select Driver:</label>
                            <select class="form-control" id="driver_id" name="driver_id" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($available_drivers as $driver) : ?>
                                    <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>">
                                        <?php echo htmlspecialchars($driver['driver_id']) . " - " . htmlspecialchars($driver['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="assign" class="btn btn-primary">Assign</button>
                        </div>
                    </form>

                    <hr>

                    <h5>Edit Existing Ambulance Assignment</h5>
                    <form action="assign_ambulance_driver.php" method="post">
                        <div class="form-group">
                            <label for="assignment_id">Select Assignment:</label>
                            <select class="form-control" id="assignment_id" name="assignment_id" required>
                                <option value="">Select Assignment</option>
                                <?php foreach ($current_assignments as $assignment) : ?>
                                    <option value="<?php echo htmlspecialchars($assignment['id']); ?>">
                                        Ambulance ID: <?php echo htmlspecialchars($assignment['ambulance_id']); ?> - Driver: <?php echo htmlspecialchars($assignment['driver_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_driver_id">Select New Driver:</label>
                            <select class="form-control" id="new_driver_id" name="new_driver_id" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($all_drivers as $driver) : ?>
                                    <option value="<?php echo htmlspecialchars($driver['driver_id']); ?>">
                                        <?php echo htmlspecialchars($driver['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" name="update" class="btn btn-primary">Update Assignment</button>
                        </div>
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
