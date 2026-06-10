<?php

include 'sidebar.php';
include '../db.php';

// Ensure the doctor is logged in
$admin_id = $_SESSION['admin_id'];

// You can retrieve doctor name from DB if needed. For now using a placeholder.

// Query to count all patients
$query_patients = "SELECT COUNT(*) AS total_patients FROM patients";
$stmt_patients = $conn->prepare($query_patients);
$stmt_patients->execute();
$result_patients = $stmt_patients->get_result();
$total_patients = $result_patients->fetch_assoc()['total_patients'] ?? 0;

$query_doctors = "SELECT COUNT(*) AS total_doctors FROM doctors";
$stmt_doctors = $conn->prepare($query_doctors);
$stmt_doctors->execute();
$result_doctors = $stmt_doctors->get_result();
$total_doctors = $result_doctors->fetch_assoc()['total_doctors'] ?? 0;

// Query to count treatments for the doctor
$query_treatments = "SELECT COUNT(*) AS total_treatments FROM treatment";
$stmt_treatments = $conn->prepare($query_treatments);
$stmt_treatments->execute();
$result_treatments = $stmt_treatments->get_result();
$total_treatments = $result_treatments->fetch_assoc()['total_treatments'] ?? 0;
?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">

        <div class="row g-4">
            <!-- Total Patients Card -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-primary text-white rounded-3 border-0 shadow">
                        <h5>Total Patients</h5>
                        <h2><?= $total_patients ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-success text-white  rounded-3 border-0 shadow">
                        <h5>Total Treatments</h5>
                        <h2><?= $total_treatments ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-info text-white  rounded-3 border-0 shadow">
                        <h5>Doctors Registered</h5>
                        <h2><?= $total_doctors ?></h2>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

</body>

</html>