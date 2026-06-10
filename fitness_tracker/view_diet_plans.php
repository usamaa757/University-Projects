<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Fetch all diet plans
$diets = mysqli_query($conn, "SELECT * FROM diet_plans ORDER BY title ASC");

// Handle feedback submission (INSERT or UPDATE)
if(isset($_POST['submit_feedback'])){
    $diet_id = $_POST['diet_id'];
    $feedback_text = $_POST['feedback'];

    // Check if feedback already exists
    $check = mysqli_query($conn, "SELECT * FROM diet_feedback WHERE user_id='$user_id' AND diet_id='$diet_id'");
    if(mysqli_num_rows($check) > 0){
        // Update existing feedback
        $sql = "UPDATE diet_feedback SET feedback_text='$feedback_text', created_at=NOW() 
                WHERE user_id='$user_id' AND diet_id='$diet_id'";
    } else {
        // Insert new feedback
        $sql = "INSERT INTO diet_feedback (user_id, diet_id, feedback_text, created_at) 
                VALUES ('$user_id', '$diet_id', '$feedback_text', NOW())";
    }

    if(mysqli_query($conn, $sql)){
        $msg = "Feedback submitted successfully!";
    } else {
        $msg = "Error submitting feedback.";
    }
}
?>

<div class="activity-container">
    <h2>Diet Plans</h2>

    <?php if($msg != "") { ?><p class="message"><?php echo $msg; ?></p><?php } ?>

    <div class="routine-cards">
        <?php while($row = mysqli_fetch_assoc($diets)){ 
            $diet_id = $row['id'];

            // Get existing feedback if any
            $feedback_res = mysqli_query($conn, "SELECT * FROM diet_feedback WHERE user_id='$user_id' AND diet_id='$diet_id'");
            $existing_feedback = "";
            if(mysqli_num_rows($feedback_res) > 0){
                $fb_row = mysqli_fetch_assoc($feedback_res);
                $existing_feedback = $fb_row['feedback_text'];
            }
        ?>
        <div class="routine-card">
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p><strong>Calories:</strong> <?php echo $row['calories']; ?> Need to burn</p>

            <!-- Feedback Form -->
            <form method="POST">
                <input type="hidden" name="diet_id" value="<?php echo $diet_id; ?>">
                <textarea name="feedback" placeholder="Give your feedback..." required><?php echo htmlspecialchars($existing_feedback); ?></textarea>
                <div class="text-center">
                    <button type="submit" name="submit_feedback">Submit Feedback</button>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</div>
