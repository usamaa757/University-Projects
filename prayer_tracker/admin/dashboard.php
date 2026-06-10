<?php
include 'header.php';

include '../db.php';
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$users = $conn->query("SELECT *  FROM users ORDER BY user_id DESC");

$user_count = $conn->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $user_count->fetch_assoc()['total_users'];

$user_count = $conn->query("SELECT 
        COUNT(CASE WHEN status = 'active' THEN 1 END) AS active_users,
        COUNT(CASE WHEN status = 'inactive' THEN 1 END) AS inactive_users,
        COUNT(*) AS total_users
    FROM users");
$result = $user_count->fetch_assoc();
$active_users = $result['active_users'];
$inactive_users = $result['inactive_users'];
$total_users = $result['total_users'];

$qaza_prayer = $conn->query("SELECT COUNT(*) AS qaza_prayer FROM prayer_records WHERE status = 'qaza'");
$qaza_prayer = $qaza_prayer->fetch_assoc()['qaza_prayer'];
?>

<!-- Admin Dashboard Content -->
<div class="container py-5">
    <h3 class="mb-4 text-center fw-bold">Welcome, <?= htmlspecialchars($adminName) ?> 👨‍💼</h3>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="bg-success text-white text-center p-4 stat-box shadow-sm">
                <h5>Total Users</h5>
                <h2><?= $total_users ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-warning text-dark text-center p-4 stat-box shadow-sm">
                <h5>Qaza Prayers</h5>
                <h2><?= $qaza_prayer ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-info text-white text-center p-4 stat-box shadow-sm">
                <h5>Active Users</h5>
                <h2><?= $active_users ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="bg-danger text-white text-center p-4 stat-box shadow-sm">
                <h5>Inactive Users</h5>
                <h2><?= $inactive_users ?></h2>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="mb-3">Overview</h5>
        <div class="bg-white p-4 shadow-sm rounded">
            <!-- Placeholder for future chart, table, or insights -->
            <p class="text-muted">This section can display overall trends, user activity charts, or system logs.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center bg-dark text-white py-3 mt-5">
    &copy; <?= date("Y") ?> Prayer Tracker Admin Panel
</footer>


</body>

</html>