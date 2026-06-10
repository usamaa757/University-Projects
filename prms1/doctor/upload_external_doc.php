<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record Management System </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
     .navbar { background-color:rgb(8, 8, 8); }
        .navbar-brand, .nav-link { color: white !important; }
    .sidebar { width: 200px; height: 100vh; background: #343a40; color: white; padding-top: 20px; position: fixed; }
    .sidebar a { padding: 10px 20px; display: block; color: white; text-decoration: none; }
    .sidebar a:hover { background: #495057; }
    .content { margin-left: 200px; padding: 20px; width: 100%; }
    @media (max-width: 768px) {
        .sidebar { width: 0; overflow: hidden; }
        .content { margin-left: 0; }
    }
</style>
<body>
    <div>
        
<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">🏥 Patient Record Management System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
               <li class="nav-item"><a class="nav-link" href="http://localhost/prms/index.html">doctor</a></li>
            </ul>
        </div>
    </div>
</nav>

    </div>
    <div >

        <div class="sidebar" id="sidebar">
            <h3 class="text-center">Admin Panel</h3>
            <a href="http://localhost/crud/doctor.php/deshboard.php">Dashboard</a>
            <a href="manage_user.php">Manage User</a>
            <a href="dashboard.php">Guidline</a>
            <a href="patients.php"> View Patients</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
        <div style="width:85%" class="content">
           
                <h2 class="mb-4">Doctor Dashboard</h2>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5>View Assigned Patients</h5>
                            </div>
                     </div>
                </div>
                    <div class="col-md-6">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h5>Write Clinical Notes</h5>
                            </div>
                        </div>
                    </div>
         </div>

    </div>
</body>
</html>