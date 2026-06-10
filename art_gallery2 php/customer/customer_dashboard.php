<?php
include 'header.php';
include "../db.php";


?>



<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">


            <h3> Dashboard</h3>

        </div>
        <div class="card-body">
            <h5>Your Role: <span class="badge bg-info"><?php echo $_SESSION['role']; ?></span></h5>





            <hr>
            <h4 class="text-center"> Welcome, <?php echo $_SESSION['username']; ?>!</h4>

        </div>
    </div>
</div>


</body>

</html>