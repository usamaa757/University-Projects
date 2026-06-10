<?php
include("admin_header.php"); 

include("../db_connection.php");

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Admin ID required."));
    
    exit();
}

// Fetch admin name for greeting
$admin_name = $_SESSION['admin_name'];


?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="card-title">Manage Users</h3>
                </div>
                <div class="card-body text-center">
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <a href="manage_buyers.php" class="btn btn-primary btn-block">Buyers</a>
                        </div>
                        <div class="col-md-4">
                            <a href="manage_sellers.php" class="btn btn-primary btn-block">Sellers</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>

</html>