<?php
include 'header.php';
include 'db_connect.php';

// Fetch statistics
$totalSeeds = $conn->query("SELECT COUNT(*) AS count FROM seeds")->fetch_assoc()['count'];
$totalAgents = $conn->query("SELECT COUNT(*) AS count FROM agents")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];

// Example data for chart
$salesData = $conn->query("SELECT MONTH(order_date) AS month, SUM(total_price) AS total FROM orders GROUP BY MONTH(order_date)");
$months = [];
$totals = [];
while ($row = $salesData->fetch_assoc()) {
    $months[] = date("M", mktime(0, 0, 0, $row['month'], 1));
    $totals[] = $row['total'];
}
?>

<section class="stats">
    <div class="card">
        <h2><?php echo $totalSeeds; ?></h2>
        <p>Total Seeds</p>
    </div>
    <div class="card">
        <h2><?php echo $totalAgents; ?></h2>
        <p>Registered Agents</p>
    </div>
    <div class="card">
        <h2><?php echo $totalOrders; ?></h2>
        <p>Total Orders</p>
    </div>
    <div class="card">
        <h2><?php echo $totalUsers; ?></h2>
        <p>Registered Users</p>
    </div>
</section>

<section class="chart-section">
    <h2>Monthly Sales Report</h2>
    <canvas id="salesChart"></canvas>
</section>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Total Sales (PKR)',
            data: <?php echo json_encode($totals); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
</body>

</html>