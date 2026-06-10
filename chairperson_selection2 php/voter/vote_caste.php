<?php
session_start();
include '../db_connection.php';

// Check if the voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

// Fetch the list of candidates
$candidates = [];
$result = $conn->query("SELECT * FROM candidates");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}
require "header.php";
$conn->close();
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title text-center mb-0">Cast Your Vote</h4>
                </div>
                <div class="card-body">
                    <form id="voteForm" action="cast_vote_process.php" method="post">
                        <?php foreach ($candidates as $candidate) : ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="candidate_id" id="candidate_id<?php echo $candidate['candidate_id']; ?>" value="<?php echo $candidate['candidate_id']; ?>" required>
                                <label class="form-check-label" for="candidate<?php echo $candidate['candidate_id']; ?>">
                                    <?php echo $candidate['name']; ?> (<?php echo $candidate['party']; ?>, <?php echo $candidate['department']; ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-dark mt-3">Submit Vote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>