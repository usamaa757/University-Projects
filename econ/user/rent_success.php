<?php
require '../db.php';
include 'header.php';

if (!isset($_GET['rental_id'])) {
    die("Invalid access.");
}

$rental_id = $_GET['rental_id'];

// Fetch rental info to get property_id first
$query = $conn->prepare("
    SELECT r.*, u.fullname, p.title, p.id AS property_id 
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN properties p ON r.property_id = p.id
    WHERE r.id = ?
");
$query->bind_param("i", $rental_id);
$query->execute();
$result = $query->get_result();
$rental = $result->fetch_assoc();
$query->close();

if (!$rental) {
    die("Rental not found.");
}

$property_id = $rental['property_id'];

// ✅ Update rental: increment paid_months and next_due_date
$update = $conn->prepare("UPDATE rentals 
    SET paid_months = paid_months + 1,
        next_due_date = DATE_ADD(IFNULL(next_due_date, NOW()), INTERVAL 1 MONTH)
    WHERE id = ?
");
$update->bind_param("i", $rental_id);
$update->execute();
$update->close();

// ✅ Update property status to 'Rent'
$property_update = $conn->prepare("UPDATE properties 
    SET status = 'Rent' 
    WHERE id = ?
");
$property_update->bind_param("i", $property_id);
$property_update->execute();
$property_update->close();

// ✅ Re-fetch rental info after update
$query = $conn->prepare("
    SELECT r.*, u.fullname, p.title 
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN properties p ON r.property_id = p.id
    WHERE r.id = ?
");
$query->bind_param("i", $rental_id);
$query->execute();
$result = $query->get_result();
$rental = $result->fetch_assoc();
$query->close();
?>

<div class="container">
    <div class="section">
        <div class="section-header">
            <h2>Rent Payment Successful!</h2>
        </div>

        <p><strong>User:</strong> <?= htmlspecialchars($rental['fullname']) ?></p>
        <p><strong>Property:</strong> <?= htmlspecialchars($rental['title']) ?></p>
        <p><strong>Monthly Rent:</strong> $ <?= number_format($rental['monthly_rent']) ?></p>
        <p><strong>Paid Months:</strong> <?= $rental['paid_months'] ?> / <?= $rental['total_months'] ?></p>
        <p><strong>Next Due Date:</strong> <?= date("F j, Y", strtotime($rental['next_due_date'])) ?></p>

        <?php if ($rental['paid_months'] >= $rental['total_months']): ?>
            <p class="alert success"><strong> All payments completed for this rental.</strong></p>
        <?php endif; ?>

        <br>
        <div class="text-center">
            <a class="btn" href="rent_payment.php"> Back to Rent Overview</a>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>