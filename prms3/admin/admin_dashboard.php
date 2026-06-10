<?php
include 'header.php';
include '../config/database.php';

// Count total patients
$patients_count = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'];

// Count total doctors
$doctors_count = $conn->query("SELECT COUNT(*) AS total FROM doctors")->fetch_assoc()['total'];

// Count total receptionists
$receptionists_count = $conn->query("SELECT COUNT(*) AS total FROM receptionists")->fetch_assoc()['total'];

// Count total tests assigned
$medicines_count = $conn->query("SELECT COUNT(*) AS total FROM medicines")->fetch_assoc()['total'];
?>
<div class="container mt-5">
    <h3 class="mb-4 text-center">📊 Admin Dashboard</h3>
    <div class="row text-center">

        <div class="col-md-3 mb-4">
            <div class="card border-success shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person-badge"></i> Doctors</h5>
                    <p class="display-6"><?= $doctors_count ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-info shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person-gear"></i> Receptionists</h5>
                    <p class="display-6"><?= $receptionists_count ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-primary shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people-fill"></i> Patients</h5>
                    <p class="display-6"><?= $patients_count ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-danger shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-capsule-pill"></i> Medicines</h5>
                    <p class="display-6"><?= $medicines_count ?></p>
                </div>
            </div>
        </div>

    </div>

    <!-- Buttons to view more -->
    <div class="row mt-4 text-center">
        <div class="col-md-3 mb-3">
            <a href="manage_doctor.php" class="btn btn-success w-100">Manage Doctors</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="manage_receptionist.php" class="btn btn-info w-100">Manage Receptionists</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="manage_patient.php" class="btn btn-primary w-100">Manage Patients</a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="medicine_list.php" class="btn btn-danger w-100">Manage Medicine</a>
        </div>
    </div>
</div>