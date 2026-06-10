<?php
include("header.php");
include("../db_connection.php");
?>

<div class="container mt-5">
    <div class="border">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center heading-bg bg-dark text-white p-2">User Dashboard</h3>
            </div>
        </div>

        <div class="row mt-4 p-2">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Rate & Reveiws
                    </div>
                    <div class="card-body">
                        <p>Users Review on your books</p>
                        <a href="display_review.php" class="btn btn-primary">Display Reviews</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Requested History
                    </div>
                    <div class="card-body">
                        <p>View your past requests or orders.</p>
                        <a href="request_book_status.php" class="btn btn-primary">View History</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Manage Account
                    </div>
                    <div class="card-body">
                        <p>Update your account information and preferences.</p>
                        <a href="user_profile.php" class="btn btn-primary">Manage Account</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Add Query
                    </div>
                    <div class="card-body">
                        <p>Add your query.</p>
                        <a href="add_query.php" class="btn btn-primary">Add</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>