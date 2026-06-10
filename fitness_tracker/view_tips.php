<?php
include("navbar.php");
include("db.php");



$msg = "";

// Fetch all workout tips
$tips = mysqli_query($conn, "SELECT * FROM fitness_tips ORDER BY title ASC");

?>

<div class="activity-container">
    <h2>Workout Tips</h2>


    <div class="routine-cards">
        <?php while($row = mysqli_fetch_assoc($tips)){ 
    
        ?>
        <div class="routine-card">
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p><strong>Created at:</strong> <?php echo ucfirst($row['created_at']); ?></p>

        
    

        </div>
        <?php } ?>
    </div>
</div>

