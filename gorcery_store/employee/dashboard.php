<?php
include 'header.php';
?>
<!-- Dashboard Links -->
<div class="container mt-5 border roundeded shadow p-0">
    <h3 class="text-center bg-dark text-white p-1">Dashboard</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Add New Products</h5>
                    <p class="card-text">Add new products to the inventory.</p>
                    <a href="add_product.php" class="btn btn-light">Go to Add Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Manage Products</h5>
                    <p class="card-text">Update or delete product details.</p>
                    <a href="manage_products.php" class="btn btn-light">Go to Manage Products</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Daily Sales</h5>
                    <p class="card-text">Keep track of daily sales records.</p>
                    <a href="sales_records.php" class="btn btn-light">Go to Sales Records</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Store Expenses</h5>
                    <p class="card-text">Maintain records of store expenses.</p>
                    <a href="expenses.php" class="btn btn-light">Go to Expenses</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Customer Records</h5>
                    <p class="card-text">View and manage customer records.</p>
                    <a href="customer_records.php" class="btn btn-light">Go to Customer Records</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Discount Deals</h5>
                    <p class="card-text">Send discount deals to customers.</p>
                    <a href="discount_deals.php" class="btn btn-light">Go to Discount Deals</a>
                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>