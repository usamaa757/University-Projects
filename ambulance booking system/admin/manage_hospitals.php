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

// Handle form submission to add a new hospital
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialties = $_POST['specialties'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (empty($name) || empty($specialties) || empty($phone) || empty($email)) {
        $error = 'All fields are required.';
    } else {
        $sql = "INSERT INTO hospitals (name, specialties, phone, email) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $name, $specialties, $phone, $email);

        if ($stmt->execute()) {
            $success = 'Hospital added successfully.';
        } else {
            $error = 'Error adding hospital.';
        }

        $stmt->close();
    }
}

// Fetch all hospitals
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
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Manage Hospitals</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="manage_hospitals.php" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Hospital Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="specialties">Specialties:</label>
                            <input type="text" class="form-control" id="specialties" name="specialties" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="class-center text-center">
                            <button type="submit" class="btn btn-primary">Add Hospital</button>
                        </div>
                    </form>
                    <hr>
                    <h4 class="card-title mt-4">Hospital List</h4>
                    <table class="table table-striped mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialties</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Adress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hospitals as $hospital) : ?>
                                <tr>
                                    <td><?php echo $hospital['hosp_id']; ?></td>
                                    <td><?php echo $hospital['name']; ?></td>
                                    <td><?php echo $hospital['specialties']; ?></td>
                                    <td><?php echo $hospital['phone']; ?></td>
                                    <td><?php echo $hospital['email']; ?></td>
                                    <td><?php echo $hospital['address']; ?></td>
                                    <td>
                                        <a href="edit_hospital.php?id=<?php echo $hospital['hosp_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
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