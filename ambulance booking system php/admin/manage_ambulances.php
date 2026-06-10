<!-- manage_ambulance.php -->
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

// Handle form submission to add a new ambulance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ambulance_number = $_POST['ambulance_number'];
    $status = $_POST['status'];

    if (empty($ambulance_number) || empty($status)) {
        $error = 'All fields are required.';
    } else {
        $sql = "INSERT INTO ambulances (ambulance_number, status) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $ambulance_number, $status);

        if ($stmt->execute()) {
            $success = 'Ambulance added successfully.';
        } else {
            $error = 'Error adding ambulance.';
        }

        $stmt->close();
    }
}

// Fetch all ambulances
$sql = "SELECT * FROM ambulances";
$result = $conn->query($sql);

$ambulances = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ambulances[] = $row;
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
                    <h4 class="card-title">Manage Ambulances</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="manage_ambulance.php" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="ambulance_number">Ambulance Number:</label>
                            <input type="text" class="form-control" id="ambulance_number" name="ambulance_number" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Add Ambulance</button>
                    </form>
                    <hr>
                    <h4 class="card-title mt-4">Ambulance List</h4>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Number</th>
                                <!-- <th>Status</th> -->
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ambulances as $ambulance) : ?>
                                <tr>
                                    <td><?php echo $ambulance['ambulance_id']; ?></td>
                                    <td><?php echo $ambulance['plate_number']; ?></td>
                                    <!-- <td><?php echo $ambulance['status']; ?></td> -->
                                    <td>
                                        <a href="edit_ambulance.php?id=<?php echo $ambulance['ambulance_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="delete_ambulance.php?id=<?php echo $ambulance['ambulance_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ambulance?');">Delete</a>
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
