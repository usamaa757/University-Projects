<?php
include 'header.php';
include 'db_connect.php';

// Validate seed ID
if (!isset($_GET['id'])) {
    header("Location: seeds.php");
    exit();
}

$id = intval($_GET['id']); // prevent SQL injection

$sql = "SELECT s.*, a.name AS agent_name, a.email AS agent_email 
        FROM seeds s
        JOIN agents a ON s.agent_id = a.id
        WHERE s.seed_id = $id";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<p> Seed not found.</p>";
    exit();
}

$seed = $result->fetch_assoc();
?>


<h2>🌱 Seed Details</h2>

<div class="seed-detail">
    <img src="uploads/<?php echo htmlspecialchars($seed['image']); ?>" width="250" height="200" alt="Seed Image">

    <h3><?php echo htmlspecialchars($seed['seed_name']); ?></h3>

    <?php if (!empty($seed['variety'])): ?>
    <p><strong>Variety:</strong> <?php echo htmlspecialchars($seed['variety']); ?></p>
    <?php endif; ?>

    <p><strong>Category:</strong> <?php echo htmlspecialchars($seed['category']); ?></p>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($seed['description'])); ?></p>
    <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($seed['price_per_kg']); ?> per kg</p>
    <p><strong>Quantity Available:</strong> <?php echo htmlspecialchars($seed['quantity_available']); ?> kg</p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($seed['status']); ?></p>
    <p><strong>Uploaded By:</strong> <?php echo htmlspecialchars($seed['agent_name']); ?>
        (<?php echo htmlspecialchars($seed['agent_email']); ?>)</p>
    <p><strong>Uploaded On:</strong> <?php echo htmlspecialchars($seed['upload_date']); ?></p>
    <?php
    if (isset($_SESSION['user_id'])) { ?>

    <a href="order_seed.php?id=<?php echo $seed['seed_id']; ?>" class="btn">Order This Seed</a>
    <a href="seeds.php" class="btn">Back to List</a>
    <?php

    } else {
        echo '<a href="login.php" class="btn">Need to login for order</a>';
    }
    ?>
</div>

</body>

</html>