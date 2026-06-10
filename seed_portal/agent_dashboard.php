<?php
include 'db_connect.php';
include 'header.php';

// Redirect if not logged in as agent
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

$agent_id = $_SESSION['agent_id'];

// ---- Fetch Quick Stats ---- //
$totalSeeds = $conn->query("SELECT COUNT(*) AS c FROM seeds WHERE agent_id='$agent_id'")->fetch_assoc()['c'];
$totalOrders = $conn->query("
    SELECT COUNT(*) AS c 
    FROM orders o 
    JOIN seeds s ON o.seed_id = s.seed_id 
    WHERE s.agent_id='$agent_id'
")->fetch_assoc()['c'];
$totalRevenue = $conn->query("
    SELECT IFNULL(SUM(o.total_price),0) AS total 
    FROM orders o 
    JOIN seeds s ON o.seed_id = s.seed_id 
    WHERE s.agent_id='$agent_id' AND o.status='Paid'
")->fetch_assoc()['total'];

// ---- Sales Chart Data ---- //
$salesData = $conn->query("
    SELECT MONTH(o.order_date) AS month, SUM(o.total_price) AS total
    FROM orders o 
    JOIN seeds s ON o.seed_id = s.seed_id 
    WHERE s.agent_id='$agent_id'
    GROUP BY MONTH(o.order_date)
    ORDER BY MONTH(o.order_date)
");

$months = [];
$totals = [];
while ($row = $salesData->fetch_assoc()) {
    $months[] = date("M", mktime(0, 0, 0, $row['month'], 1));
    $totals[] = $row['total'];
}


?>


<div class="container" style="width: 90%;">

    <!-- 🌿 Quick Stats -->
    <div class="stats">
        <div class="card">
            <h2><?php echo $totalSeeds; ?></h2>
            <p>Seeds Uploaded</p>
        </div>
        <div class="card">
            <h2><?php echo $totalOrders; ?></h2>
            <p>Orders Received</p>
        </div>
        <div class="card">
            <h2>Rs. <?php echo number_format($totalRevenue, 0); ?></h2>
            <p>Total Sales</p>
        </div>
    </div>

    <!-- 🌾 Uploaded Seeds -->
    <h3 class="section-title">🌾 Your Uploaded Seeds</h3>
    <?php
    $seeds = $conn->query("SELECT * FROM seeds WHERE agent_id='$agent_id'");
    if ($seeds->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Seed Name</th>
                    <th>Category</th>
                    <th>Variety</th>
                    <th>Price (PKR/kg)</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>";
        while ($row = $seeds->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['seed_name']}</td>
                    <td>{$row['category']}</td>
                    <td>{$row['variety']}</td>
                    <td>{$row['price_per_kg']}</td>
                    <td>{$row['quantity_available']} kg</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No seeds uploaded yet. <a href='upload_seed.php' class='btn'>Upload Now</a></p>";
    }
    ?>

    <!-- 🧾 Recent Orders -->
    <h3 class="section-title">🧾 Recent Orders for Your Seeds</h3>
    <?php
    $orders = $conn->query("
        SELECT o.*, s.seed_name 
        FROM orders o 
        JOIN seeds s ON o.seed_id = s.seed_id 
        WHERE s.agent_id='$agent_id' 
        ORDER BY o.order_date DESC 
        LIMIT 10
    ");

    if ($orders->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Order ID</th>
                    <th>Seed</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>";
        while ($row = $orders->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['seed_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>Rs. {$row['total_price']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['order_date']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recent orders yet.</p>";
    }
    ?>

    <!-- 📈 Sales Chart -->
    <div class="chart-container">
        <h3 class="section-title">📈 Monthly Sales Overview</h3>
        <canvas id="salesChart" height="100"></canvas>
    </div>

</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Monthly Revenue (PKR)',
            data: <?php echo json_encode($totals); ?>,
            fill: true,
            borderColor: '#2e8b57',
            backgroundColor: 'rgba(46,139,87,0.2)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
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