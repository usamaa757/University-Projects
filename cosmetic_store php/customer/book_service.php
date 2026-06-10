<?php
include 'header.php';
include '../db.php';

$service_id = $_GET['service_id'] ?? null;
if (!$service_id) {
    echo "Service not found.";
    exit;
}

// Fetch service details
$query = "SELECT * FROM services WHERE service_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $service_id);
mysqli_stmt_execute($stmt);
$service_result = mysqli_stmt_get_result($stmt);
$service = mysqli_fetch_assoc($service_result);

if (!$service) {
    echo "Service not found.";
    exit;
}
?>

<!-- Booking Form -->
<div class="dashboard-content">
    <div class="dashboard-header">
        <h2>Book Service: <?= htmlspecialchars($service['service_name']) ?></h2>
    </div>

    <form method="POST" action="submit_booking.php" class="form">
        <input type="hidden" name="service_id" value="<?= $service_id ?>">

        <label for="booking_date">Select Date</label>
        <input type="date" name="booking_date" id="booking_date" required>

        <label for="booking_time">Select Time</label>
        <input type="time" name="booking_time" id="booking_time" required>

        <label for="special_requests">Special Requests</label>
        <textarea name="special_requests" id="special_requests" placeholder="Any special requests?"></textarea>

        <button type="submit" class="btn">Confirm Booking</button>
    </form>
</div>

</body>

</html>