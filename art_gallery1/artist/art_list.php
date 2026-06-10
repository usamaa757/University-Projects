<?php include '../db.php'; ?>
<?php include 'header.php'; ?>

<div class="container shadow rounded border mt-5">
    <h3 class="text-center mb-4">Art Inventory</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Art Image</th>
                    <th>Art Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM arts WHERE artist_id = '{$_SESSION['user_id']}'");
                ?>

                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Art Image"
                            style="height: 80px; width: auto; border-radius: 8px; object-fit: cover;">
                        <?php else: ?>
                        <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($row['art_name']) ?></td>
                    <td>Rs. <?= number_format($row['price']) ?></td>
                    <td>
                        <span
                            class="badge 
                <?= $row['status'] == 'approved' ? 'bg-success' : ($row['status'] == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>

                    <td>
                        <a href="edit_art.php?art_id=<?= $row['art_id'] ?>" class="btn btn-sm">Edit</a>
                        <a href="delete_art.php?art_id=<?= $row['art_id'] ?>" onclick="return confirm('Are you sure?')"
                            class="btn btn-sm">Delete</a>
                    </td>

                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No art pieces found.</td>
                </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
</body>

</html>