<?php
include 'sidebar.php';
include '../db.php';


$patient_id = $_SESSION['patient_id'];

$sql = "SELECT pi.*, 
               p.name AS patient_name, 
               t.suggested_treatment, 
               d.name AS doctor_name
        FROM patient_instructions pi
        JOIN patients p ON pi.patient_id = p.patient_id
        JOIN treatment t ON pi.treatment_id = t.treatment_id
        JOIN doctors d ON t.doctor_id = d.doctor_id
        WHERE pi.patient_id = ?
        ORDER BY pi.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <div class="container p-3 card">
        <h4 class="mb-3 text-center">Patient-Specific Instructions List</h4>
        <?php
        echo "<div class='row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4'>";

        while ($row = $result->fetch_assoc()) {
            echo "
            <div class='col'>
                <div class='card h-100 shadow-sm border-info'>
                    <div class='card-header bg-info text-white'>
                        <h6 class='mb-0'>Patient: {$row['patient_name']}</h6>
                        <small>Doctor: {$row['doctor_name']}</small>
                    </div>
                    <div class='card-body'>
                        <p><strong>Suggested Treatment:</strong> {$row['suggested_treatment']}</p>
                        <p><strong>Pre-Procedure:</strong><br> {$row['pre_procedure']}</p>
                        <p><strong>Post-Procedure:</strong><br> {$row['post_procedure']}</p>
                        <p><strong>Post-Discharge:</strong><br> {$row['post_discharge']}</p>
                    </div>
                    <div class='card-footer text-muted text-end'>
                        Created on: " . date("d M, Y", strtotime($row['created_at'])) . "
                    </div>
                </div>
            </div>";
        }

        echo "
        </div>"; ?>

    </div>
</div>