<?php
include '../db.php'; // Adjust path if needed
include 'header.php';
// Total property listings
$totalListings = $conn->query("SELECT COUNT(*) AS count FROM properties")->fetch_assoc()['count'];

// New users this month (excluding admin and agents)
$newUsers = $conn->query("
    SELECT COUNT(*) AS count 
    FROM users 
    WHERE role = 'user' 
      AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
      AND YEAR(created_at) = YEAR(CURRENT_DATE())
")->fetch_assoc()['count'];

// New agents this month
$newAgents = $conn->query("
    SELECT COUNT(*) AS count 
    FROM users 
    WHERE role = 'agent' 
      AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
      AND YEAR(created_at) = YEAR(CURRENT_DATE())
")->fetch_assoc()['count'];

// Recent inquiries this week
$recentInquiries = $conn->query("
    SELECT COUNT(*) AS count 
    FROM contact_messages 
    WHERE WEEK(submitted_at) = WEEK(CURRENT_DATE()) 
      AND YEAR(submitted_at) = YEAR(CURRENT_DATE())
")->fetch_assoc()['count'];

// Revenue (assumes 'commission' column exists in 'properties')
$revenue = $conn->query("
    SELECT SUM(total_payment * (markup_rate / 100)) AS total
    FROM purchases
    WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
      AND YEAR(created_at) = YEAR(CURRENT_DATE())
")->fetch_assoc()['total'] ?? 0;

?>

<!-- Overview Section -->
<div class="section-header">

    <h2>Overview Listing</h2>
</div>
<div class="overview-container">
    <div class="card">
        <div class="icon">📋</div>
        <h3>Total Listings</h3>
        <p><?= $totalListings ?> active property listings</p>
    </div>

    <div class="card">
        <div class="icon">🧑‍💼</div>
        <h3>New Users & Agents</h3>
        <p><?= $newUsers ?> users joined | <?= $newAgents ?> new agents</p>
    </div>

    <div class="card">
        <div class="icon">📨</div>
        <h3>Recent Inquiries</h3>
        <p><?= $recentInquiries ?> new property inquiries this week</p>
    </div>

    <div class="card">
        <div class="icon">💰</div>
        <h3>Total Revenue</h3>
        <p>$ <?= number_format($revenue) ?> generated this month</p>
    </div>
</div>
<?php include '../footer.php'; ?>

</body>

</html>