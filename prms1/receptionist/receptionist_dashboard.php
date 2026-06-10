<?php
include 'header.php'; // Your existing receptionist header
include '../config/database.php';

// Fetch counts
$total_patients = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'];
$total_appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc()['total']; // assuming `appointments` table
?>

<div class="container mt-5">
    <h3 class="text-center mb-4">Receptionist Dashboard</h3>

    <div class="row text-center">
        <div class="col-md-6 mb-4">
            <div class="card border-primary shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people-fill"></i> Total Patients</h5>
                    <p class="display-6"><?= $total_patients ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-success shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-calendar-check-fill"></i> Appointments</h5>
                    <p class="display-6"><?= $total_appointments ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center mt-4">
        <div class="col-md-6 mb-3">
            <a href="view_patient.php" class="btn btn-outline-primary w-100">
                <i class="bi bi-people-fill"></i> Patients
            </a>
            <a href="add_care_plan.php" class="btn btn-outline-danger w-100 mt-2">
                <i class="bi bi-file-medical"></i> Add Care Plans
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="manage_appointments.php" class="btn btn-outline-success w-100">
                <i class="bi bi-calendar-event"></i> Manage Appointments
            </a>
            <a href="assign_care_plan.php" class="btn btn-outline-warning w-100 mt-2">
                <i class="bi bi-clipboard-check"></i> Assign Care Plan
            </a>
        </div>
    </div>

</div>