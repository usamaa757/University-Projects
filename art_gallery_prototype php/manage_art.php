<?php include 'db.php';
include 'header.php';
if (!isset($_SESSION['role']) == 'admin') {
    header("Location: login.php");
}

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE art_items SET status='approved' WHERE art_id=$id");
} elseif (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->query("UPDATE art_items SET status='rejected' WHERE art_id=$id");
}

$result = $conn->query("SELECT * FROM art_items WHERE status='pending'");
?>


<div class="container mt-5">
    <h2 class="text-center">Pending Art Approvals</h2>
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
                <td><?php echo $row['name']; ?></td>
                <td><img src="uploads/<?php echo $row['image']; ?>" width="100"></td>
                <td>
                    <a href="?approve=<?php echo $row['art_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject=<?php echo $row['art_id']; ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>