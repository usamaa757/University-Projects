<?php
include 'header.php'; // Include your header file
include '../db_connection.php'; // Include database connection

// Fetch plants that belong to the seller
$seller_id = $_SESSION['seller_id'];
$query = "SELECT * FROM plants WHERE seller_id = '$seller_id'";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plant_id = $_POST['plant_id'];
    $watering_frequency = $_POST['watering_frequency'];
    $sunlight_needs = $_POST['sunlight_needs'];
    $temperature_range = $_POST['temperature_range'];
    $repotting_guidelines = $_POST['repotting_guidelines'];

    // Insert the plant care information into the database
    $insert_query = "INSERT INTO plant_care (plant_id, watering_frequency, sunlight_needs, temperature_range, repotting_guidelines) 
                     VALUES ('$plant_id', '$watering_frequency', '$sunlight_needs', '$temperature_range', '$repotting_guidelines')";

    if (mysqli_query($conn, $insert_query)) {
        $success_message = "Plant care information added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!-- Plant Care Form -->
<div class="container mt-5 round border shadow p-3">
    <h3>Add Plant Care Information</h3>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="add_plant_care.php" method="POST">
        <div class="form-group">
            <label for="plant_id">Plant:</label>
            <select name="plant_id" class="form-control" required>
                <option value="">Select a Plant</option>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <option value="<?php echo $row['plant_id']; ?>"><?php echo $row['plant_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="watering_frequency">Watering Frequency:</label>
            <input type="text" name="watering_frequency" class="form-control" placeholder="e.g., Every 3 days" required>
        </div>
        <div class="form-group">
            <label for="sunlight_needs">Sunlight Needs:</label>
            <input type="text" name="sunlight_needs" class="form-control" placeholder="e.g., Indirect sunlight"
                required>
        </div>
        <div class="form-group">
            <label for="temperature_range">Temperature Range:</label>
            <input type="text" name="temperature_range" class="form-control" placeholder="e.g., 18°C to 24°C" required>
        </div>
        <div class="form-group">
            <label for="repotting_guidelines">Repotting Guidelines:</label>
            <textarea name="repotting_guidelines" class="form-control" placeholder="e.g., Repot every 2 years"
                required></textarea>
        </div>
        <div class="text-center">

            <button type="submit" class="btn bg-primary text-white">Submit</button>
        </div>
    </form>
</div>