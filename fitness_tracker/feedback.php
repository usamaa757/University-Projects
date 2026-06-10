<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Fetch all workout routines
$routines = mysqli_query($conn, "SELECT * FROM workout_routines ORDER BY title ASC");

// Handle feedback submission (INSERT or UPDATE)
if(isset($_POST['submit_feedback'])){
    $routine_id = $_POST['routine_id'];
    $feedback_text = $_POST['feedback'];

    // Check if feedback already exists
    $check = mysqli_query($conn, "SELECT * FROM feedback WHERE user_id='$user_id' AND routine_id='$routine_id'");
    if(mysqli_num_rows($check) > 0){
        // Update existing feedback
        $sql = "UPDATE feedback SET feedback_text='$feedback_text', created_at=NOW() 
                WHERE user_id='$user_id' AND routine_id='$routine_id'";
    } else {
        // Insert new feedback
        $sql = "INSERT INTO feedback (user_id, routine_id, feedback_text, created_at) 
                VALUES ('$user_id', '$routine_id', '$feedback_text', NOW())";
    }

    if(mysqli_query($conn, $sql)){
        $msg = "Feedback submitted successfully!";
    } else {
        $msg = "Error submitting feedback.";
    }
}
?>

<div class="activity-container">
    <h2>Workout Routines</h2>

    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <div class="routine-cards">
        <?php while($row = mysqli_fetch_assoc($routines)){ 
            $routine_id = $row['id'];

            // Get existing feedback if any
            $feedback_res = mysqli_query($conn, "SELECT * FROM feedback WHERE user_id='$user_id' AND routine_id='$routine_id'");
            $existing_feedback = "";
            if(mysqli_num_rows($feedback_res) > 0){
                $fb_row = mysqli_fetch_assoc($feedback_res);
                $existing_feedback = $fb_row['feedback_text'];
            }

            // Get all feedback for this routine (optional)
            $all_feedback_res = mysqli_query($conn, "SELECT f.feedback_text, u.full_name 
                                                     FROM feedback f 
                                                     JOIN users u ON f.user_id=u.id 
                                                     WHERE f.routine_id='$routine_id'
                                                     ORDER BY f.created_at DESC");
        ?>
        <div class="routine-card">
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p><strong>Difficulty:</strong> <?php echo ucfirst($row['difficulty']); ?></p>

            <!-- Feedback Form -->
            <form method="POST">
                <input type="hidden" name="routine_id" value="<?php echo $routine_id; ?>">
                <textarea name="feedback" placeholder="Give your feedback..." required><?php echo htmlspecialchars($existing_feedback); ?></textarea>
                <div class="text-center">
                    <button type="submit" name="submit_feedback">Submit Feedback</button>
                </div>
            </form>


        </div>
        <?php } ?>
    </div>
</div>
