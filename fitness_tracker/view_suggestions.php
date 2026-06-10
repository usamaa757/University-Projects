<?php
include("navbar.php");
include("db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "user"){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$suggestions = mysqli_query($conn,
    "SELECT ts.*, u.full_name AS trainer_name 
     FROM trainer_suggestions ts
     JOIN users u ON ts.trainer_id = u.id
     WHERE ts.user_id='$user_id'
     ORDER BY ts.created_at DESC");
?>

<div class="table-container">
    <h2>Your Trainer Suggestions</h2>

    <?php if(mysqli_num_rows($suggestions)==0){ ?>
        <p>No suggestions yet.</p>
    <?php } ?>

    <?php while($s = mysqli_fetch_assoc($suggestions)){ ?>
        <div class="suggestion-box">
            <p><strong>Trainer:</strong> <?php echo $s['trainer_name']; ?></p>
            <p><?php echo nl2br($s['suggestion']); ?></p>
            <small><?php echo $s['created_at']; ?></small>
        </div>
    <?php } ?>
</div>

