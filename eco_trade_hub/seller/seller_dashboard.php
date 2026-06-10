<?php
include("header.php");

include("../db_connection.php");
// Check if the user is logged in and is a seller
if (!isset($_SESSION['seller_id'])) {
    header("Location: ../login.php?msg=" . urlencode("Please log in as a seller to access the dashboard."));
    exit();
}

?>

<div class="container mt-3 col-10 ">
    <div class="border ">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center heading-bg bg-dark text-white p-2">Seller Dashboard</h2>
            </div>
        </div>

        <div class="row p-2">
            <div class="col-md-3">
                <div class="card">



                    <a href="manage_products.php" class="btn btn-primary">Go to Add Products</a>
                </div>

            </div>
            <div class="col-md-3">
                <div class="card">

                    <a href="view_orders.php" class="btn btn-primary">Go to View Orders</a>
                </div>

            </div>
            <div class="col-md-3">
                <div class="card">


                    <a href="display_reviews.php" class="btn btn-primary">Reviews & Feedback</a>
                </div>

            </div>
            <div class="col-md-3">
                <div class="card">


                    <a href="change_parts_detail.php" class="btn btn-primary">Edit Parts</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>

</html>