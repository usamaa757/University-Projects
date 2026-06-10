<?php
include 'sidebar.php';
include '../db.php';

// Ensure the doctor is logged in
$doctor_id = $_SESSION['doctor_id'] ?? null;


// Query to count all patients
$query_patients = "SELECT COUNT(*) AS total_patients FROM patients";
$stmt_patients = $conn->prepare($query_patients);
$stmt_patients->execute();
$result_patients = $stmt_patients->get_result();
$total_patients = $result_patients->fetch_assoc()['total_patients'] ?? 0;

// Query to count appointments for the doctor
$query_appointments = "SELECT COUNT(*) AS total_appointments FROM appointments WHERE doctor_id = ? AND status = 'Pending'";
$stmt_appointments = $conn->prepare($query_appointments);
$stmt_appointments->bind_param('i', $doctor_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
$total_appointments = $result_appointments->fetch_assoc()['total_appointments'] ?? 0;
?>


<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">

        <div class="row g-4">
            <!-- Total Patients Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h5>Total Patients</h5>
                            <h2 class="mb-0"><?php echo $total_patients; ?></h2>
                        </div>
                    </div>
                    <a href="patient_list.php" class="btn btn-sm btn-primary mt-3">View All</a>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center">
                        <div class="card-icon me-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5>Appointments</h5>
                            <h2 class="mb-0"><?php echo $total_appointments; ?></h2>
                        </div>
                    </div>
                    <a href="appointments.php" class="btn btn-sm btn-primary mt-3">View Appointments</a>
                </div>
            </div>
        </div>


        <div class="footer">
            &copy; <?php echo date("Y"); ?> Doctor's Portal. All rights reserved.
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
</body>

</html>