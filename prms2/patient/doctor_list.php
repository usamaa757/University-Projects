<?php
include 'sidebar.php';
include '../db.php';

// Fetch doctors from database
$query = "SELECT doctor_id, name, specialization, email FROM doctors";
$result = $conn->query($query);
?>

<!-- Main Content -->
<div class="main-content">
    <div class="container py-5">
        <h2 class="mb-4 text-center">Available Doctors</h2>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><strong>Specialization:</strong>
                            <?= htmlspecialchars($row['specialization']); ?></p>
                        <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($row['email']); ?></p>
                        <a href="book_appointment.php?doctor_id=<?= $row['doctor_id']; ?>" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-1"></i> Book Appointment
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

</body>

</html>