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
    $hospital_id = $_GET['id'];

    // Fetch hospital details
    $sql = "SELECT * FROM hospitals WHERE hosp_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $hospital_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hospital = $result->fetch_assoc();
    $stmt->close();

    if (!$hospital) {
        $error = 'Hospital not found.';
    }
} else {
    header("Location: manage_hospitals.php");
    exit();
}

// Handle form submission to update hospital details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialties = $_POST['specialties'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (empty($name) || empty($specialties) || empty($phone) || empty($email)) {
        $error = 'All fields are required.';
    } else {
        $sql = "UPDATE hospitals SET name = ?, specialties = ?, phone = ?, email = ? WHERE hosp_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $name, $specialties, $phone, $email, $hospital_id);

        if ($stmt->execute()) {
            $success = 'Hospital updated successfully.';
        } else {
            $error = 'Error updating hospital.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<div class="container">
    <a href="manage_hospitals.php" class="btn btn-secondary mt-3 mb-3">Back to Manage Hospitals</a>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Hospital</h4>
                </div>
                <div class="card-body">
                    <?php if ($error) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success) : ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form action="edit_hospital.php?id=<?php echo $hospital_id; ?>" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="name">Hospital Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($hospital['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="specialties">Specialties:</label>
                            <input type="text" class="form-control" id="specialties" name="specialties" value="<?php echo htmlspecialchars($hospital['specialties']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number:</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($hospital['phone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($hospital['email']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Hospital</button>
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
