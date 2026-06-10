<?php

session_start();
if (!isset($_SESSION['patient_id'])) {
    header('Location: ../login.php');
    exit();
}
$patient_name = $_SESSION['name'];

?>
<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Patient's Portal</span>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <span class="nav-link">Welcome, <?php echo htmlspecialchars($patient_name); ?></span>
            </li>

        </ul>
    </div>
</nav>