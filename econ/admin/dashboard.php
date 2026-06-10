<?php
include 'header.php';
?>

<!-- Main Content -->
<main class="dashboard-main">
    <div class="section-header">
        <h2>Welcome to the Admin Dashboard</h2>
    </div>

    <div class="card-grid">
        <div class="card">
            <a href="overview.php">
                <div class="card-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="card-title">Overview Listing</div>
            </a>
            <div class="card-desc">Quick view of system data</div>
        </div>

        <div class="card">
            <a href="listings.php">
                <div class="card-icon"><i class="fas fa-home"></i></div>
                <div class="card-title">Property Listing</div>
            </a>
            <div class="card-desc">Manage all property details</div>
        </div>



        <div class="card">
            <a href="manage_agents.php">
                <div class="card-icon"><i class="fas fa-user-tie"></i></div>
                <div class="card-title">Agent Management</div>
            </a>
            <div class="card-desc">Manage real estate agents</div>
        </div>

        <div class="card">
            <a href="report.php">
                <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                <div class="card-title">Report Analysis</div>
            </a>
            <div class="card-desc">View insights and trends</div>
        </div>


        <div class="card">
            <a href="user_query.php">
                <div class="card-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="card-title">User Queries</div>
            </a>
            <div class="card-desc">View user suggestions</div>
        </div>

        <div class="card">
            <a href="calculator.php">
                <div class="card-icon"><i class="fas fa-calculator"></i></div>
                <div class="card-title">Calculator</div>
            </a>
            <div class="card-desc">Calculate Installment</div>
        </div>
    </div>
</main>
<?php include '../footer.php'; ?>


</body>

</html>