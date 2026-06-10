<?php
include 'sidebar.php';
include '../db.php';
$patient_id = $_GET['patient_id'] ?? null;
$treatment_id = $_GET['treatment_id'] ?? null;
$doctor_id = $_SESSION['doctor_id'];

// Get patient name
$patient_name = '';
$pat_result = $conn->query("SELECT name FROM patients WHERE patient_id = $patient_id");
if ($pat_result && $pat_result->num_rows > 0) {
    $patient_name = $pat_result->fetch_assoc()['name'];
}

// Get treatment name or summary
$treatment_name = '';
$treat_result = $conn->query("SELECT suggested_treatment FROM treatment WHERE treatment_id = $treatment_id");
if ($treat_result && $treat_result->num_rows > 0) {
    $treatment_name = $treat_result->fetch_assoc()['suggested_treatment'];
}
if (isset($_POST['submit'])) {
    $patient_id = $_POST['patient_id'];
    $treatment_id = $_POST['treatment_id'];
    $doctor_id = $_SESSION['doctor_id']; // doctor from session
    $pre = $_POST['pre_procedure'];
    $post = $_POST['post_procedure'];
    $discharge = $_POST['post_discharge'];

    $stmt = $conn->prepare("INSERT INTO patient_instructions (patient_id, treatment_id, doctor_id, pre_procedure, post_procedure, post_discharge) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $patient_id, $treatment_id, $doctor_id, $pre, $post, $discharge);

    if ($stmt->execute()) {
        echo "<script>alert('Instructions saved successfully!'); window.location.href='instruction_list.php';</script>";
    } else {
        echo "<script>alert('Error saving instructions.'); window.history.back();</script>";
    }
}

?>

<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-3 text-center">Add Specific Instructions</h4>
        <div class="mb-3">
            <label class="form-label">Patient Name:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($patient_name) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Treatment Summary:</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($treatment_name) ?>" readonly>
        </div>

        <div class="">
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="patient_id" value="<?= $_GET['patient_id'] ?>">
                    <input type="hidden" name="treatment_id" value="<?= $_GET['treatment_id'] ?>">

                    <div class="mb-3">
                        <label for="pre_procedure" class="form-label">Pre-Procedural Instructions</label>
                        <textarea class="form-control" name="pre_procedure" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="post_procedure" class="form-label">Post-Procedural Instructions</label>
                        <textarea class="form-control" name="post_procedure" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="post_discharge" class="form-label">Post-Discharge Instructions</label>
                        <textarea class="form-control" name="post_discharge" rows="3" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary">Save Instructions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>