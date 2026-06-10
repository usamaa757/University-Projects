<?php
session_start();
include '../db_connection.php';

// Check if the voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Fetch voter details
$stmt = $conn->prepare("SELECT * FROM voter_registration WHERE voter_id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$voter = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Check if the voter has already voted
$stmt = $conn->prepare("SELECT * FROM votes WHERE voter_id = ?");
$stmt->bind_param("s", $voter_id);
$stmt->execute();
$vote = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch the list of candidates
$candidates = [];
$result = $conn->query("SELECT * FROM candidates");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidates[] = $row;
    }
}

// Fetch selected candidate details if a candidate is selected
$selectedCandidate = null;
if (isset($_POST['candidate_id']) && !empty($_POST['candidate_id'])) {
    $candidate_id = $_POST['candidate_id'];
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE candidate_id = ?");
    $stmt->bind_param("s", $candidate_id);
    $stmt->execute();
    $selectedCandidate = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
require "header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title text-center mb-0">Voter Dashboard</h4>
                </div>
                <div class="card-body">
                    <?php if ($voter) : ?>
                        <h5 class="card-title">Welcome, <?php echo htmlspecialchars($voter['name']); ?></h5>
                        <p class="card-text">Voter ID: <?php echo htmlspecialchars($voter['voter_id']); ?></p>
                        <p class="card-text">Department: <?php echo htmlspecialchars($voter['department']); ?></p>

                        <?php if ($vote) : ?>
                            <div class="alert alert-success" role="alert">
                                You have already voted.
                            </div>
                        <?php else : ?>
                            <div class="alert alert-info" role="alert">
                                You have not voted yet. Please cast your vote below.
                            </div>
                            <form id="voteForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-check">
                                    <select style="width: 50%;" class="form-select p-2" name="candidate_id" onchange="this.form.submit()" required>
                                        <option value="" disabled selected>Select a candidate</option>
                                        <?php foreach ($candidates as $candidate) : ?>
                                            <option value="<?php echo $candidate['candidate_id']; ?>" <?php echo isset($candidate_id) && $candidate_id == $candidate['candidate_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($candidate['name']); ?> (<?php echo htmlspecialchars($candidate['department']); ?> - <?php echo htmlspecialchars($candidate['party']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>
                            <?php if ($selectedCandidate) : ?>
                                <div class="mt-3 p-3 border rounded bg-light" id="candidateDescription">
                                    <h5>Description:</h5>
                                    <p><?php echo htmlspecialchars($selectedCandidate['description']); ?></p>
                                    <form id="voteSubmitForm" action="cast_vote_process.php" method="post">
                                        <input type="hidden" name="candidate_id" value="<?php echo htmlspecialchars($selectedCandidate['candidate_id']); ?>">
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-dark mt-3">Submit Vote</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="alert alert-danger text-center" role="alert">
                            Voter details not found. Please contact the administrator.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
