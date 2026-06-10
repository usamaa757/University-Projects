<?php

session_start();
if (!isset($_SESSION['doctor_id'])) {
    header('Location: ../login.php'); // Redirect if not logged in
    exit();
}
$doctor_name = $_SESSION['name'];

?>
<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Doctor's Portal</span>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo htmlspecialchars($doctor_name); ?></span>
            </li>

        </ul>
    </div>
</nav>