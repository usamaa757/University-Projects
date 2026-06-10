<?php
include 'header.php';
?>


<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Event Management System</h1>
        <p class="lead">Plan. Manage. Connect. All in one place.</p>
    </div>

    <div class="row justify-content-center mb-4">
        <div class="col-md-4 d-grid mb-3">
            <a href="login.php" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login
            </a>
        </div>
        <div class="col-md-4 d-grid mb-3">
            <a href="register.php" class="btn btn-success btn-lg">
                <i class="bi bi-person-plus-fill me-2"></i> Register
            </a>
        </div>
    </div>

    <hr>

    <div class="row text-center mt-5">
        <div class="col-md-6">
            <h4><i class="bi bi-person-badge-fill text-primary"></i> Event Organizers</h4>
            <ul class="list-unstyled mt-3">
                <li>Create & manage events</li>
                <li>Edit/delete event details</li>
                <li>View RSVPs & attendance</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h4><i class="bi bi-people-fill text-success"></i> Attendees</h4>
            <ul class="list-unstyled mt-3">
                <li>Browse & RSVP to events</li>
                <li>Search by keyword/date</li>
                <li>Track your upcoming events</li>
            </ul>
        </div>
    </div>
</div>

</body>

</html>