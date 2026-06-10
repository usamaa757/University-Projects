<?php
session_start();
include '../db_connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch the vote data and count votes per party
$query = "SELECT c.party, v.voter_id, v.name AS voter_name, c.name AS candidate_name, c.department
          FROM votes AS vt
          JOIN voter_registration AS v ON vt.voter_id = v.voter_id
          JOIN candidates AS c ON vt.candidate_id = c.candidate_id
          ORDER BY c.party, v.voter_id";
$result = $conn->query($query);

$votes = [];
$partyVoteCount = [];
while ($row = $result->fetch_assoc()) {
    $votes[$row['party']][] = $row;
    if (!isset($partyVoteCount[$row['party']])) {
        $partyVoteCount[$row['party']] = 0;
    }
    $partyVoteCount[$row['party']]++;
}

// Sort parties by vote count in descending order
arsort($partyVoteCount);

$conn->close();
require "header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter-Candidate Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Voter-Candidate Report</h2>
        <a href="vote_result_pdf.php" target="_blank"> <button class="btn btn-sm btn-success mb-2">Generate Report</button></a>
        <?php if (!empty($votes)) : ?>
            <?php foreach ($partyVoteCount as $party => $count) : ?>
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th colspan="2" class="text-center">
                                <?php echo htmlspecialchars($party); ?> (<?php echo $count; ?> votes) - Candidate: <?php echo htmlspecialchars($votes[$party][0]['candidate_name']); ?>
                            </th>
                            <th class="text-center">
                            Candidate: <?php echo htmlspecialchars($votes[$party][0]['candidate_name']); ?>
                            </th>
                        </tr>
                        </thead>
                        <tr>
                            <th>Voter ID</th>
                            <th>Voter Name</th>
                            <th>Department</th>
                        </tr>
                   
                    <tbody>
                        <?php foreach ($votes[$party] as $vote) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vote['voter_id']); ?></td>
                                <td><?php echo htmlspecialchars($vote['voter_name']); ?></td>
                                <td><?php echo htmlspecialchars($vote['department']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">No votes have been cast yet.</p>
        <?php endif; ?>
    </div>