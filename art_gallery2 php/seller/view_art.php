<?php

include '../db.php';
include 'header.php';

$seller_id = $_SESSION['user_id'];

$query = "SELECT * FROM art_items WHERE seller_id = '$seller_id'";
$result = $conn->query($query);
?>

<div class="container mt-5 border rounded shadow">
    <h3 class="text-center">My Art Items</h3>
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
                <td><?php echo $row['art_name']; ?></td>
                <td><img src="<?php echo $row['image']; ?>" width="100"></td>
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
                    <a href="edit_art.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="delete_art.php?art_id=<?php echo $row['art_id']; ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure?');">Delete</a>
                    <?php if ($row['status'] == 'approved') { ?>

                    <a href="reviews.php?art_id=<?php echo $row['art_id']; ?>"
                        class="btn btn-primary btn-sm">Reviews</a>
                    <?php } else { ?>
                    <a href="" class="btn btn-primary btn-sm disabled">Reviews</a>
                    <?php  } ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php
            if ($result->num_rows == 0) {
                echo "<tr><td colspan='6' class = 'text-center'> No art found</td></td>";
            } ?>
        </tbody>
    </table>
</div>