<?php
include '../connection.php';


$sql = "SELECT * FROM user WHERE status";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pending User Approvals</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        background-color: #f4f6f9;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #343a40;
        color: #fff;
    }

    .btn {
        border-radius: 20px;
        padding: 5px 15px;
    }

    h2 {
        font-weight: bold;
        color: #343a40;
    }
    </style>
</head>

<body>

    <?php include "../header/admin-header.php";  ?>
    <div class="container mt-5">
        <div class="card p-4">
            <h2 class="text-center mb-4">📋 Pending User Approvals</h2>

            <table class="table table-hover table-bordered text-center">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= ucfirst($row['name']) ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><span class="badge badge-info"><?= ucfirst($row['role']) ?></span></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <a href="approve_user.php?id=<?= $row['id'] ?>&action=approve"
                                class="btn btn-success btn-sm">✅ Approve</a>
                            <a href="approve_user.php?id=<?= $row['id'] ?>&action=reject"
                                class="btn btn-danger btn-sm">❌ Reject</a>
                        </td>
                    </tr>
                    <?php }
                    } else { ?>
                    <tr>
                        <td colspan="5" class="text-muted">No pending users found</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>