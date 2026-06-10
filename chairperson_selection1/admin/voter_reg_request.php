<?php
session_start();
include '../db_connection.php';

// Fetch all pending registrations
$result = $conn->query("SELECT id, name, gender, student_id, department, registration_status FROM voter_registration WHERE registration_status = 'pending'");

$conn->close();
include "header.php";
?>

    <div class="container mt-5">
        <h2 class="text-center">Registered Voter List</h2>
        <table class="table table-bordered mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Student ID</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if($result->num_rows>0) {?>
            
                <?php $sno =1; while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $sno++; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['gender']; ?></td>
                    <td><?php echo $row['student_id']; ?></td>
                    <td><?php echo $row['department']; ?></td>
                    <td><?php echo $row['registration_status']; ?></td>
                    <td>
                        <a href="voter_reg_request_process.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Accept</a>
                        <a href="voter_reg_request_process.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                    </td>
                </tr>
                <?php }
                }  else { ?>
                <td colspan="7" class="text-danger"><?php echo 'No record found'; }?></td>

            </tbody>
        </table>
    </div>
