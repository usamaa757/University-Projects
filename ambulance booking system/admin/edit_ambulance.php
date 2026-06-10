<!-- edit_ambulance.php -->
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
    $ambulance_id = $_GET['id'];

    // Fetch ambulance details
    $sql = "SELECT * FROM ambulances WHERE ambulance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $ambulance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ambulance = $result->fetch_assoc();
    $stmt->close();

    if (!$ambulance) {
        $error = 'Ambulance not found.';
    }
} else {
    header("Location: manage_ambulances.php");
    exit();
}

// Handle form submission to update ambulance details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plate_number = $_POST['plate_number'];
    $status = $_POST['status'];

    if (empty($plate_number) || empty($status)) {
        $error = 'All fields are required.';
    } else {
        $sql = "UPDATE ambulances SET plate_number = ?, status = ? WHERE ambulance_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $plate_number, $status, $ambulance_id);

        if ($stmt->execute()) {
            $success = 'Ambulance updated successfully.';
        } else {
            $error = 'Error updating ambulance.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<div class="container">
<a href="manage_ambulances.php" class="btn btn-secondary mt-3 mb-3">Back to Ambulance List</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Ambulance</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="edit_ambulance.php?id=<?php echo $ambulance_id; ?>" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="plate_number">Ambulance Number:</label>
                            <input type="text" class="form-control" id="plate_number" name="plate_number" value="<?php echo $ambulance['plate_number']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="Available" <?php echo ($ambulance['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                <option value="Unavailable" <?php echo ($ambulance['status'] == 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Ambulance</button>
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
