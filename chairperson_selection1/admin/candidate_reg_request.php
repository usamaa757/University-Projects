<?php
include '../db_connection.php';

$query = "SELECT * FROM candidates WHERE status = 'Pending'";
$result = $conn->query($query);
require "header.php";
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center"> Registered Candidate List</h2>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>
    
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Party</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result->num_rows>0) {?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['candidate_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                  
                    <td>
                        <a href="candidate_reg_request_process.php?action=approve&candidate_id=<?php echo $row['candidate_id']; ?>" class="btn btn-success btn-sm">Accept</a>
                        <a href="candidate_reg_request_process.php?action=reject&candidate_id=<?php echo $row['candidate_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this candidate?');">Reject</a>
                        </td>
                  
                </tr>
            <?php endwhile; ?>
            <?php 
                }  else { ?>
                <td colspan="7" class="text-danger"><?php echo 'No record found'; }?></td>
        </tbody>
    </table>
</div>
