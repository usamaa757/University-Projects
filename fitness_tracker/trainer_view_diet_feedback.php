<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "trainer"){
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['user_id'];
$msg = "";

// Fetch all diet plans created by this trainer
$diet_plans = mysqli_query($conn, 
    "SELECT dp.*, u.full_name AS trainer_name
     FROM diet_plans dp
     LEFT JOIN users u ON dp.trainer_id = u.id
     WHERE dp.trainer_id='$trainer_id'
     ORDER BY dp.id DESC");
?>

<div class="activity-container">
    <h2>Diet Plan Feedback</h2>

    <?php if($msg != "") { ?>
        <p class="message"><?php echo $msg; ?></p>
    <?php } ?>

    <?php if(mysqli_num_rows($diet_plans) == 0) { ?>
        <p>No diet plans created yet.</p>
    <?php } ?>

    <?php while($plan = mysqli_fetch_assoc($diet_plans)) { ?>

        <div class="routine-card">
            <h3><?php echo $plan['title']; ?></h3>
            <p><strong>Description:</strong> <?php echo $plan['description']; ?></p>

            <h4>Feedback from Users:</h4>

            <?php
            $diet_id = $plan['id'];

            $feedback_res = mysqli_query($conn, 
                "SELECT df.feedback_text, df.created_at, u.full_name 
                 FROM diet_feedback df
                 JOIN users u ON df.user_id = u.id
                 WHERE df.diet_id='$diet_id'
                 ORDER BY df.created_at DESC");

            if(mysqli_num_rows($feedback_res) == 0){
                echo "<p>No feedback yet.</p>";
            } else {
                while($fb = mysqli_fetch_assoc($feedback_res)){ ?>
                    <div class="feedback-box">
                        <strong><?php echo $fb['full_name']; ?>:</strong>
                        <p><?php echo htmlspecialchars($fb['feedback_text']); ?></p>
                        <small><?php echo $fb['created_at']; ?></small>
                    </div>
            <?php }
            }
            ?>

        </div>

    <?php } ?>

</div>