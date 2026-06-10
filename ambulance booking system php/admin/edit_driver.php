<!-- edit_driver.php -->
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

if (isset($_GET['id'])) {
    $driver_id = $_GET['id'];

    // Fetch driver details
    $sql = "SELECT * FROM drivers WHERE driver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $driver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $driver = $result->fetch_assoc();
    $stmt->close();

    if (!$driver) {
        $error = 'Driver not found.';
    }
} else {
    header("Location: manage_drivers.php");
    exit();
}

// Handle form submission to update driver details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $license_number = $_POST['license_number'];
    $status = $_POST['status'];

    if (empty($name) || empty($license_number) || empty($status)) {
        $error = 'Name, license number, and status are required.';
    } else {
        $sql = "UPDATE drivers SET name = ?, phone_number = ?, license_number = ?, status = ? WHERE driver_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $phone_number, $license_number, $status, $driver_id);

        if ($stmt->execute()) {
            $success = 'Driver details updated successfully.';
            // Fetch updated details
            $sql = "SELECT * FROM drivers WHERE driver_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $driver = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = 'Error updating driver details.';
        }

    }
    // $stmt->close();
}

$conn->close();
?>

<div class="container">
                    <a href="manage_drivers.php" class="btn btn-secondary mt-3 mb-3">Back to Drivers List</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Driver</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="edit_driver.php?id=<?php echo $driver['driver_id']; ?>" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Driver Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $driver['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_info">Phone #:</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo $driver['phone_number']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="license_number">License Number:</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="<?php echo $driver['license_number']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Available" <?php if ($driver['status'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Unavailable" <?php if ($driver['status'] == 'Unavailable') echo 'selected'; ?>>Unavailable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Driver</button>
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
