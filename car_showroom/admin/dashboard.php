<?php include 'header.php';


include '../db.php';

// Total Cars
$total_cars = $conn->query("SELECT COUNT(*) as count FROM cars")->fetch_assoc()['count'];

// Total Orders
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];

// Pending Payments
$pending_payments = $conn->query("SELECT COUNT(*) as count FROM orders WHERE payment_status = 'partial'")->fetch_assoc()['count'];

// Paid Payments
$paid_payments = $conn->query("SELECT COUNT(*) as count FROM orders WHERE payment_status = 'paid'")->fetch_assoc()['count'];

// Cancelled Orders
$cancelled_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'cancelled'")->fetch_assoc()['count'];
?>

<div class="container mt-5 card rounded-3 border-0 shadow p-3">
    <h3 class="mb-4 text-center">Admin Dashboard</h3> <!-- Smaller heading -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-3">
        <!-- Reduced gap g-4 to g-3 -->

        <!-- Total Cars -->
        <div class="col mb-2">
            <div class="card shadow-sm p-2">
                <h6 class="card-title text-center text-success mb-2">Total Cars</h6>
                <p class="card-text text-center fs-5"><?= $total_cars ?></p>
            </div>
        </div>

        <!-- Orders -->
        <div class="col mb-2">
            <div class="card shadow-sm p-2">
                <h6 class="card-title text-center text-info mb-2">Orders</h6>
                <p class="card-text text-center fs-5"><?= $total_orders ?></p>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="col mb-2">
            <div class="card shadow-sm p-2">
                <h6 class="card-title text-center text-warning mb-2">Pending Payments</h6>
                <p class="card-text text-center fs-5"><?= $pending_payments ?></p>
            </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="col mb-2">
            <div class="card shadow-sm p-2">
                <h6 class="card-title text-center text-danger mb-2">Cancelled Orders</h6>
                <p class="card-text text-center fs-5"><?= $cancelled_orders ?></p>
            </div>
        </div>

        <!-- Paid Orders -->
        <div class="col mb-2">
            <div class="card shadow-sm p-2">
                <h6 class="card-title text-center text-primary mb-2">Paid Orders</h6>
                <p class="card-text text-center fs-5"><?= $paid_payments ?></p>
            </div>
        </div>

    </div>
    <!-- Report Button -->
    <div class="text-center mt-4">
        <a href="reports.php" class="btn px-4 py-2">
            <i class="bi bi-file-earmark-bar-graph"></i> Reports
        </a>
    </div>

</div>