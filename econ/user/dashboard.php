<?php
include 'header.php';
include '../db.php';

$userName = $_SESSION['fullname'];
$userId = $_SESSION['user_id'];

// Properties Purchased
$purchasedCount = $conn->query("SELECT COUNT(*) AS total FROM purchases WHERE user_id = $userId")->fetch_assoc()['total'] ?? 0;

// Payments Pending
$pendingCount = $conn->query("
    SELECT COUNT(*) AS pending 
    FROM purchases 
    WHERE user_id = $userId AND paid_installments < (period_years * 12)
")->fetch_assoc()['pending'] ?? 0;

// Total Installment Paid
$totalPaid = $conn->query("
    SELECT SUM(paid_installments * monthly_installment) AS total_paid 
    FROM purchases 
    WHERE user_id = $userId
")->fetch_assoc()['total_paid'] ?? 0;
// Rentals Count
$rentCount = $conn->query("
    SELECT COUNT(*) AS total 
    FROM rentals 
    WHERE user_id = $userId
")->fetch_assoc()['total'] ?? 0;

// Rentals Pending
$rentPending = $conn->query("
    SELECT COUNT(*) AS pending 
    FROM rentals 
    WHERE user_id = $userId AND paid_months < total_months
")->fetch_assoc()['pending'] ?? 0;

// Total Rent Paid
$totalRentPaid = $conn->query("
    SELECT SUM(paid_months * monthly_rent) AS total_paid 
    FROM rentals 
    WHERE user_id = $userId
")->fetch_assoc()['total_paid'] ?? 0;

?>

<!-- Main Content -->
<div class="section-header">
    <h2>Welcome, <?= htmlspecialchars($userName) ?> 👋</h2>
</div>
<main class="dashboard-main">


    <div class="card-grid">
        <div class="card">
            <div class="card-icon"><i class="fas fa-home"></i></div>
            <div class="card-title">Properties Purchased</div>
            <div class="card-desc"><?= $purchasedCount ?></div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="card-title">Payments Pending</div>
            <div class="card-desc"><?= $pendingCount ?></div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
            <div class="card-title">Total Installment Paid</div>
            <div class="card-desc">$ <?= number_format($totalPaid) ?></div>
        </div>
        <div class="card">
            <div class="card-icon"><i class="fas fa-key"></i></div>
            <div class="card-title">Properties Rented</div>
            <div class="card-desc"><?= $rentCount ?></div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-clock"></i></div>
            <div class="card-title">Rent Pending</div>
            <div class="card-desc"><?= $rentPending ?></div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="card-title">Total Rent Paid</div>
            <div class="card-desc">$ <?= number_format($totalRentPaid) ?></div>
        </div>

    </div>

    <div class="action-buttons" style="text-align: center; margin-top: 2rem;">
        <a href="purchase_list.php" class="btn">View My Properties</a>
        <a href="installments.php" class="btn">Pay Installment</a>
        <a href="rent_payment.php" class="btn">Pay Rental</a>
    </div>
</main>


<?php include '../footer.php'; ?>