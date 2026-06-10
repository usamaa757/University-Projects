<?php
include 'header.php'; // Include header
include '../db_connection.php'; // Include database connection

$plant_id = $_GET['plant_id']; // Get plant_id from URL

// Default value for $plant_care and $plant_data to prevent undefined variable warning
$plant_care = null;
$plant_data = null;

// Query to fetch plant details from the plants table based on plant_id
$plant_query = "SELECT * FROM plants WHERE plant_id = '$plant_id'";
$plant_result = mysqli_query($conn, $plant_query);

// Fetch plant data if available
if ($plant_result && mysqli_num_rows($plant_result) > 0) {
    $plant_data = mysqli_fetch_assoc($plant_result); // Fetch plant details
} else {
    $error_message = "No plant found with the specified ID.";
}

// Query to fetch plant care details from the plant_care table
$care_query = "SELECT * FROM plant_care WHERE plant_id = '$plant_id'";
$care_result = mysqli_query($conn, $care_query);

// Fetch plant care data if available
if ($care_result && mysqli_num_rows($care_result) > 0) {
    $plant_care = mysqli_fetch_assoc($care_result); // Fetch plant care details
} else {
    $care_error_message = "No care information available for this plant.";
}
?>

<!-- Plant Details and Care Display -->
<div class="container mt-5 rounded border shadow p-3">
    <h3 class="text-center mb-4">Plant Details and Care Guidelines</h3>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php else: ?>
        <!-- Display Plant Information -->
        <div class="mb-3">
            <h4><?php echo htmlspecialchars($plant_data['plant_name']); ?></h4>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($plant_data['description']); ?></p>
            <p><strong>Plant Type:</strong> <?php echo htmlspecialchars($plant_data['plant_type']); ?></p>
            <?php if (!empty($plant_data['plant_image'])): ?>
                <img src="<?php echo htmlspecialchars($plant_data['plant_image']); ?>"
                    alt="<?php echo htmlspecialchars($plant_data['plant_name']); ?>" class="img-fluid" />
            <?php endif; ?>
        </div>

        <!-- Display Plant Care Information -->
        <?php if (isset($care_error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($care_error_message); ?></div>
        <?php elseif ($plant_care): ?>
            <div class="plant-care-details">
                <!-- Watering Frequency -->
                <div class="mb-3">
                    <h5>Watering Frequency:</h5>
                    <p class="border p-2 rounded"><?php echo htmlspecialchars($plant_care['watering_frequency']); ?></p>
                </div>

                <!-- Sunlight Needs -->
                <div class="mb-3">
                    <h5>Sunlight Needs:</h5>
                    <p class="border p-2 rounded"><?php echo htmlspecialchars($plant_care['sunlight_needs']); ?></p>
                </div>

                <!-- Temperature Range -->
                <div class="mb-3">
                    <h5>Temperature Range:</h5>
                    <p class="border p-2 rounded"><?php echo htmlspecialchars($plant_care['temperature_range']); ?></p>
                </div>

                <!-- Repotting Guidelines -->
                <div class="mb-3">
                    <h5>Repotting Guidelines:</h5>
                    <p class="border p-2 rounded"><?php echo nl2br(htmlspecialchars($plant_care['repotting_guidelines'])); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>