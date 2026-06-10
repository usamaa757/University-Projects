<!-- dashboard.php -->
<?php

include('header.php');
?>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
                </div>
                <!-- <div class="row m-3">
                    <div class="col-md-6">
                        <a href="book_ambulance.php" class="btn btn-success btn-block">Book Ambulance</a>
                    </div>
                    <div class="col-md-6">
                        <a href="booking_status.php" class="btn btn-success btn-block">Current Booking Status</a>
                    </div>
                </div> -->
                <div class="card-body text-center">

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="book_ambulance.php" class="btn btn-primary btn-block">Book Ambulance</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="booking_status.php" class="btn btn-primary btn-block">Current Booking Status</a>
                        </div>
                        <!-- <div class="col-md-4 mb-2">
                            <a href="edit_ambulance.php" class="btn btn-primary btn-block">Edit Booking</a>

                        </div> -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>