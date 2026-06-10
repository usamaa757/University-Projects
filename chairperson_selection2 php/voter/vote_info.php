<?php
session_start();
include '../db_connection.php';

// Check if the voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Check if the voter has cast their vote
$query = "SELECT * FROM votes WHERE voter_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$result = $stmt->get_result();
$vote_cast = false;
$candidate_info = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $vote_cast = true;
    $candidate_id = $row['candidate_id'];

    // Fetch candidate information
    $candidate_query = "SELECT * FROM candidates WHERE candidate_id = ?";
    $candidate_stmt = $conn->prepare($candidate_query);
    $candidate_stmt->bind_param("s", $candidate_id);
    $candidate_stmt->execute();
    $candidate_result = $candidate_stmt->get_result();
    $candidate_info = $candidate_result->fetch_assoc();
    $candidate_stmt->close();
}

$stmt->close();
$conn->close();
require "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Cast Info</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h4 class="card-title mb-0">Vote Cast Info</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Voter ID</th>
                                <td><?php echo htmlspecialchars($voter_id); ?></td>
                            </tr>
                            <tr>
                                <th>Vote Status</th>
                                <td><?php echo $vote_cast ? "Casted" : "Not Yet Voted"; ?></td>
                            </tr>
                            <?php if ($vote_cast && $candidate_info): ?>
                                <tr>
                                    <th colspan="2" class="text-center">Candidate Information</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo htmlspecialchars($candidate_info['name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td><?php echo htmlspecialchars($candidate_info['department']); ?></td>
                                </tr>
                                <tr>
                                    <th>Party</th>
                                    <td><?php echo htmlspecialchars($candidate_info['party']); ?></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <a href="voter_dashboard.php" class="btn btn-dark w-100">Cast Your Vote</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
