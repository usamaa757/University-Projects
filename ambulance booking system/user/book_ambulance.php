<?php

include('header.php');
include('../db_connection.php');

// Fetch nearest hospitals (for simplicity, fetching all hospitals here)
$sql = "SELECT * FROM hospitals";
$result = $conn->query($sql);

$hospitals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}

// Fetch diseases
$sql = "SELECT * FROM diseases ORDER BY name ASC";
$result = $conn->query($sql);

$diseases = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $diseases[] = $row;
    }
}

$conn->close();
?>

<div class="container mt-3">
    <a href="user_dashboard.php" class="btn btn-secondary mt-3 mb-3">Back to Dashboard</a>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ambulance Registration</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                            unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <form action="book_ambulance_process.php" method="post" class="mt-4">
                        <div class="form-group">
                            <label for="patient_name">Patient Name:</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name" required>
                        </div>
                        <div class="form-group">
                            <label for="patient_age">Patient Age:</label>
                            <input type="number" class="form-control" id="patient_age" name="patient_age" required>
                        </div>
                        <div class="form-group">
                            <label for="patient_gender">Patient Gender:</label>
                            <select class="form-control" id="patient_gender" name="patient_gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="disease">Diseases:</label>
                            <select class="form-control" id="diseaseSelect" name="disease_id">
                                <option value="">Select Disease</option>
                                <?php foreach ($diseases as $disease) : ?>
                                    <option value="<?php echo $disease['disease_id']; ?>"><?php echo $disease['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="patient_status">Patient Status:</label>
                            <textarea class="form-control" id="patient_status" name="patient_status"
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="pickup_point">Pick-up Point:</label>
                            <input type="text" class="form-control" id="pickup_point" name="pickup_point" required>
                        </div>
                        <div class="form-group">
                            <label for="destination">Destination (Hospital):</label>
                            <select class="form-control" id="hospitalSelect" name="destination" required>
                                <option value="">Select Hospital</option>
                                <?php foreach ($hospitals as $hospital) : ?>
                                    <option value="<?php echo $hospital['hosp_id']; ?>"><?php echo $hospital['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date">Date:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="time">Time:</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Book Ambulance</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hospitalElement = document.getElementById('hospitalSelect');
        const hospitalChoices = new Choices(hospitalElement, {
            searchEnabled: true
        });

        const diseaseElement = document.getElementById('diseaseSelect');
        const diseaseChoices = new Choices(diseaseElement, {
            searchEnabled: true
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>