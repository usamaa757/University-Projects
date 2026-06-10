<?php
include '../db.php';
include 'header.php';

$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $property_id = $_POST['property_id'] ?? null;

    if (!$user_id || !is_numeric($property_id)) {
        $error = "Invalid request or not logged in.";
    } else {
        // Check if already rented
        $check = $conn->prepare("SELECT id FROM rentals WHERE user_id = ? AND property_id = ?");
        $check->bind_param("ii", $user_id, $property_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $error = "You already booked this property.";
        } else {
            $stmt = $conn->prepare("INSERT INTO rentals (user_id, property_id, rent_date) VALUES (?, ?, NOW())");
            $stmt->bind_param("ii", $user_id, $property_id);
            $stmt->execute();

            $success = "Rental Confirmed Successfully!<br>You will be contacted by the agent shortly.";
        }
    }
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "Invalid Property ID.";
} else {
    $property_id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT p.*, u.fullname AS agent_name, u.email AS agent_email 
                            FROM properties p 
                            JOIN users u ON p.agent_id = u.id 
                            WHERE p.id = ? AND p.listing_type = 'rent'");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "Property not found or not available for rent.";
    } else {
        $property = $result->fetch_assoc();
    }
}
?>

<div class="container">
    <div class="section-header">

        <h2>Rental Confirmation</h2>
    </div>

    <?php if ($error): ?>
    <div class="alert error"><?= $error ?></div>
    <?php else: ?>
    <div class="section">
        <h3><?= htmlspecialchars($property['title']) ?></h3>
        <p><strong>City:</strong> <?= htmlspecialchars($property['city']) ?></p>
        <p><strong>Price (Monthly):</strong> $ <?= number_format($property['price']) ?></p>
        <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($property['description'])) ?></p>
        <hr>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property['agent_name']) ?>
            (<?= htmlspecialchars($property['agent_email']) ?>)</p>

        <form action="rent_advance_payment.php" method="POST">
            <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
            <input type="hidden" name="price" value="<?= $property['price'] ?>">
            <div class="text-center">
                <button type="submit" class="btn">Confirm Rental</button>
            </div>
        </form>
    </div>
    <?php endif; ?>


</div>
<?php include '../footer.php'; ?>