<?php
include 'header.php';
include '../config/database.php';

$doctor_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $treatment = $_POST['treatment'];
    $medicine_ids = $_POST['medicine_ids']; // array of selected medicine IDs
    $doctor_id = $_SESSION['doctor_id'];

    $stmt = $conn->prepare("INSERT INTO treatments (patient_id, doctor_id, medicine_id, treatment, treatment_date) VALUES (?, ?, ?, ?, NOW())");

    foreach ($medicine_ids as $medicine_id) {
        $stmt->bind_param("iiis", $patient_id, $doctor_id, $medicine_id, $treatment);
        $stmt->execute();
    }

    $stmt->close();
    header("Location: add_treatment.php?success=1");
    exit;
}

// Get patient_id from URL
$patient_id = $_GET['patient_id'] ?? null;
if (!$patient_id) {
    echo "<div class='alert alert-danger'>Patient ID is required.</div>";
    exit;
}

// Fetch the patient details
$patient_stmt = $conn->prepare("SELECT id, name FROM patients WHERE id = ?");
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();
$patient_stmt->close();

if (!$patient) {
    echo "<div class='alert alert-warning'>Patient not found.</div>";
    exit;
}

// Fetch all medicines
$med_stmt = $conn->prepare("SELECT id, name FROM medicines ORDER BY name ASC");
$med_stmt->execute();
$med_result = $med_stmt->get_result();
$medicines = $med_result->fetch_all(MYSQLI_ASSOC);
?>
<div class="container mt-5">
    <h3 class="mb-4">Add Treatment for <?= htmlspecialchars($patient['name']) ?></h3>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Treatment added successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">

                <div class="mb-3">
                    <label for="medicine_ids" class="form-label">Select Medicines:</label>
                    <select name="medicine_ids[]" id="medicine_ids" class="form-select" multiple required>
                        <?php foreach ($medicines as $med): ?>
                        <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="treatment" class="form-label">Treatment:</label>
                    <textarea name="treatment" id="treatment" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-success">Save Treatment</button>
            </form>
        </div>
    </div>
</div>