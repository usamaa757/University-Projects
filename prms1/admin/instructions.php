<?php
include 'header.php';
include '../config/database.php';

$patient_id = $_GET['patient_id'] ?? 0;

// Fetch patient info
$patient_stmt = $conn->prepare("SELECT name FROM patients WHERE id = ?");
$patient_stmt->bind_param("i", $patient_id);
$patient_stmt->execute();
$patient_result = $patient_stmt->get_result();
$patient = $patient_result->fetch_assoc();

if (!$patient) {
    echo "<div class='alert alert-danger'>Patient not found.</div>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['instruction_type'];
    $text = $_POST['instruction_text'];

    $stmt = $conn->prepare("INSERT INTO patient_instructions (patient_id, instruction_type, instruction_text, added_by) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iss", $patient_id, $type, $text);
    $stmt->execute();

    echo "<div class='alert alert-success'>Instruction saved successfully.</div>";
}
?>

<div class="container mt-5">
    <h3 class="text-center">Instructions for <?= htmlspecialchars($patient['name']) ?></h3>

    <!-- Add Instruction Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Add New Instruction</h5>
            <form method="post">
                <div class="mb-3">
                    <label>Instruction Type</label>
                    <select name="instruction_type" class="form-control" required>
                        <option value="pre">Pre-Procedural</option>
                        <option value="post">Post-Procedural</option>
                        <option value="discharge">Post-Discharge</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Instruction Text</label>
                    <textarea name="instruction_text" class="form-control" rows="4" required></textarea>
                </div>
                <button class="btn btn-primary">Save Instruction</button>
            </form>
        </div>
    </div>

    <!-- Display Instructions -->
    <?php
    $types = ['pre' => 'Pre-Procedural', 'post' => 'Post-Procedural', 'discharge' => 'Post-Discharge'];

    foreach ($types as $key => $label):
        $stmt = $conn->prepare("SELECT instruction_text, created_at FROM patient_instructions WHERE patient_id = ? AND instruction_type = ?");
        $stmt->bind_param("is", $patient_id, $key);
        $stmt->execute();
        $result = $stmt->get_result();
    ?>
    <h5 class="mt-4"><?= $label ?> Instructions</h5>
    <?php if ($result->num_rows > 0): ?>
    <ul class="list-group mb-3">
        <?php while ($row = $result->fetch_assoc()): ?>
        <li class="list-group-item">
            <?= nl2br(htmlspecialchars($row['instruction_text'])) ?>
            <br><small class="text-muted">Added on <?= date("d M Y", strtotime($row['created_at'])) ?></small>
        </li>
        <?php endwhile; ?>
    </ul>
    <?php else: ?>
    <div class="alert alert-info">No <?= strtolower($label) ?> instructions added.</div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>