<!-- manage_drivers.php -->
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
include('../db_connection.php');

$error = '';
$success = '';

// Handle form submission to add a new driver
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $license_number = $_POST['license_number'];
    $status = $_POST['status'];

    if (empty($name) || empty($license_number) || empty($status)) {
        $error = 'Name, license number, and status are required.';
    } else {
        $sql = "INSERT INTO drivers (name, contact_info, license_number, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $contact_info, $license_number, $status);

        if ($stmt->execute()) {
            $success = 'Driver added successfully.';
        } else {
            $error = 'Error adding driver.';
        }

        $stmt->close();
    }
}

// Fetch all drivers
$sql = "SELECT * FROM drivers";
$result = $conn->query($sql);

$drivers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
                    <h4 class="card-title">Manage Drivers</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="manage_drivers.php" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Driver Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_info">Phone #:</label>
                            <input type="text" class="form-control" id="contact_info" name="contact_info">
                        </div>
                        <div class="form-group">
                            <label for="license_number">License Number:</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="class-center text-center">
                        <button type="submit" class="btn btn-primary">Add Driver</button>
                        </div>
                    </form>
                    <hr>
                    <h4 class="card-title mt-4">Driver List</h4>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone #</th>
                                <th>License Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($drivers as $driver) : ?>
                                <tr>
                                    <td><?php echo $driver['driver_id']; ?></td>
                                    <td><?php echo $driver['name']; ?></td>
                                    <td><?php echo $driver['phone_number']; ?></td>
                                    <td><?php echo $driver['license_number']; ?></td>
                                    <td><?php echo $driver['status']; ?></td>
                                    <td>
                                        <a href="edit_driver.php?id=<?php echo $driver['driver_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    </td>
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
