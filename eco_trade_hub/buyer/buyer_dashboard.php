<?php

include("header.php");
include("../db_connection.php");

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['buyer_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as buyer first."));
    exit();
   
}

?>
    
<div class="container mt-5 ">
    <div class="border">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center heading-bg bg-dark text-white p-2">Buyer Dashboard</h2>
            </div>
        </div>

        <div class="row mt-4 p-2">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Request Part
                    </div>
                    <div class="card-body">
                        <p>Make request for part</p>
                        <a href="part_requests.php" class="btn btn-primary">Request</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        View Orders
                    </div>
                    <div class="card-body">
                        <p>View orders placed</p>
                        <a href="orders.php" class="btn btn-primary">Go to View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Account Information
                    </div>
                    <div class="card-body">
                        <p>Update your account information and settings.</p>
                        <a href="account_info.php" class="btn btn-primary">Go to Account Information</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>