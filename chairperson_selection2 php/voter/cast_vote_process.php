<?php
session_start();
include '../db_connection.php';

// Check if the voter is logged in


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voter_id = $_SESSION['voter_id'];
    $candidate_id = $_POST['candidate_id'];

    // Check if the voter has already voted
    $stmt = $conn->prepare("SELECT * FROM votes WHERE voter_id = ?");
    $stmt->bind_param("s", $voter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
        alert('You have already voted.');
        window.location.href = 'voter_dashboard.php'; // Redirect to dashboard or desired page
        </script>";
        exit();
    }

    // Record the vote
    $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $voter_id, $candidate_id);

    if ($stmt->execute()) {
        echo "<script>
        alert('Vote cast successfully.');
        window.location.href = 'voter_dashboard.php'; // Redirect to dashboard or desired page
        </script>";
    } else {
        echo "<script>
        alert('Failed to cast vote. Please try again.');
        window.location.href = 'cast_vote.php'; // Redirect back to voting page
        </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: cast_vote.php");
    exit();
}
?>
