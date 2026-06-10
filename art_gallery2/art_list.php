<?php
include 'header.php';
include 'db.php';
// Fetch Artwork Data
$sql = "SELECT a.*, u.username as artist_name
        FROM art_items a
        JOIN users u ON a.seller_id = u.user_id WHERE a.status= 'approved'";
$result = $conn->query($sql);
?>


<!-- Hero Section -->
<div class="container mt-5 border rounded shadow">
    <div class="text-center">
        <h3>Art Work</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Art Name</th>
                <th>Artist Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <tr>
                    <td><?php echo $row['art_name']; ?></td>
                    <td><?php echo $row['artist_name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td>
                        <a href="art_details.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-success">View
                            Details</a>
                    </td>
                </tr>
        <?php
            }
        }
        // Close Database Connection
        $conn->close();
        ?>

    </table>
</div>
<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; 2025 Arts Gallery. All Rights Reserved.</p>
</footer>

</body>

</html>