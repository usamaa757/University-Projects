<?php
include 'sidebar.php';
include '../db.php'; // database connection

$msg = '';
$patient_name = '';
if (isset($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];

    $stmt = $conn->prepare("SELECT name FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $patient_name = $row['name'];
    } else {
        $patient_name = "Unknown Patient";
    }
}

if (isset($_POST['submit'])) {
    $patient_id = $_POST['patient_id'];
    $condition_name = $_POST['condition_name'];
    $relationship = $_POST['relationship'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO family_history (patient_id, condition_name, relationship, status, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $patient_id, $condition_name, $relationship, $status, $notes);

    if ($stmt->execute()) {
        echo "<script>alert('Family history added successfully!'); window.location.href='add_family_history.php?patient_id=$patient_id';</script>";
    } else {
        echo "<script>alert('Error added family history.'); window.history.back();</script>";
        exit;
    }
}
?>

<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-0">Add Family History for <?= htmlspecialchars($patient_name); ?></h4>

        <div class="">
            <div class="card-body">
                <?php if (!empty($msg)) {
                    echo $msg;
                }
                ?>
                <form method="post">
                    <input type="hidden" name="patient_id" value="<?= htmlspecialchars($_GET['patient_id']); ?>">

                    <div class="mb-3">
                        <label for="condition_name" class="form-label">Condition Name</label>
                        <input type="text" class="form-control" id="condition_name" name="condition_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="relationship" class="form-label">Relationship</label>
                        <select class="form-select" id="relationship" name="relationship" required>
                            <option value="">Select Relationship</option>
                            <option value="Mother">Mother</option>
                            <option value="Father">Father</option>
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Grandmother">Grandmother</option>
                            <option value="Grandfather">Grandfather</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Positive">Positive</option>
                            <option value="Negative">Negative</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-success w-100">Save Family History</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>