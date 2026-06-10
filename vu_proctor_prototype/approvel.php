<?php
include 'navbar.php';

include 'db.php';
if (!isset($_SESSION['user_id']) && !isset($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit;
}

// Approve action
if (isset($_GET['approve'])) {
    $uid = intval($_GET['approve']);
    $sql = "UPDATE users SET approved=1 WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $msg = "User approved successfully!";
}

// Fetch pending users
$sql = "SELECT id, full_name, role, employee_id, email, cnic FROM users WHERE approved=0";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Pending User Approvals</h2>
    <?php if (isset($msg)) echo "<p class='msg success'>$msg</p>"; ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Employee ID</th>
            <th>Email</th>
            <th>CNIC</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['full_name'] ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['employee_id'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['cnic'] ?></td>
            <td><a class="btn" href="approvel.php?approve=<?= $row['id'] ?>">Approve</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>

</html>