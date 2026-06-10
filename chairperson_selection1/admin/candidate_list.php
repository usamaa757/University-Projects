<?php
include '../db_connection.php';

// Fetch the list of voters
$sql = "SELECT * FROM candidates WHERE status = 'Approved'";
$result = $conn->query($sql);
require "header.php";
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Candidates List</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Candidate ID</th>
                    <th>Department</th>
                    <th>Party Name</th>
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
                            <td>{$row['candidate_id']}</td>
                            <td>{$row['department']}</td>
                            <td>{$row['party']}</td>
                          </tr>";
                    }
                } else {


                    echo "<tr><td colspan='6' class='alert text-danger' role='alert'>No candidate register</td></tr>";

                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
?>