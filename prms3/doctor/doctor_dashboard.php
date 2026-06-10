<?php

include 'header.php';
?>
<div class="container mt-4">
    <h2 class="mb-4">Doctor Dashboard</h2>
    <div class="alert alert-success">
        Logged in as: <strong><?php echo htmlspecialchars($doctor_email); ?></strong>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title">My Profile</h5>
                    <p class="card-text">View and update your personal details.</p>
                    <a href="doctor_profile.php" class="btn btn-outline-primary">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <h5 class="card-title">Appointments</h5>
                    <p class="card-text">Check scheduled patient appointments.</p>
                    <a href="appointments.php" class="btn btn-outline-warning">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h5 class="card-title">Patient Records</h5>
                    <p class="card-text">Access medical records of patients.</p>
                    <a href="view_patients.php" class="btn btn-outline-danger">Access</a>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>