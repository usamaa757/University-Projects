<!-- manage_doctors.php -->
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

// Handle form submission to add a new doctor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $hospital_id = $_POST['hospital_id'];
    $availability = $_POST['availability'];

    if (empty($name) || empty($specialty) || empty($hospital_id)) {
        $error = 'All fields are required.';
    } else {
        $sql = "INSERT INTO doctors (name, specialty, hosp_id, availability) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $name, $specialty, $hospital_id, $availability);

        if ($stmt->execute()) {
            $success = 'Doctor added successfully.';
        } else {
            $error = 'Error adding doctor.';
        }

        $stmt->close();
    }
}

// Fetch all doctors
$sql = "SELECT d.*, h.name as hospital_name FROM doctors d JOIN hospitals h ON d.hosp_id = h.hosp_id";
$result = $conn->query($sql);

$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
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

$conn->close();
?>

<div class="container">
                    <a href="admin_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Manage Doctors</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="manage_doctors.php" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Doctor Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="specialty">Specialty:</label>
                            <input type="text" class="form-control" id="specialty" name="specialty" required>
                        </div>
                        <div class="form-group">
                            <label for="availability">Availability (e.g., Mon-Tue 9AM-10PM):</label>
                            <input type="text" class="form-control" id="availability" name="availability" required>
                        </div>
                        <div class="form-group">
                            <label for="hospitalSelect">Hospital:</label>
                            <select class="form-control" id="hospitalSelect" name="hospital_id" required>
                                <option value="">Select Hospital</option>
                                <?php foreach ($hospitals as $hospital) : ?>
                                    <option value="<?php echo $hospital['hosp_id']; ?>"><?php echo $hospital['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="class-center text-center">
                        <button type="submit" class="btn btn-primary">Add Doctor</button>
                        </div>
                    </form>
                    <hr>
                    <h4 class="card-title mt-4">Doctor List</h4>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Hospital</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctors as $doctor) : ?>
                                <tr>
                                    <td><?php echo $doctor['doctor_id']; ?></td>
                                    <td><?php echo $doctor['name']; ?></td>
                                    <td><?php echo $doctor['specialty']; ?></td>
                                    <td><?php echo $doctor['hospital_name']; ?></td>
                                    <td><?php echo $doctor['availability']; ?></td>
                                    <td>
                                        <a href="edit_doctor.php?id=<?php echo $doctor['doctor_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
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
