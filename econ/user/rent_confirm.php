<?php
include '../db.php';
include 'header.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $user_id = $_SESSION['user_id'] ?? null;
    $property_id = $_GET['property_id'] ?? null;

    if (!$user_id || !is_numeric($property_id)) {
        $error = "Invalid request or not logged in.";
    } else {
        // Fetch property info (rent price)
        $stmt = $conn->prepare("SELECT price FROM properties WHERE id = ? AND listing_type = 'rent'");
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $property = $stmt->get_result()->fetch_assoc();

        if (!$property) {
            $error = "Property not available for rent.";
        } else {
            $monthly_rent = (int)$property['price'];
            $total_months = 12; // You can make this dynamic or ask user
            $paid_months = 0;
            $next_due_date = date('Y-m-d', strtotime('+1 month'));

            // Prevent duplicate rental
            $check = $conn->prepare("SELECT id FROM rentals WHERE user_id = ? AND property_id = ?");
            $check->bind_param("ii", $user_id, $property_id);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {
                $error = "You already rented this property.";
            } else {
                $insert = $conn->prepare("INSERT INTO rentals 
                    (user_id, property_id, monthly_rent, total_months, paid_months, next_due_date, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())");

                $insert->bind_param("iiiiis", $user_id, $property_id, $monthly_rent, $total_months, $paid_months, $next_due_date);
                if ($insert->execute()) {
                    // ✅ Update property status to 'Rent'
                    $updateStatus = $conn->prepare("UPDATE properties SET status = 'Rent' WHERE id = ?");
                    $updateStatus->bind_param("i", $property_id);
                    $updateStatus->execute();
                    $updateStatus->close();

                    $success = "Rental Confirmed Successfully!<br>You will be contacted by the agent shortly.";
                } else {
                    $error = "Failed to record rental. Please try again.";
                }
            }
        }
    }
} else {
    $error = "Invalid request method.";
}
?>

<div class="container">
    <div class="section-header">

        <h2>Rental Status</h2>
    </div>
    <?php if ($success): ?>
    <div class="alert success"><?= $success ?></div>
    <?php else: ?>
    <div class="alert error"><?= $error ?></div>
    <?php endif; ?>
</div>
<?php include '../footer.php'; ?>