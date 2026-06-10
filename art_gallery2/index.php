<?php
include 'db.php';
include 'header.php';
$sql = "SELECT a.*, u.username as artist_name
        FROM art_items a
        JOIN users u ON a.seller_id = u.user_id WHERE a.status= 'approved' LIMIT 3";
$result = $conn->query($sql);
?>

<!-- Hero Section -->
<div class="container mt-4">
    <div class="jumbotron text-center bg-light p-5 rounded shadow">
        <h1 class="display-4">Welcome to the Arts Gallery</h1>
        <p class="lead">Discover and purchase incredible artwork from talented artists worldwide.</p>
        <a href="art_list.php" class="btn btn-outline-primary btn-lg">Explore Gallery</a>
    </div>
</div>

<!-- Featured Artwork -->
<div class="container mt-5 border rounded shadow">
    <h2 class="text-center">Featured Artwork</h2>

    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src=" <?php echo $base_url . 'seller/' . htmlspecialchars($row['image']); ?>" class="card-img-top"
                            alt="Artwork" width="400" height="400">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['art_name']; ?></h5>
                            <p class="card-text"><?php echo $row['artist_name']; ?></p>
                            <p class="card-text">$<?php echo $row['price']; ?></p>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        // Close Database Connection
        $conn->close();
        ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Arts Gallery. All Rights Reserved.</p>
</footer>

</body>

</html>