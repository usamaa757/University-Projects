<?php
include '../db_connection.php';

// Fetch the list of voters
$sql = "SELECT * FROM voter_registration WHERE registration_status = 'approved'";
$result = $conn->query($sql);
require "header.php";
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Voter List</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Student ID</th>
                <th>Department</th>
                <th>Voter ID</th>
                <th>Password</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                $sno = 0;
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $sno = $sno + 1;
                        echo "<tr>
                            <td> $sno </td>
                            <td>{$row['name']}</td>
                            <td>{$row['gender']}</td>
                            <td>{$row['student_id']}</td>
                            <td>{$row['department']}</td>
                            <td>{$row['voter_id']}</td>
                            <td>{$row['plain_password']}</td>
                          </tr>";
                }
            } else {
               
            
                echo "<tr><td colspan='7' class='alert text-danger' role='alert'>No voter register</td></tr>";
            
          
        }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
?>
