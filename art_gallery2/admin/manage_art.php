<?php include '../db.php';
include 'header.php';

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE art_items SET status='approved' WHERE art_id=$id");
} elseif (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->query("UPDATE art_items SET status='rejected' WHERE art_id=$id");
}


$result = $conn->query("SELECT * FROM art_items WHERE status='pending'");
?>


<div class="container mt-5 border rounded shadow">
    <h3 class="text-center">Pending Art Approvals</h3>
    <table class="table table-bordered mt-4">
        <thead class="table-dark">
            <tr>
                <th>Art Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM art_items WHERE status='pending'");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['art_name']; ?></td>
                <td><img src="<?php echo $base_url . 'seller/' . $row['image']; ?>" width="100"></td>
                <td>
                    <a href="?approve=<?php echo $row['art_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject=<?php echo $row['art_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
            <?php endwhile;

            if ($result->num_rows == 0) {
                echo "<tr><td colspan='3' class = 'text-center'> No art found</td></td>";
            } ?>
        </tbody>
    </table>
</div>