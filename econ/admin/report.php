<?php
include '../db.php';
include 'header.php';

// Income
$purchaseIncomeQuery = $conn->query("SELECT SUM(monthly_installment * paid_installments) AS purchase_income FROM purchases");
$purchaseIncome = $purchaseIncomeQuery->fetch_assoc()['purchase_income'] ?? 0;

$rentIncomeQuery = $conn->query("SELECT SUM(monthly_rent * paid_months) AS rent_income FROM rentals");
$rentIncome = $rentIncomeQuery->fetch_assoc()['rent_income'] ?? 0;

// Property counts
$soldQuery = $conn->query("SELECT COUNT(*) AS sold_count FROM properties WHERE LOWER(status) = 'sold'");
$sold = $soldQuery->fetch_assoc()['sold_count'] ?? 0;

$rentQuery = $conn->query("SELECT COUNT(*) AS rent_count FROM properties WHERE LOWER(status) = 'rent'");
$rent = $rentQuery->fetch_assoc()['rent_count'] ?? 0;
?>
<div class="container">

    <div class="section">

        <div style="max-width: 900px; margin: 50px auto; text-align: center;">
            <h2>Property & Income Report</h2>
            <canvas id="combinedChart"></canvas>
        </div>

    </div>
</div>

<script>
const ctx = document.getElementById('combinedChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Sold', 'Rent', 'Purchase Income', 'Rental Income'],
        datasets: [{
                label: 'Property Count',
                data: [<?= $sold ?>, <?= $rent ?>, null, null],
                backgroundColor: ['#6c5ce7', '#00b894', null, null],
                yAxisID: 'yCount'
            },
            {
                label: 'Income ($)',
                data: [null, null, <?= $purchaseIncome ?>, <?= $rentIncome ?>],
                backgroundColor: [null, null, '#fdcb6e', '#d63031'],
                yAxisID: 'yIncome'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            yCount: {
                type: 'linear',
                position: 'left',
                beginAtZero: true,
                suggestedMax: <?= max($sold, $rent) + 9 ?>,
                ticks: {
                    stepSize: 1,
                    precision: 0,
                    callback: value => value.toLocaleString()
                },
                title: {
                    display: true,
                    text: 'Property Count'
                }
            },
            yIncome: {
                type: 'linear',
                position: 'right',
                beginAtZero: true,
                suggestedMax: <?= max($purchaseIncome, $rentIncome) * 6 ?>,
                ticks: {
                    callback: value => '$' + value.toLocaleString()
                },
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'Income ($)'
                }
            }
        },
        plugins: {
            tooltip: {
                intersect: true,
                mode: 'nearest',
                callbacks: {
                    label: function(context) {
                        const label = context.dataset.label;
                        const value = context.parsed.y;
                        return label === 'Income ($)' ?
                            `${label}: $${value.toLocaleString()}` :
                            `${label}: ${value}`;
                    }
                }
            },
            legend: {
                position: 'bottom'
            }
        }
    }

});
</script>


<?php include '../footer.php'; ?>