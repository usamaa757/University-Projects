<?php
include '../db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$agentId = $_SESSION['user_id'];
$rental_id = isset($_GET['rental_id']) ? (int)$_GET['rental_id'] : 0;

if ($rental_id <= 0) {
    die("Invalid rental ID");
}

// Fetch rental
$stmt = $conn->prepare("
    SELECT r.*, p.*
    FROM rentals r 
    JOIN properties p ON r.property_id = p.id 
    WHERE r.id = ? AND p.agent_id = ?
");
$stmt->bind_param("ii", $rental_id, $agentId);
$stmt->execute();
$result = $stmt->get_result();
$rental = $result->fetch_assoc();

if (!$rental) {
    die("Rental not found or access denied");
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $add_months = (int)$_POST['add_months'];
    $new_rent = (float)$_POST['monthly_rent'];

    if ($new_rent <= 0) {
        $error = "Please enter valid rent.";
    } else {
        $stmt = $conn->prepare("
            UPDATE rentals 
            SET total_months = total_months + ?, 
                monthly_rent = ?, 
                renewed_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param("idi", $add_months, $new_rent, $rental_id);

        if ($stmt->execute()) {
            $success = "Rental updated successfully!";
            // Refresh rental data
            $rental['total_months'] += $add_months;
            $rental['monthly_rent'] = $new_rent;
        } else {
            $error = "Failed to update rental.";
        }
    }
}
?>

<div class="container section">
    <div class="section-header">

        <h2>Extend Rental for: <?= htmlspecialchars($rental['title']) ?></h2>
    </div>

    <?php if ($success): ?>
    <div class="alert success"><?= $success ?></div>
    <?php elseif ($error): ?>
    <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Current Total Months: <strong><?= $rental['total_months'] ?></strong></label><br>
        <label>Paid Months: <strong><?= $rental['paid_months'] ?></strong></label><br>
        <label>Current Monthly Rent: <strong>$ <?= number_format($rental['monthly_rent']) ?></strong></label><br><br>

        <label>➕ Add More Months</label>
        <input type="number" name="add_months" required>

        <label>💸 New Monthly Rent ($)</label>
        <input type="number" name="monthly_rent" min="1" step="0.01" required value="<?= $rental['monthly_rent'] ?>">

        <br><br>
        <button type="submit" class="btn">Extend Rental</button>
    </form>
</div>

<?php include '../footer.php'; ?>