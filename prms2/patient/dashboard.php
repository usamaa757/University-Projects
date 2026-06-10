<?php
include "../db.php";
include 'sidebar.php';


$patient_id = $_SESSION['patient_id'];

// Fetch patient name
$stmt = $conn->prepare("SELECT name FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();


?>

<!-- Main Content Area -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row g-4 text-center">
            <!-- Appointments Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 shadow">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-check fs-1 text-primary me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Appointments</h5>
                        </div>
                    </div>
                    <a href="appointments.php" class="btn btn-sm btn-primary mt-3">View Appointments</a>
                </div>
            </div>

            <!-- Patients Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 shadow">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-journal-medical fs-1 text-success me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Medical History</h5>
                        </div>
                    </div>
                    <a href="medical_history.php" class="btn btn-sm btn-success mt-3">View Medical Records</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5 text-muted">
            &copy; <?= date("Y"); ?> Doctor's Portal. All rights reserved.
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>