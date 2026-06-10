<?php

include 'db.php';
include 'header.php';
if (!isset($_SESSION['role']) == 'seller') {
    header("Location: login.php");
}

$seller_id = $_SESSION['user_id'];

$query = "SELECT * FROM art_items WHERE seller_id = '$seller_id'";
$result = $conn->query($query);
?>

<div class="container mt-5">
    <h2 class="text-center">My Art Items</h2>
    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>Art Name</th>
                <th>Image</th>
                <th>Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><img src="uploads/<?php echo $row['image']; ?>" width="100"></td>
                <td><?php echo $row['description']; ?></td>
                <td>$<?php echo $row['price']; ?></td>
                <td>
                    <?php
                        if ($row['status'] == 'approved') {
                            echo "<span class='badge bg-success'>Approved</span>";
                        } elseif ($row['status'] == 'rejected') {
                            echo "<span class='badge bg-danger'>Rejected</span>";
                        } else {
                            echo "<span class='badge bg-warning text-dark'>Pending</span>";
                        }
                        ?>
                </td>
                <td>
                    <a href="edit_art.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="delete_art.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>