<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record Management System </title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script></head>
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
            <h3 class="text-center">Doctor Panel</h3>
            <a href="http://localhost/crud/doctor.php/deshboard.php">Dashboard</a>
            <a href="manage_user.php">View Patient</a>
            <a href="dashboard.php">Add Clinical Notes</a>
            <a href="patients.php"> Add Madication</a>
            <a href="../auth/logout.php">View Problem</a>
            <a href="../auth/logout.php">Uplaod External Documents</a>
        </div>
         <div style="width:85%" class="content">
            <h2>Patient Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddUserModal">Add New Patient</button>
        
            <table class="table table-bordered mt-3">
                <thead>
                    <tr class="table-danger">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contect</th>
                        <th>Last Visit</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td> 1</td>
                        <td> 2</td>
                        <td>3 </td>
                        <td>4 </td>
                        <td>5 </td>
                        <td>6</td>
                        <td> 
                            <div>
                                <button type="button" class="btn btn-success">
                                    <i class="bi bi-pencil-square"></i></button>
                                <button type="button" class="btn btn-danger">
                                    <i class="bi bi-trash"></i></button>
                                </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
    </body>
</html>