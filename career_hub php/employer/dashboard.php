<?php
include 'header.php';
include "../db_connect.php";



$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();



?>


<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-dark text-white text-center">


            <h3> Dashboard</h3>

        </div>
        <div class="card-body">
            <h5>Your Role: <span class="badge bg-info"><?php echo ucfirst($user['role']); ?></span></h5>





            <hr>
            <h4 class="text-center"> Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h4>

        </div>
    </div>
</div>

</body>

</html>