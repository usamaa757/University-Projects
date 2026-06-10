<?php
include("navbar.php");
include("db.php");

// if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'trainer'){
//     header("Location: login.php");
//     exit();
// }

$trainer_id = $_SESSION['user_id'];
$msg = "";

// Fetch feedbacks for routines assigned to this trainer
$feedbacks = mysqli_query($conn, "
    SELECT f.id AS feedback_id, f.feedback_text, f.trainer_response, u.full_name, r.title
    FROM feedback f
    JOIN users u ON f.user_id=u.id
    JOIN workout_routines r ON f.routine_id=r.id
    WHERE r.trainer_id='$trainer_id'
    ORDER BY f.created_at DESC
");

// Handle trainer response submission
if(isset($_POST['submit_response'])){
    $feedback_id = $_POST['feedback_id'];
    $response = $_POST['trainer_response'];

    $sql = "UPDATE feedback SET trainer_response='$response' WHERE id='$feedback_id'";
    if(mysqli_query($conn, $sql)){
        $msg = "Response submitted successfully!";
    } else {
        $msg = "Error submitting response.";
    }
}
?>

<div class="activity-container">
    <h2>User Feedbacks</h2>

    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <div class="routine-cards">
        <?php while($row = mysqli_fetch_assoc($feedbacks)) { ?>
            <div class="routine-card">
                <p><strong>User:</strong> <?php echo $row['full_name']; ?></p>
                <p><strong>Routine:</strong> <?php echo $row['title']; ?></p>
                <p><strong>Feedback:</strong> <?php echo htmlspecialchars($row['feedback_text']); ?></p>

                <!-- Trainer Response -->
                <form method="POST">
                    <input type="hidden" name="feedback_id" value="<?php echo $row['feedback_id']; ?>">
                    <textarea name="trainer_response" placeholder="Respond to this feedback..."><?php echo htmlspecialchars($row['trainer_response']); ?></textarea>
                    <div class="text-center">
                        <button type="submit" name="submit_response">Submit Response</button>
                    </div>
                </form>

                <?php if($row['trainer_response'] != ""): ?>
                    <p><strong>Your Previous Response:</strong></p>
                    <p style="background:#f1f3f5; padding:8px; border-radius:6px;"><?php echo htmlspecialchars($row['trainer_response']); ?></p>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
</div>
