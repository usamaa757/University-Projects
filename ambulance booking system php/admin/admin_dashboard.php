<!-- admin_dashboard.php -->
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include('header.php');
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h4>
                </div>
                <div class="row m-3">
                    <div class="col-md-6">
                        <a href="manage_booking.php" class="btn btn-success btn-block">Manage Booking Request</a>
                    </div>
                    <div class="col-md-6">
                        <a href="booking_status.php" class="btn btn-success btn-block">Manage Booking Status</a>
                    </div>
                </div>
                <div class="card-body text-center">

                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="manage_hospitals.php" class="btn btn-primary btn-block">Manage Hospitals</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="manage_doctors.php" class="btn btn-primary btn-block">Manage Doctors</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="manage_ambulances.php" class="btn btn-primary btn-block">Manage Ambulances</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="manage_drivers.php" class="btn btn-primary btn-block">Manage Drivers</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="assign_ambulance_driver.php" class="btn btn-primary btn-block">Assign Ambulance Driver</a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="manage_booking.php" class="btn btn-primary btn-block">Manage Booking</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Container -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body text-center">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="assign_ambulance_hospital.php" class="btn btn-secondary btn-block">Assign Ambulance to Hospitals</a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="ambulance_to_hospital_list.php" class="btn btn-secondary btn-block">Assigned Ambulance to Hospitals List</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="assign_ambulance_driver.php" class="btn btn-secondary btn-block">Assign Ambulance to Drivers</a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="ambluance_to_driver_list.php" class="btn btn-secondary btn-block">Assigned Ambulance to Driver List</a>
                        </div>
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