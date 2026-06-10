<?php
require '../db.php';
include 'header.php';

if (!isset($_GET['rental_id'])) {
    die("Invalid request.");
}

$rental_id = (int)$_GET['rental_id'];

// Fetch rental info to get property_id
$query = $conn->prepare("SELECT property_id FROM rentals WHERE id = ?");
$query->bind_param("i", $rental_id);
$query->execute();
$result = $query->get_result();
$rental = $result->fetch_assoc();
$query->close();

if (!$rental) {
    die("Rental record not found.");
}

$property_id = $rental['property_id'];

//  Mark property as available
$updateProperty = $conn->prepare("UPDATE properties SET status = 'Available' WHERE id = ?");
$updateProperty->bind_param("i", $property_id);
$updateProperty->execute();
$updateProperty->close();

//  Option 1: Delete rental record
/*
$deleteRental = $conn->prepare("DELETE FROM rentals WHERE id = ?");
$deleteRental->bind_param("i", $rental_id);
$deleteRental->execute();
$deleteRental->close();
*/
//  Option 2 (RECOMMENDED): Mark rental as ended (requires a column like `status` in `rentals` table)

$endRental = $conn->prepare("UPDATE rentals SET status = 'terminated', terminated_at = NOW() WHERE id = ?");
$endRental->bind_param("i", $rental_id);
$endRental->execute();
$endRental->close();


?>
<div class="container">
    <div class="section">
        <div class="section-header">

            <h2>Rental Terminated Successfully</h2>
        </div>
        <p>The rental agreement has been terminated. The property is now available for others.</p>
        <div class="text-center">

            <a class="btn" href="terminated_rentals.php"> Back to Rent Overview</a>
        </div>
    </div>

</div>

<?php include '../footer.php'; ?>