<?php

include 'header.php';
?>


<style>
body {
    font-family: 'Cairo', sans-serif;
    background-image: url('ayse-bek-YLdYVzHopto-unsplash.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    min-height: 100vh;
    color: white;
}

.overlay {
    background-color: rgba(0, 0, 0, 0.65);
    min-height: 100vh;
}

.container {
    margin-top: 200px;
}
</style>


<div class="overlay d-flex align-items-center justify-content-center text-center ">
    <div class="container">
        <h1 class="display-4 fw-bold ">Welcome to <span class="text-success">Prayer Tracker</span></h1>
        <p class="lead mb-4">Keep track of your daily and missed (Qaza) prayers with ease and accountability.</p>

    </div>
</div>

<!-- Footer -->
<footer class="text-center text-white-50 py-3 footer">
    &copy; <?php echo date('Y'); ?> Prayer Tracker. All rights reserved.
</footer>

<!-- Bootstrap 5 JS Bundle (Optional for components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>