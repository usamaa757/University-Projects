<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Record Management System </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                <li class="nav-item"><a class="nav-link" href="http://localhost/prms/index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/prms/auth/login.php"> Admin Login</a></li>
                <li class="nav-item"><a class="nav-link" href="http://localhost/prms/auth/user_login.php">User Login</a></li>
                <li> <a class = "nav-link" href="">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal -->
<div class="modal fade" id="AddUserModal" tabindex="-1" aria-labelledby="AddUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="AddUserModalLabel">Create guidline</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title"  required>
            </div>
            <div class="mb-3">
                
              <div class="mb-3">
                <label for="role" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="admin" <?php echo ($role == "admin") ? "selected" : ""; ?>>Protocol</option>
                    <option value="doctor" <?php echo ($role == "doctor") ? "selected" : ""; ?>>Guidline</option>
                    <option value="DataOperator" <?php echo ($role == "DataOperator") ? "selected" : ""; ?>Care Plan</option>
                </select>
            </div>
          
            <div class="mb-3">
                <label for="text" class="form-label">content</label>
                <input type="text" class="form-control" id="content" name="content">
            </div>
            <button type="submit" class="btn btn-<?php echo $update_mode ? 'warning' : 'primary'; ?>">
            
            </button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
    </div>
<!-- Sidebar -->
       <div class="sidebar" id="sidebar">
            <h3 class="text-center">Doctor Panel</h3>
            <a href="http://localhost/crud/admin/deshboard.php">Dashboard</a>
            <a href="manage_user.php">Manage User</a>
            <a href="view_patient.php"> View Patients</a>
            <a href="guidline.php">Guidline</a>
            <a href="careplan.php"> Care Plan</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
        <div style="width:85%" class="content">
            <h2>Clinical  Guidline</h2>
<!-- Table -->
    <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddUserModal">Add New Guidline</button>
        
            <table class="table table-bordered mt-3">
                <thead>
                    <tr class="table-danger">
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Assigned Patient</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <td> 1</td>
                        <td> 2</td>
                        <td>3 </td>
                        <td>5 </td>
                        <td> 
                            <div>
                                  <button type="button" class="btn btn-primary">
                                    <i class="bi bi-eye"></i>View</button>
                                <button type="button" class="btn btn-success">
                                    <i class="bi bi-pencil-square"></i>Edit</button>
                                <button type="button" class="btn btn-danger">
                                    <i class="bi bi-trash"></i>Delete</button>
                                  
                                </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
     <!-- ✅ Required for dropdown, modals, etc. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>