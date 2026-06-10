<?php include 'header.php';
include '../db.php';
?>

<!-- Bootstrap Icons CDN -->

<div class="container mt-5 mb-5">
    <div class="card shadow-sm p-4 rounded-4">
        <h3 class="text-center mb-4">
            <i class="bi bi-bar-chart-line-fill text-primary"></i> Generated Report
        </h3>

        <!-- Payment Methods Section -->
        <div class="mb-5">
            <h5 class="text-secondary">
                <i class="bi bi-credit-card-2-front-fill me-2 text-info"></i>Payment Methods
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-info">
                        <tr>
                            <th>Order ID</th>
                            <th>userr</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT o.order_id, u.name, o.payment_option, o.payment_status 
                                                FROM orders o 
                                                JOIN users u ON o.user_id = u.user_id");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['payment_option']}</td>
                                    <td>{$row['payment_status']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- userr Orders Section -->
        <div class="mb-5">
            <h5 class="text-secondary">
                <i class="bi bi-receipt-cutoff me-2 text-warning"></i>User Orders
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-warning">
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Car</th>
                            <th>Order Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT o.order_id, u.name, ca.model, o.order_date, o.status 
                                                FROM orders o 
                                                JOIN users u ON o.user_id = u.user_id 
                                                JOIN cars ca ON o.car_id = ca.car_id");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['model']}</td>
                                    <td>{$row['order_date']}</td>
                                    <td>{$row['status']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Car Sales Section -->
        <div>
            <h5 class="text-secondary">
                <i class="bi bi-truck-front-fill me-2 text-success"></i>Car Sales Summary
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>Car Model</th>
                            <th>Total Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT ca.model, COUNT(*) AS total_sold 
                                                FROM orders o 
                                                JOIN cars ca ON o.car_id = ca.car_id 
                                                GROUP BY ca.model");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['model']}</td>
                                    <td>{$row['total_sold']}</td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


    </div>
</div>

<?php $conn->close(); ?>