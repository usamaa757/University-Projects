<?php
include 'db_connection.php';

// Query to count the total number of votes each candidate has received
$query = "
    SELECT c.candidate_id, c.name, c.gender, c.department, c.party, COUNT(v.vote_id) as total_votes
    FROM candidates c
    LEFT JOIN votes v ON c.candidate_id = v.candidate_id
    GROUP BY c.candidate_id, c.name, c.department, c.party
    ORDER BY total_votes DESC
";

$result = $conn->query($query);
require "header.php";
?>

<div class="container mt-5">
    <h2 class="mb-4 text-center">Candidate Vote Counts</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Department</th>
                <th>Party</th>
                <th>Total Votes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['candidate_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['department']); ?></td>
                    <td><?php echo htmlspecialchars($row['party']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_votes']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
