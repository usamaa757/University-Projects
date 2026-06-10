<?php
include 'sidebar.php';
include '../db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM patients WHERE patient_id = $id");
$patient = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $conn->query("UPDATE patients SET name='$name', age='$age', gender='$gender', contact='$contact',
    address='$address' WHERE patient_id=$id");
    echo "<script>alert('Patient updated successfully!'); window.location.href='patients.php';</script>";
}
?>


<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-0">Edit Patient</h4>

        <div class="">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3"><input type="text" name="name" class="form-control"
                            value="<?= $patient['name'] ?>" required></div>
                    <div class="mb-3"><input type="number" name="age" class="form-control"
                            value="<?= $patient['age'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <select name="gender" class="form-control" required>
                            <option <?= $patient['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option <?= $patient['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                            <option <?= $patient['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3"><input type="text" name="contact" class="form-control"
                            value="<?= $patient['contact'] ?>" required></div>
                    <div class="mb-3"><textarea name="address"
                            class="form-control"><?= $patient['address'] ?></textarea></div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="patients.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>