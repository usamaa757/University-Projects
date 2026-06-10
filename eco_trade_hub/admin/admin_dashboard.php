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
                <div class="card-header">
                    <h3 class="card-title">Admin Dashboard</h3>
                </div>
                <div class="card-body text-center">

                    <div class="row">
                        <div class="col-md-4 mb-2">
                        <a href="manage_products.php" class="btn btn-primary btn-block">Manage Product</a>
                        </div>
                        <div class="col-md-4 mb-2">
                        <a href="manage_users.php" class="btn btn-primary btn-block">Manage Users</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="users_request.php" class="btn btn-primary btn-block">User Requests</a>
                        </div>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>