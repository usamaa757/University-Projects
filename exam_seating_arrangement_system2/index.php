<?php include 'header.php'; ?>

<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Exam Seating Management</h1>
        <p class="lead text-muted">Organize and optimize student exam seating with ease</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Register Students</h5>
                    <p class="card-text">Add or import students into the system for exam assignment.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Assign Courses</h5>
                    <p class="card-text">Assign students to their respective courses before seating.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <h5 class="card-title">Generate Seating</h5>
                    <p class="card-text">Automatically generate a seating arrangement that avoids cheating.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 text-muted small">
        &copy; <?php echo date("Y"); ?> Exam Seating System | Built with PHP & Bootstrap 5
    </footer>
</div>