<?php

include 'header.php';
?>
<div class="container mt-4">
    <h2 class="mb-4">👤 Patient Dashboard</h2>
    <div class="alert alert-info">
        Logged in as: <strong><?php echo htmlspecialchars($patient_email); ?></strong>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title">View Profile</h5>
                    <p class="card-text">See your personal information and health details.</p>
                    <a href="view_profile.php" class="btn btn-outline-primary">Go</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title">Medical Records</h5>
                    <p class="card-text">Access your past medical records and reports.</p>
                    <a href="medical_record.php" class="btn btn-outline-success">View</a>
                    <a href="family_history.php" class="btn btn-outline-success">Add Family History</a>
                    <a href="family_record.php" class="btn btn-outline-success">Family Records</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title">Book Appointment</h5>
                    <p class="card-text">Schedule an appointment with a doctor.</p>
                    <a href="appointment_list.php" class="btn btn-outline-warning">Book</a>
                    <a href="my_appointment.php" class="btn btn-outline-warning">My Appointments</a>
                    <a href="care_plans.php?patient_id= <?= $_SESSION['user_id']; ?>" class="btn btn-outline-warning">My
                        Care Plans</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>