<!-- edit_doctor.php -->
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
    $doctor_id = $_GET['id'];

    // Fetch doctor details
    $sql = "SELECT * FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();
    $stmt->close();

    if (!$doctor) {
        $error = 'Doctor not found.';
    }
} else {
    header("Location: manage_doctors.php");
    exit();
}

// Fetch hospitals for the select dropdown
$sql = "SELECT * FROM hospitals";
$result = $conn->query($sql);

$hospitals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

// Handle form submission to update doctor details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $hospital_id = $_POST['hosp_id'];
    $availability = $_POST['availability'];

    if (empty($name) || empty($specialty) || empty($hospital_id)) {
        $error = 'All fields are required.';
    } else {
        $sql = "UPDATE doctors SET name = ?, specialty = ?, hosp_id = ?, availability = ? WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssisi', $name, $specialty, $hospital_id, $availability, $doctor_id);

        if ($stmt->execute()) {
            $success = 'Doctor updated successfully.';
        } else {
            $error = 'Error updating doctor.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<div class="container">
<a href="manage_doctors.php" class="btn btn-secondary mt-3 mb-3">Back to Doctor List</a>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Doctor</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="edit_doctor.php?id=<?php echo $doctor_id; ?>" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Doctor Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $doctor['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="specialty">Specialty:</label>
                            <input type="text" class="form-control" id="specialty" name="specialty" value="<?php echo $doctor['specialty']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="availability">Availability:</label>
                            <input type="text" class="form-control" id="availability" name="availability" value="<?php echo $doctor['availability']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="hospitalSelect">Hospital:</label>
                            <select class="form-control" id="hospitalSelect" name="hosp_id" required>
                                <?php foreach ($hospitals as $hospital) : ?>
                                    <option value="<?php echo $hospital['hosp_id']; ?>" <?php echo ($hospital['hosp_id'] == $doctor['hosp_id']) ? 'selected' : ''; ?>>
                                        <?php echo $hospital['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Doctor</button>
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
